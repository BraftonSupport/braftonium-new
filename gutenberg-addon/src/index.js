const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment, useState, useEffect } = wp.element;
const { InspectorAdvancedControls }	= wp.blockEditor;
const { createHigherOrderComponent } = wp.compose;
const  apiFetch  = wp.apiFetch;
import Select from 'react-select';

/**
 * Blocks that never get microstyles.
 */
const EXCLUDED_BLOCKS = [ 'gravityforms/form' ];

/**
 * Add the microstyles attribute.
 *
 * Only the user's selection is persisted. The available class list is fetched
 * on demand and kept in memory - storing it in attributes bloated the saved
 * markup and forced an attribute write on every block render.
 *
 * @param {Object} settings Settings for the block.
 *
 * @return {Object} settings Modified settings.
 */
function addAttributes( settings ) {

	//check if object exists for old Gutenberg version compatibility
	if( typeof settings.attributes !== 'undefined' && ! EXCLUDED_BLOCKS.includes( settings.name ) ){

		settings.attributes = Object.assign( settings.attributes, {
			braftoniumClasses:{
				type: 'array',
				default: [],
			}
		});

	}

	return settings;
}

/**
 * The class list only depends on the block type, so it is cached for the
 * lifetime of the editor and shared by every block of that type.
 */
const classListCache = new Map();
const classListRequests = new Map();

function fetchClassList( blockType ){
	if( classListCache.has( blockType ) ){
		return Promise.resolve( classListCache.get( blockType ) );
	}

	if( ! classListRequests.has( blockType ) ){
		const request = apiFetch(
			{
				method: 'post',
				path: '/braftonium/v1/braftonium-class-list',
				data: {
					blockType
				}
			}
		).then( ( data ) => {
			const list = Array.isArray( data ) ? data : [];
			classListCache.set( blockType, list );
			return list;
		} ).catch( () => {
			// Don't cache failures, so selecting the block again retries.
			return [];
		} ).finally( () => {
			classListRequests.delete( blockType );
		} );

		classListRequests.set( blockType, request );
	}

	return classListRequests.get( blockType );
}

/**
 * Load the microstyles available to a block type.
 *
 * @param {string}  blockType Block name.
 * @param {boolean} enabled   Whether the list is needed yet.
 *
 * @return {{availableClasses: Array, isLoading: boolean}} Class list state.
 */
function useClassList( blockType, enabled ){
	const [ state, setState ] = useState( () => ({
		availableClasses: classListCache.get( blockType ) || [],
		isLoading: enabled && ! classListCache.has( blockType ),
	}) );

	useEffect( () => {
		if( ! enabled || EXCLUDED_BLOCKS.includes( blockType ) ){
			return;
		}

		if( classListCache.has( blockType ) ){
			setState( {
				availableClasses: classListCache.get( blockType ),
				isLoading: false,
			} );
			return;
		}

		let cancelled = false;

		setState( { availableClasses: [], isLoading: true } );

		fetchClassList( blockType ).then( ( list ) => {
			if( cancelled ){
				return;
			}
			setState( { availableClasses: list, isLoading: false } );
		} );

		return () => {
			cancelled = true;
		};
	}, [ blockType, enabled ] );

	return state;
}

addFilter(
	'blocks.registerBlockType',
	'editorskit/custom-attributes',
	addAttributes
);

/**
 * Add the microstyles control to the Advanced Block Panel.
 *
 * @param {function} BlockEdit Block edit component.
 *
 * @return {function} BlockEdit Modified block edit component.
 */
const withAdvancedControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const {
			attributes,
			setAttributes,
			isSelected,
			name
		} = props;

		const isSupported = typeof attributes?.braftoniumClasses !== 'undefined'
			&& ! EXCLUDED_BLOCKS.includes( name );

		const {
			availableClasses,
			isLoading
		} = useClassList( name, isSelected && isSupported );

		function handleClassSelection( newClasses ){
			const selected = newClasses || [];
			const selectedValues = Array.from( selected, x => x.value );
			const optionValues = availableClasses.map( ( item ) => item.value );

			// Keep any hand written classes, drop the microstyles we manage.
			const existing = ( attributes.className || '' )
				.split( ' ' )
				.filter( ( item ) => item && ! optionValues.includes( item ) );

			setAttributes( {
				braftoniumClasses: selected,
				className: existing.concat( selectedValues ).join( ' ' ),
			} );
		}

		// Always keep the same tree shape, otherwise BlockEdit remounts every
		// time the block is selected or deselected.
		return (

			<Fragment>
				<BlockEdit { ...props } />

				{ isSelected && isSupported &&
					<InspectorAdvancedControls>
						{ isLoading && <div>Currently Loading MicroStyles....</div> }
						{ ! isLoading && availableClasses.length == 0 &&
							<div>There are no MicroStyles for this block.</div>
						}
						{ ! isLoading && availableClasses.length > 0 &&
							<div className="special">
								<label>Braftonium Microstyles</label>
								<Select
										size=""
										help={__('These are micro styles for your theme. They are distint from block styles in that these will contain a more focused style option. Hold Ctrl and click the classes you wish to add or remove.')}
										isMulti={true}
										label="Braftonium MicroStyles"
										value={ attributes.braftoniumClasses }
										options={availableClasses}
										onChange={handleClassSelection}
									/>
									<span>These are micro styles for your theme. They are distint from block styles in that these will contain a more focused style option. Hold Ctrl and click the classes you wish to add or remove.</span>
									</div>
						}
					</InspectorAdvancedControls>
				}

			</Fragment>
		);
	}
}, 'withBraftoniumMicrostyles' );

addFilter(
	'editor.BlockEdit',
	'editorskit/custom-advanced-control',
	withAdvancedControls
);
function applyExtraClass( extraProps, blockType, attributes ) {

	const { braftoniumClasses } = attributes;
	// console.log("try to apply");
	//check if attribute exists for old Gutenberg version compatibility
	//add class only when visibleOnMobile = false
	//add allowedBlocks restriction

	// if ( typeof braftoniumClasses !== 'undefined' && braftoniumClasses.length > 0) {

	// 	extraProps.className = braftoniumClasses.join(" ");
	// }

	return extraProps;
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'editorskit/applyExtraClass',
	applyExtraClass
);
