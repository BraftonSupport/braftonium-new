/**
 * Editor script: braftonium/slide  (a card inside braftonium/slider)
 *
 * - Holds a free InnerBlocks area (paragraph by default).
 * - "Background Color" sets the card background (rgba, alpha enabled).
 *
 * IMPORTANT (inner block saving): `save` returns InnerBlocks.Content so the
 * slide's inner blocks are serialized into post content and reach render.php as
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
    var InnerBlocks = blockEditor.InnerBlocks;
    var useBlockProps = blockEditor.useBlockProps;

    var PanelBody = components.PanelBody;
    var ColorPicker = components.ColorPicker;

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

    blocks.registerBlockType( 'braftonium/slide', {
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var bgColor = attributes.bgColor || { r: 0, g: 0, b: 0, a: 0 };
            var hasBgColor = ( bgColor.a == null ? 0 : bgColor.a ) > 0;

            var blockProps = useBlockProps( { className: 'braftonium-slide' } );

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __( 'Background Color', 'braftonium' ), initialOpen: true },
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
                    el(
                        'div',
                        {
                            className: 'braftonium-slide__inner',
                            style: hasBgColor ? { backgroundColor: rgbaString( bgColor ) } : undefined,
                        },
                        el( InnerBlocks, {
                            template: [ [ 'core/paragraph', { placeholder: __( 'Add slide content...', 'braftonium' ) } ] ],
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
