const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment } = wp.element;
const { InspectorAdvancedControls } = wp.blockEditor;
const { useState, useEffect } = wp.element;
const apiFetch = wp.apiFetch;
import Select from 'react-select';

/**
 * Add the persisted Braftonium attribute to (almost) every block.
 *
 * Only `braftoniumClasses` (the chosen microstyles) is persisted. The available
 * options + loading state are transient editor UI and live in component state —
 * persisting them used to leak `classesFetched`/`loading` into saved post content.
 *
 * @param {Object} settings Settings for the block.
 * @return {Object} Modified settings.
 */
function addAttributes( settings ) {
	// Guard for old-Gutenberg compatibility; skip Gravity Forms blocks.
	if ( typeof settings.attributes !== 'undefined' && settings.name !== 'gravityforms/form' ) {
		settings.attributes = Object.assign( settings.attributes, {
			braftoniumClasses: {
				type: 'array',
				default: [],
			},
		} );
	}
	return settings;
}

addFilter(
	'blocks.registerBlockType',
	'editorskit/custom-attributes',
	addAttributes
);

/**
 * Fetch the microstyle class list for a block type. Returns the data (no side
 * effects) so the caller owns state.
 *
 * @param {string} blockType Block name.
 * @return {Promise<Array>} The class options.
 */
async function getClassList( blockType ) {
	if ( ! blockType || blockType === 'gravityforms/form' ) {
		return [];
	}
	const data = await apiFetch( {
		method: 'post',
		path: '/braftonium/v1/braftonium-class-list',
		data: { blockType },
	} );
	return Array.isArray( data ) ? data : [];
}

/**
 * Add the "Braftonium MicroStyles" control to the Advanced panel of every block.
 *
 * @param {Function} BlockEdit Block edit component.
 * @return {Function} Wrapped component.
 */
const withAdvancedControls = ( BlockEdit ) => {
	return ( props ) => {
		const { attributes, setAttributes, isSelected, name } = props;
		const { braftoniumClasses = [] } = attributes;

		// Transient UI state — NOT persisted block attributes.
		const [ availableClasses, setAvailableClasses ] = useState( [] );
		const [ loading, setLoading ] = useState( false );
		const [ fetched, setFetched ] = useState( false );

		// Side effect belongs in useEffect (unconditional hook). Fetch the class
		// list once the block is selected; reset when deselected.
		useEffect( () => {
			let cancelled = false;

			if ( isSelected && ! fetched ) {
				setLoading( true );
				getClassList( name )
					.then( ( data ) => {
						if ( cancelled ) {
							return;
						}
						setAvailableClasses( data );
						setLoading( false );
						setFetched( true );
					} )
					.catch( () => {
						if ( cancelled ) {
							return;
						}
						setLoading( false );
						setFetched( true );
					} );
			}

			if ( ! isSelected && fetched ) {
				setFetched( false );
			}

			return () => {
				cancelled = true;
			};
		}, [ isSelected, name, fetched ] );

		function handleClassSelection( newClasses ) {
			const classValues = Array.from( newClasses || [], ( item ) => item.value );
			const existing = ( attributes.className ? attributes.className : '' ).split( ' ' );
			const optionValues = availableClasses.map( ( item ) => item.value );
			// Keep any classes that aren't Braftonium options (manually added).
			const nonOptions = existing.filter( ( item ) => item && ! optionValues.includes( item ) );

			setAttributes( { braftoniumClasses: newClasses || [] } );
			setAttributes( { className: [ ...nonOptions, ...classValues ].join( ' ' ).trim() } );
		}

		if ( ! isSelected ) {
			return <BlockEdit { ...props } />;
		}

		if ( loading ) {
			return (
				<Fragment>
					<BlockEdit { ...props } />
					<InspectorAdvancedControls>
						<div>{ __( 'Currently loading MicroStyles…' ) }</div>
					</InspectorAdvancedControls>
				</Fragment>
			);
		}

		if ( fetched && availableClasses.length === 0 ) {
			return (
				<Fragment>
					<BlockEdit { ...props } />
					<InspectorAdvancedControls>
						<div>{ __( 'There are no MicroStyles for this block.' ) }</div>
					</InspectorAdvancedControls>
				</Fragment>
			);
		}

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<InspectorAdvancedControls>
					<div className="braftonium-microstyles">
						<label>{ __( 'Braftonium MicroStyles' ) }</label>
						<Select
							isMulti={ true }
							label={ __( 'Braftonium MicroStyles' ) }
							value={ braftoniumClasses }
							options={ availableClasses }
							onChange={ handleClassSelection }
						/>
						<span>
							{ __(
								'These are micro styles for your theme. They are distinct from block styles in that they contain a more focused style option. Hold Ctrl and click the classes you wish to add or remove.'
							) }
						</span>
					</div>
				</InspectorAdvancedControls>
			</Fragment>
		);
	};
};

addFilter(
	'editor.BlockEdit',
	'editorskit/custom-advanced-control',
	withAdvancedControls
);

/**
 * Reserved: apply extra classes at save time (currently a no-op — classes are
 * written to `className` directly in handleClassSelection).
 */
function applyExtraClass( extraProps ) {
	return extraProps;
}

addFilter(
	'blocks.getSaveContent.extraProps',
	'editorskit/applyExtraClass',
	applyExtraClass
);
