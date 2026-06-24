/**
 * Editor script: braftonium/cta
 *
 * Scaffolded from the Banner block. Same full-bleed background + content
 * overlay model, plus a buttons row in the starter template.
 *
 * IMPORTANT (inner block saving): `save` returns InnerBlocks.Content — the same
 * fix applied to the Banner block. Returning null drops the inner blocks from
 * the saved post content, which leaves render.php with an empty $content.
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

    var PanelBody = components.PanelBody;
    var Button = components.Button;
    var ColorPicker = components.ColorPicker;
    var RadioControl = components.RadioControl;
    var HStack = components.__experimentalHStack;
    var Text = components.__experimentalText;

    var metadata = {
        name: 'braftonium/cta',
        title: __( 'CTA', 'braftonium' ),
        category: 'braftonium',
        icon: 'megaphone',
    };

    // Normalise whatever ColorPicker hands back into a plain {r,g,b,a} object.
    function toRgba( value ) {
        if ( value && typeof value === 'object' ) {
            var o = value.rgb || value;
            return {
                r: +o.r || 0,
                g: +o.g || 0,
                b: +o.b || 0,
                a: o.a == null ? 1 : +o.a,
            };
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
            return {
                r: +p[ 0 ] || 0,
                g: +p[ 1 ] || 0,
                b: +p[ 2 ] || 0,
                a: p[ 3 ] == null ? 1 : +p[ 3 ],
            };
        }

        return { r: 0, g: 0, b: 0, a: 1 };
    }

    function rgbaString( c ) {
        return 'rgba(' + c.r + ', ' + c.g + ', ' + c.b + ', ' + ( c.a == null ? 1 : c.a ) + ')';
    }

    blocks.registerBlockType( metadata.name, {
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var backgroundImage = attributes.backgroundImage;
            var backgroundImageUrl = attributes.backgroundImageUrl;
            var backgroundImageAlt = attributes.backgroundImageAlt;
            var overlayColor = attributes.overlayColor;
            var alignContent = attributes.alignContent;

            var blockProps = useBlockProps( { className: 'braftonium-cta' } );

            function removeImage() {
                setAttributes( {
                    backgroundImage: 0,
                    backgroundImageUrl: '',
                    backgroundImageAlt: '',
                } );
            }

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
                        el(
                            'div',
                            { style: { marginBottom: '16px' } },
                            el( HStack, null, el( Text, null, __( 'Overlay Color', 'braftonium' ) ) ),
                            el( ColorPicker, {
                                color: rgbaString( overlayColor ),
                                onChange: function ( value ) {
                                    setAttributes( { overlayColor: toRgba( value ) } );
                                },
                                enableAlpha: true,
                            } )
                        ),
                        el( RadioControl, {
                            label: __( 'Content Alignment', 'braftonium' ),
                            selected: alignContent,
                            options: [
                                { label: __( 'Left', 'braftonium' ), value: 'left' },
                                { label: __( 'Center', 'braftonium' ), value: 'center' },
                                { label: __( 'Right', 'braftonium' ), value: 'right' },
                            ],
                            onChange: function ( value ) {
                                setAttributes( { alignContent: value } );
                            },
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
                    el( 'div', {
                        className: 'overlay',
                        style: { backgroundColor: rgbaString( overlayColor ) },
                    } ),
                    el(
                        'div',
                        { className: 'cta-content align-wrap-' + alignContent },
                        el( InnerBlocks, {
                            template: [
                                [ 'core/heading', { level: 2, placeholder: __( 'CTA Heading...', 'braftonium' ) } ],
                                [ 'core/paragraph', { placeholder: __( 'Add CTA description...', 'braftonium' ) } ],
                                [ 'core/buttons', {} ],
                            ],
                        } )
                    )
                )
            );
        },
        save: function () {
            return el( InnerBlocks.Content, null );
        },
    } );
} )();
