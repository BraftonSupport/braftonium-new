const { __ } = wp.i18n;
const { addFilter } = wp.hooks;
const { Fragment }	= wp.element;
const { InspectorAdvancedControls }	= wp.blockEditor;
const { createHigherOrderComponent } = wp.compose;
const { SelectControl } = wp.components;
const  apiFetch  = wp.apiFetch;
const {compose } = wp.compose;
const useSelect  = wp.data.useSelect;
const withSelect = wp.data.withSelect;
const { Component } = wp.element;
const {useEffect} = wp.element;
import Select from 'react-select';
/**
 * Add custom attribute for mobile visibility.
 *
 * @param {Object} settings Settings for the block.
 *
 * @return {Object} settings Modified settings.
 */
 function addAttributes( settings ) {
	
	//check if object exists for old Gutenberg version compatibility
	if( typeof settings.attributes !== 'undefined' ){
	
		settings.attributes = Object.assign( settings.attributes, {
			braftoniumClasses:{ 
				type: 'array',
				default: [],
			}
		});
		settings.attributes = Object.assign( settings.attributes, {
			availableClasses:{ 
				type: 'array',
				default: [],
			}
		});
		settings.attributes = Object.assign( settings.attributes, {
			classesFetched:{ 
				type: 'boolean',
				default: false,
			}
		});
		settings.attributes = Object.assign( settings.attributes, {
			loading:{ 
				type: 'boolean',
				default: false,
			}
		});
    
	}

	return settings;
}
async function getClassList(setAttributes, blockType){
	console.log(blockType);
	const data = await apiFetch(
		{
			method: 'post',
			path: '/braftonium/v1/braftonium-class-list',
			data: {
				blockType
			}
		}
	);
	console.log(data);
	setAttributes({availableClasses: data});
	setAttributes({loading: false});
	return data;
}
addFilter(
	'blocks.registerBlockType',
	'editorskit/custom-attributes',
	addAttributes
);
/**
 * Add mobile visibility controls on Advanced Block Panel.
 *
 * @param {function} BlockEdit Block edit component.
 *
 * @return {function} BlockEdit Modified block edit component.
 */
 const withAdvancedControls =  (BlockEdit ) => {
	// withSelect((select, ownProps)=>{
	// 	console.log('select',select,'props',ownProps);
		
	// 	 })
	return (props)=>{
		// var classOptions = [{ label: 'Full Width Row', value: 'full-width' }];
		const {
			attributes,
			setAttributes,
			isSelected,
			classList
		} = props;
		console.log(props);
		const {
			braftoniumClasses,
			availableClasses,
			classesFetched,
			loading
		} = attributes;
		if(isSelected && !classesFetched){
			setAttributes({classesFetched: true});
			setAttributes({loading: true});
			useSelect((select)=>{
				getClassList(setAttributes, props.name);
			})
			
		}
		if(!isSelected){
			setAttributes({classesFetched: false});
			setAttributes({loading: false});
		}
		function handleClassSelection(newClasses){
			
			var ClassValues = Array.from(newClasses, x=>x.value);
			
			// console.log(newSize, attributes);
			var classes = attributes.className? attributes.className : "";
			var classes = classes.split(" ");
			var classOptionValues = availableClasses.map(function(item){
				return item.value;
			})
			var nonOptions = classes.filter(item=>{
				if(classOptionValues.includes(item)){
					return false;
				}
				return true;
			})
			setAttributes( {braftoniumClasses: newClasses});
			setAttributes( {  className:  nonOptions.join(" ")+" "+ClassValues.join(" ") } )
		}
		// console.log('attribute classes',availableClasses);
		// classOptions = availableClasses;
		// console.log('my data',classOptions);
		// useEffect(async ()=>{
		// 	if(isSelected){
		// 		console.log('help');
		// 	}
		// });
		if(isSelected && loading){
			return (
				<Fragment>
					<BlockEdit {...props} />
					<InspectorAdvancedControls>
						<div>Currently Loading MicroStyles....</div>
					</InspectorAdvancedControls>
				</Fragment>
			)
		}
		if(isSelected && !loading && classesFetched && availableClasses.length == 0){
			return (
				<Fragment>
					<BlockEdit {...props} />
					<InspectorAdvancedControls>
						<div>There are no MicroStyles for this block.</div>
					</InspectorAdvancedControls>
				</Fragment>
			)
		}
		return (

			<Fragment>
				<BlockEdit {...props} />

				{ isSelected && 
					<InspectorAdvancedControls>
						<div class="special">
							<lable>Braftonium Microstyles</lable>
							<Select 
									size=""
									help={__('These are micro styles for your theme. They are distint from block styles in that these will contain a more focused style option. Hold Ctrl and click the classes you wish to add or remove.')}
									isMulti={true}
									label="Braftonium MicroStyles"
									value={ braftoniumClasses }
									options={availableClasses}
									onChange={handleClassSelection}
								/>
								<span>These are micro styles for your theme. They are distint from block styles in that these will contain a more focused style option. Hold Ctrl and click the classes you wish to add or remove.</span>
								</div>
						{/* <ToggleControl
							label={ __( 'Mobile Devices Visibity' ) }
							checked={ !! visibleOnMobile }
							onChange={ () => setAttributes( {  visibleOnMobile: ! visibleOnMobile } ) }
							help={ !! visibleOnMobile ? __( 'Showing on mobile devices.' ) : __( 'Hidden on mobile devices.' ) }
						/> */}
					</InspectorAdvancedControls>
				}

			</Fragment>
		);
	}
}
const applyWithSelect = withSelect((select, ownProps)=>{
	console.log('s', select,'p',ownProps);
	return {
		classList: getClassList()
	}
 })(withAdvancedControls);
// export default compose(
// 	applyWithSelect
// )(withAdvancedControls);

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