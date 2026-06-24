/**
 * Editor script: braftonium/custom-row
 *
 * A horizontal row container with an optional, positionable background image.
 * Content is constrained to the banner/cta content width (see custom-row.scss).
 *
 * IMPORTANT (inner block saving): `save` returns InnerBlocks.Content so the
 * inner blocks are serialized into post content and reach render.php as
 * $content. Returning null dropped them — the previous bug.
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

    blocks.registerBlockType( 'braftonium/custom-row', {
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var backgroundImage = attributes.backgroundImage;
            var backgroundImageUrl = attributes.backgroundImageUrl;
            var backgroundImageAlt = attributes.backgroundImageAlt;
            var backgroundPosition = attributes.backgroundPosition || {};
            var bgColor = attributes.bgColor || { r: 0, g: 0, b: 0, a: 0 };

            var hasBgColor = ( bgColor.a == null ? 0 : bgColor.a ) > 0;

            var blockProps = useBlockProps( {
                className: 'braftonium-custom-row',
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

            // useInnerBlocksProps makes the inner blocks DIRECT children of the
            // flex row so the layout renders correctly in the editor.
            var innerBlocksProps = useInnerBlocksProps(
                { className: 'custom-row-content' },
                {}
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
