/**
 * Editor script: braftonium/custom-list
 *
 * Container for braftonium/custom-list-item children, laid out as a responsive
 * grid. Per-breakpoint (Desktop / Tablet / Mobile) controls for container
 * width, columns per row and gap, plus a background image and background color.
 *
 * IMPORTANT (inner block saving): `save` returns InnerBlocks.Content so the
 * child items are serialized into post content and reach render.php as $content.
 */
( function () {
    'use strict';

    var blocks = window.wp.blocks;
    var React = window.React;
    var i18n = window.wp.i18n;
    var blockEditor = window.wp.blockEditor;
    var components = window.wp.components;

    var __ = i18n.__;
    var el = React.createElement;
    var Fragment = React.Fragment;

    var InspectorControls = blockEditor.InspectorControls;
    var MediaUpload = blockEditor.MediaUpload;
    var MediaUploadCheck = blockEditor.MediaUploadCheck;
    var InnerBlocks = blockEditor.InnerBlocks;
    var useBlockProps = blockEditor.useBlockProps;
    var useInnerBlocksProps = blockEditor.useInnerBlocksProps;

    var PanelBody = components.PanelBody;
    var Button = components.Button;
    var ColorPicker = components.ColorPicker;
    var RangeControl = components.RangeControl;
    var HStack = components.__experimentalHStack;
    var Text = components.__experimentalText;

    var LAYOUT_DEFAULTS = {
        desktop: { width: 1200, columns: 3, gap: 24 },
        tablet: { width: 0, columns: 2, gap: 20 },
        mobile: { width: 0, columns: 1, gap: 16 },
    };

    var metadata = {
        name: 'braftonium/custom-list',
        title: __( 'Custom List', 'braftonium' ),
        category: 'braftonium',
        icon: 'list-view',
    };

    function toRgba( value ) {
        if ( value && typeof value === 'object' ) {
            var o = value.rgb || value;
            return { r: +o.r || 0, g: +o.g || 0, b: +o.b || 0, a: o.a == null ? 1 : +o.a };
        }
        var s = String( value ).trim();
        var m;
        if ( ( m = s.match( /^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})?$/i ) ) ) {
            return {
                r: parseInt( m[ 1 ], 16 ),
                g: parseInt( m[ 2 ], 16 ),
                b: parseInt( m[ 3 ], 16 ),
                a: m[ 4 ] == null ? 1 : +( parseInt( m[ 4 ], 16 ) / 255 ).toFixed( 3 ),
            };
        }
        if ( ( m = s.match( /^#?([0-9a-f])([0-9a-f])([0-9a-f])$/i ) ) ) {
            return {
                r: parseInt( m[ 1 ] + m[ 1 ], 16 ),
                g: parseInt( m[ 2 ] + m[ 2 ], 16 ),
                b: parseInt( m[ 3 ] + m[ 3 ], 16 ),
                a: 1,
            };
        }
        if ( ( m = s.match( /rgba?\(([^)]+)\)/i ) ) ) {
            var p = m[ 1 ].split( ',' );
            return { r: +p[ 0 ] || 0, g: +p[ 1 ] || 0, b: +p[ 2 ] || 0, a: p[ 3 ] == null ? 1 : +p[ 3 ] };
        }
        return { r: 0, g: 0, b: 0, a: 1 };
    }

    function rgbaString( c ) {
        c = c || {};
        return 'rgba(' + ( +c.r || 0 ) + ', ' + ( +c.g || 0 ) + ', ' + ( +c.b || 0 ) + ', ' + ( c.a == null ? 1 : c.a ) + ')';
    }

    // A PanelBody with width / columns / gap controls for one breakpoint.
    function layoutPanel( key, title, layout, setLayout ) {
        var bp = layout[ key ] || LAYOUT_DEFAULTS[ key ];

        function update( field, value ) {
            var next = {};
            next[ key ] = Object.assign( {}, bp );
            next[ key ][ field ] = value;
            setLayout( Object.assign( {}, layout, next ) );
        }

        return el(
            PanelBody,
            { title: title, initialOpen: false },
            el( RangeControl, {
                label: __( 'Container width (px, 0 = full width)', 'braftonium' ),
                value: bp.width,
                onChange: function ( v ) {
                    update( 'width', v == null ? 0 : v );
                },
                min: 0,
                max: 1920,
                step: 10,
            } ),
            el( RangeControl, {
                label: __( 'Columns per row', 'braftonium' ),
                value: bp.columns,
                onChange: function ( v ) {
                    update( 'columns', Math.max( 1, v || 1 ) );
                },
                min: 1,
                max: 6,
            } ),
            el( RangeControl, {
                label: __( 'Gap (px)', 'braftonium' ),
                value: bp.gap,
                onChange: function ( v ) {
                    update( 'gap', v == null ? 0 : v );
                },
                min: 0,
                max: 100,
            } )
        );
    }

    blocks.registerBlockType( metadata.name, {
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var backgroundImage = attributes.backgroundImage;
            var backgroundImageUrl = attributes.backgroundImageUrl;
            var backgroundImageAlt = attributes.backgroundImageAlt;
            var backgroundPosition = attributes.backgroundPosition || {};
            var bgColor = attributes.bgColor || { r: 0, g: 0, b: 0, a: 0 };
            var layout = Object.assign( {}, LAYOUT_DEFAULTS, attributes.layout || {} );

            var hasBgColor = ( bgColor.a == null ? 0 : bgColor.a ) > 0;

            var blockProps = useBlockProps( {
                className: 'braftonium-custom-list',
                style: hasBgColor ? { backgroundColor: rgbaString( bgColor ) } : undefined,
            } );

            function removeImage() {
                setAttributes( { backgroundImage: 0, backgroundImageUrl: '', backgroundImageAlt: '' } );
            }

            function setPosition( field, value ) {
                var next = Object.assign( {}, backgroundPosition );
                next[ field ] = value;
                setAttributes( { backgroundPosition: next } );
            }

            function setLayout( next ) {
                setAttributes( { layout: next } );
            }

            // Editor preview uses the Desktop grid (media queries are applied on
            // the front end via render.php's scoped <style>). Width constraint
            // matches the banner/cta rule: target width capped at 90vw.
            var desktop = layout.desktop || LAYOUT_DEFAULTS.desktop;
            var contentStyle = {
                display: 'grid',
                gridTemplateColumns: 'repeat(' + Math.max( 1, desktop.columns || 1 ) + ', minmax(0, 1fr))',
                gap: ( desktop.gap || 0 ) + 'px',
                width: desktop.width > 0 ? desktop.width + 'px' : '100%',
                maxWidth: desktop.width > 0 ? '100%' : 'none',
                marginInline: desktop.width > 0 ? 'auto' : '0',
            };

            // useInnerBlocksProps makes the items DIRECT children of the grid
            // container so columns render correctly in the editor (plain
            // InnerBlocks inserts a wrapper, which collapsed the grid to 1 col).
            var innerBlocksProps = useInnerBlocksProps(
                { className: 'custom-list-content', style: contentStyle },
                {
                    allowedBlocks: [ 'braftonium/custom-list-item' ],
                    template: [
                        [ 'braftonium/custom-list-item', {} ],
                        [ 'braftonium/custom-list-item', {} ],
                        [ 'braftonium/custom-list-item', {} ],
                    ],
                }
            );

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __( 'Background Settings', 'braftonium' ), initialOpen: true },
                        el(
                            MediaUploadCheck,
                            null,
                            el( MediaUpload, {
                                onSelect: function ( media ) {
                                    setAttributes( {
                                        backgroundImage: media.id,
                                        backgroundImageUrl: media.url,
                                        backgroundImageAlt: media.alt || '',
                                    } );
                                },
                                allowedTypes: [ 'image' ],
                                value: backgroundImage,
                                render: function ( obj ) {
                                    var open = obj.open;
                                    return el(
                                        'div',
                                        { style: { marginBottom: '16px' } },
                                        el( HStack, null, el( Text, null, __( 'Background Image', 'braftonium' ) ) ),
                                        backgroundImageUrl
                                            ? el(
                                                  Fragment,
                                                  null,
                                                  el( 'img', {
                                                      src: backgroundImageUrl,
                                                      alt: backgroundImageAlt,
                                                      style: { width: '100%', height: 'auto', marginTop: '8px' },
                                                  } ),
                                                  el(
                                                      HStack,
                                                      { style: { marginTop: '8px' } },
                                                      el( Button, { onClick: open, variant: 'secondary' }, __( 'Replace Image', 'braftonium' ) ),
                                                      el( Button, { onClick: removeImage, variant: 'link', isDestructive: true }, __( 'Remove', 'braftonium' ) )
                                                  )
                                              )
                                            : el( Button, { onClick: open, variant: 'secondary' }, __( 'Select Image', 'braftonium' ) )
                                    );
                                },
                            } )
                        ),
                        backgroundImageUrl &&
                            el(
                                Fragment,
                                null,
                                el( RangeControl, {
                                    label: __( 'Top Position', 'braftonium' ),
                                    value: backgroundPosition.top,
                                    onChange: function ( v ) {
                                        setPosition( 'top', v );
                                    },
                                    min: -500,
                                    max: 500,
                                } ),
                                el( RangeControl, {
                                    label: __( 'Left Position', 'braftonium' ),
                                    value: backgroundPosition.left,
                                    onChange: function ( v ) {
                                        setPosition( 'left', v );
                                    },
                                    min: -500,
                                    max: 500,
                                } )
                            ),
                        el( 'div', { style: { marginBottom: '8px', marginTop: '8px' } }, el( Text, null, __( 'Background Color', 'braftonium' ) ) ),
                        el( ColorPicker, {
                            color: rgbaString( bgColor ),
                            onChange: function ( value ) {
                                setAttributes( { bgColor: toRgba( value ) } );
                            },
                            enableAlpha: true,
                        } )
                    ),
                    el(
                        PanelBody,
                        { title: __( 'Layout', 'braftonium' ), initialOpen: true },
                        el( Text, { style: { display: 'block', marginBottom: '8px' } }, __( 'Set columns, gap and container width per screen size.', 'braftonium' ) ),
                        layoutPanel( 'desktop', __( 'Desktop (≥1024px)', 'braftonium' ), layout, setLayout ),
                        layoutPanel( 'tablet', __( 'Tablet (≥768px)', 'braftonium' ), layout, setLayout ),
                        layoutPanel( 'mobile', __( 'Mobile (<768px)', 'braftonium' ), layout, setLayout )
                    )
                ),
                el(
                    'div',
                    blockProps,
                    backgroundImageUrl &&
                        el( 'img', {
                            src: backgroundImageUrl,
                            alt: backgroundImageAlt,
                            className: 'background-image',
                        } ),
                    el( 'div', innerBlocksProps )
                )
            );
        },
        save: function () {
            return el( InnerBlocks.Content, null );
        },
    } );
} )();
