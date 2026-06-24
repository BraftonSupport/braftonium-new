/**
 * Editor script: braftonium/custom-list-item
 *
 * A single item inside braftonium/custom-list. Holds a free InnerBlocks area
 * (image + heading + paragraph by default).
 *
 * IMPORTANT (inner block saving): `save` returns InnerBlocks.Content so the
 * item's inner blocks are serialized into post content and reach render.php as
 * $content. Returning null dropped them — the previous bug.
 */
( function () {
    'use strict';

    var blocks = window.wp.blocks;
    var React = window.React;
    var i18n = window.wp.i18n;
    var blockEditor = window.wp.blockEditor;

    var __ = i18n.__;
    var el = React.createElement;

    var InnerBlocks = blockEditor.InnerBlocks;
    var useBlockProps = blockEditor.useBlockProps;

    blocks.registerBlockType( 'braftonium/custom-list-item', {
        edit: function () {
            var blockProps = useBlockProps( { className: 'braftonium-custom-list-item' } );
            return el(
                'div',
                blockProps,
                el( InnerBlocks, {
                    template: [
                        [ 'core/image', {} ],
                        [ 'core/heading', { level: 4, placeholder: __( 'Heading', 'braftonium' ) } ],
                        [ 'core/paragraph', { placeholder: __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'braftonium' ) } ],
                    ],
                } )
            );
        },
        save: function () {
            return el( InnerBlocks.Content, null );
        },
    } );
} )();
