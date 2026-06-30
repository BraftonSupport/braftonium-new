import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	SelectControl,
	RangeControl,
} from '@wordpress/components';
import './editor.scss';

// Default panel content: a real, editable heading. Editors can add anything else.
const TEMPLATE = [ [ 'core/heading', { level: 3, placeholder: __( 'Dropdown heading', 'braftonium' ) } ] ];

export default function Edit( { attributes, setAttributes } ) {
	const { label, url, linkTarget, layout, panelWidth, columns, rows } = attributes;

	const blockProps = useBlockProps( { className: 'braftonium-nav-dropdown is-editing' } );

	const panelStyle = {
		'--panel-width': `${ panelWidth }px`,
		'--panel-cols': columns,
		'--panel-rows': rows,
	};
	const panelClassName =
		`braftonium-nav-dropdown__panel braftonium-nav-dropdown__panel--${ layout }` +
		( layout === 'grid' && rows > 0 ? ' has-rows' : '' );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: panelClassName, style: panelStyle },
		{ template: TEMPLATE, templateLock: false }
	);

	const isGridLike = layout === 'grid' || layout === 'full';

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Dropdown link', 'braftonium' ) }>
					<TextControl
						label={ __( 'Label', 'braftonium' ) }
						value={ label }
						onChange={ ( value ) => setAttributes( { label: value } ) }
						help={ __( 'The top-level menu label.', 'braftonium' ) }
					/>
					<TextControl
						label={ __( 'Link URL', 'braftonium' ) }
						value={ url }
						onChange={ ( value ) => setAttributes( { url: value } ) }
						placeholder="/products/"
					/>
					<ToggleControl
						label={ __( 'Open in new tab', 'braftonium' ) }
						checked={ linkTarget === '_blank' }
						onChange={ ( value ) => setAttributes( { linkTarget: value ? '_blank' : '' } ) }
					/>
				</PanelBody>

				<PanelBody title={ __( 'Dropdown layout', 'braftonium' ) }>
					<SelectControl
						label={ __( 'Layout', 'braftonium' ) }
						value={ layout }
						options={ [
							{ label: __( 'Dropdown (1 column)', 'braftonium' ), value: 'dropdown' },
							{ label: __( 'Fixed width', 'braftonium' ), value: 'fixed' },
							{ label: __( 'Grid', 'braftonium' ), value: 'grid' },
							{ label: __( 'Full width (mega menu)', 'braftonium' ), value: 'full' },
						] }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>

					{ layout === 'fixed' && (
						<RangeControl
							label={ __( 'Width (px)', 'braftonium' ) }
							value={ panelWidth }
							min={ 200 }
							max={ 900 }
							step={ 10 }
							onChange={ ( value ) => setAttributes( { panelWidth: value } ) }
						/>
					) }

					{ isGridLike && (
						<RangeControl
							label={ __( 'Columns', 'braftonium' ) }
							value={ columns }
							min={ 1 }
							max={ 6 }
							onChange={ ( value ) => setAttributes( { columns: value } ) }
						/>
					) }

					{ layout === 'grid' && (
						<RangeControl
							label={ __( 'Rows (0 = auto)', 'braftonium' ) }
							value={ rows }
							min={ 0 }
							max={ 6 }
							onChange={ ( value ) => setAttributes( { rows: value } ) }
							help={ __( 'Set rows to fill columns top-to-bottom (column-major).', 'braftonium' ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<li { ...blockProps }>
				<RichText
					tagName="span"
					className="wp-block-navigation-item__label"
					value={ label }
					allowedFormats={ [] }
					onChange={ ( value ) => setAttributes( { label: value } ) }
					placeholder={ __( 'Menu label', 'braftonium' ) }
				/>
				<div { ...innerBlocksProps } />
			</li>
		</>
	);
}
