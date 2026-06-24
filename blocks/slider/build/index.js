/**
 * Editor script: braftonium/slider  (Slick carousel)
 *
 * - Inner blocks are braftonium/slide; save returns InnerBlocks.Content.
 * - "Preview" toggle initialises a live Slick carousel in the editor (Slick is
 *   enqueued for the editor in blocks.php). Toggling off destroys it again.
 * - Presentation options are per breakpoint: Desktop / Tablet / Cell, each with
 *   slides-to-show, slides-to-scroll and a screen-width breakpoint.
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
    var useRef = React.useRef;
    var useEffect = React.useEffect;

    var InspectorControls = blockEditor.InspectorControls;
    var MediaUpload = blockEditor.MediaUpload;
    var MediaUploadCheck = blockEditor.MediaUploadCheck;
    var InnerBlocks = blockEditor.InnerBlocks;
    var useBlockProps = blockEditor.useBlockProps;
    var useInnerBlocksProps = blockEditor.useInnerBlocksProps;

    var PanelBody = components.PanelBody;
    var Button = components.Button;
    var ColorPicker = components.ColorPicker;
    var RadioControl = components.RadioControl;
    var RangeControl = components.RangeControl;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var Text = components.__experimentalText;

    var RESP_DEFAULTS = {
        desktop: { breakpoint: 1920, slidesToShow: 3, slidesToScroll: 1 },
        tablet: { breakpoint: 1024, slidesToShow: 2, slidesToScroll: 1 },
        cell: { breakpoint: 600, slidesToShow: 1, slidesToScroll: 1 },
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

    function tierSettings( t ) {
        return {
            slidesToShow: Math.max( 1, parseInt( t.slidesToShow, 10 ) || 1 ),
            slidesToScroll: Math.max( 1, parseInt( t.slidesToScroll, 10 ) || 1 ),
        };
    }

    // Build the Slick options object — mirrors render.php so preview matches live.
    function buildSlickConfig( attrs ) {
        var resp = Object.assign( {}, RESP_DEFAULTS, attrs.responsive || {} );
        var desktop = Object.assign( {}, RESP_DEFAULTS.desktop, resp.desktop );
        var tablet = Object.assign( {}, RESP_DEFAULTS.tablet, resp.tablet );
        var cell = Object.assign( {}, RESP_DEFAULTS.cell, resp.cell );

        var cfg = {
            dots: ( attrs.dotsVisibility || 'visible' ) === 'visible',
            arrows: ( attrs.arrowsVisibility || 'visible' ) === 'visible',
            autoplay: true,
            autoplaySpeed: attrs.playbackAutoplaySpeed || 3000,
            speed: attrs.playbackSlideSpeed || 300,
            infinite: !! attrs.presentationInfinite,
            slidesToShow: tierSettings( desktop ).slidesToShow,
            slidesToScroll: tierSettings( desktop ).slidesToScroll,
            responsive: [
                { breakpoint: parseInt( desktop.breakpoint, 10 ) || 1920, settings: tierSettings( desktop ) },
                { breakpoint: parseInt( tablet.breakpoint, 10 ) || 1024, settings: tierSettings( tablet ) },
                { breakpoint: parseInt( cell.breakpoint, 10 ) || 600, settings: tierSettings( cell ) },
            ],
        };

        if ( cfg.arrows ) {
            if ( attrs.arrowsType === 'image' ) {
                if ( attrs.arrowsLeftImage ) {
                    cfg.prevArrow = '<button type="button" class="slick-prev"><img src="' + attrs.arrowsLeftImage + '" alt="" /></button>';
                }
                if ( attrs.arrowsRightImage ) {
                    cfg.nextArrow = '<button type="button" class="slick-next"><img src="' + attrs.arrowsRightImage + '" alt="" /></button>';
                }
            } else {
                cfg.prevArrow = '<button type="button" class="slick-prev">' + ( attrs.arrowsLeftText || 'Previous' ) + '</button>';
                cfg.nextArrow = '<button type="button" class="slick-next">' + ( attrs.arrowsRightText || 'Next' ) + '</button>';
            }
        }

        return cfg;
    }

    // A set of show / scroll / breakpoint controls for one responsive tier.
    function tierControls( key, title, responsive, setResponsive ) {
        var tier = Object.assign( {}, RESP_DEFAULTS[ key ], responsive[ key ] || {} );

        function update( field, value ) {
            var next = Object.assign( {}, responsive );
            next[ key ] = Object.assign( {}, tier );
            next[ key ][ field ] = value;
            setResponsive( next );
        }

        return el(
            'div',
            { style: { marginBottom: '16px' } },
            el( Text, { style: { display: 'block', fontWeight: 600, marginBottom: '4px' } }, title ),
            el( RangeControl, {
                label: __( 'Slides to Show', 'braftonium' ),
                value: tier.slidesToShow,
                onChange: function ( v ) {
                    update( 'slidesToShow', Math.max( 1, v || 1 ) );
                },
                min: 1,
                max: 6,
            } ),
            el( RangeControl, {
                label: __( 'Slides to Scroll', 'braftonium' ),
                value: tier.slidesToScroll,
                onChange: function ( v ) {
                    update( 'slidesToScroll', Math.max( 1, v || 1 ) );
                },
                min: 1,
                max: 6,
            } ),
            el( RangeControl, {
                label: __( 'Screen width / breakpoint (px)', 'braftonium' ),
                help: __( 'Applies at this screen width and below.', 'braftonium' ),
                value: tier.breakpoint,
                onChange: function ( v ) {
                    update( 'breakpoint', v == null ? 0 : v );
                },
                min: 0,
                max: 2560,
                step: 10,
            } )
        );
    }

    blocks.registerBlockType( 'braftonium/slider', {
        edit: function ( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var preview = attributes.preview;
            var dotsVisibility = attributes.dotsVisibility;
            var dotsPlacement = attributes.dotsPlacement;
            var arrowsVisibility = attributes.arrowsVisibility;
            var arrowsType = attributes.arrowsType;
            var arrowsLeftImage = attributes.arrowsLeftImage;
            var arrowsRightImage = attributes.arrowsRightImage;
            var arrowsLeftText = attributes.arrowsLeftText;
            var arrowsRightText = attributes.arrowsRightText;
            var playbackAutoplaySpeed = attributes.playbackAutoplaySpeed;
            var playbackSlideSpeed = attributes.playbackSlideSpeed;
            var presentationInfinite = attributes.presentationInfinite;
            var responsive = Object.assign( {}, RESP_DEFAULTS, attributes.responsive || {} );
            var bgColor = attributes.bgColor || { r: 0, g: 0, b: 0, a: 0 };

            var hasBgColor = ( bgColor.a == null ? 0 : bgColor.a ) > 0;
            var trackRef = useRef( null );

            var blockProps = useBlockProps( {
                className: 'braftonium-slider slick-dots-' + dotsPlacement + ( preview ? ' is-previewing' : '' ),
                style: hasBgColor ? { backgroundColor: rgbaString( bgColor ) } : undefined,
            } );

            // useInnerBlocksProps -> slides are DIRECT children of the track, so
            // Slick (which expects direct children) works on the same element.
            var innerBlocksProps = useInnerBlocksProps(
                { className: 'braftonium-slider-track', ref: trackRef },
                {
                    allowedBlocks: [ 'braftonium/slide' ],
                    template: [ [ 'braftonium/slide', {} ], [ 'braftonium/slide', {} ] ],
                    orientation: 'horizontal',
                }
            );

            // Init / destroy Slick when preview toggles or the config changes.
            useEffect(
                function () {
                    var $ = window.jQuery;
                    var node = trackRef.current;
                    if ( ! $ || ! $.fn || ! $.fn.slick || ! node ) {
                        return;
                    }
                    var $track = $( node );

                    function destroy() {
                        if ( $track.hasClass( 'slick-initialized' ) ) {
                            try {
                                $track.slick( 'unslick' );
                            } catch ( e ) {}
                        }
                    }

                    if ( preview ) {
                        destroy();
                        try {
                            $track.slick( buildSlickConfig( attributes ) );
                        } catch ( e ) {}
                    } else {
                        destroy();
                    }

                    return destroy;
                },
                [
                    preview,
                    dotsVisibility,
                    dotsPlacement,
                    arrowsVisibility,
                    arrowsType,
                    arrowsLeftImage,
                    arrowsRightImage,
                    arrowsLeftText,
                    arrowsRightText,
                    playbackAutoplaySpeed,
                    playbackSlideSpeed,
                    presentationInfinite,
                    JSON.stringify( responsive ),
                ]
            );

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __( 'Preview', 'braftonium' ), initialOpen: false },
                        el( ToggleControl, {
                            label: __( 'Preview Slider?', 'braftonium' ),
                            help: __( 'Runs the live carousel in the editor. Turn off to edit slides.', 'braftonium' ),
                            checked: preview,
                            onChange: function ( v ) {
                                setAttributes( { preview: v } );
                            },
                        } )
                    ),
                    el(
                        PanelBody,
                        { title: __( 'Dot Options', 'braftonium' ), initialOpen: true },
                        el( RadioControl, {
                            label: __( 'Visibility', 'braftonium' ),
                            selected: dotsVisibility,
                            options: [
                                { label: __( 'Visible', 'braftonium' ), value: 'visible' },
                                { label: __( 'Hidden', 'braftonium' ), value: 'hidden' },
                            ],
                            onChange: function ( v ) {
                                setAttributes( { dotsVisibility: v } );
                            },
                        } ),
                        dotsVisibility === 'visible' &&
                            el( RadioControl, {
                                label: __( 'Placement', 'braftonium' ),
                                selected: dotsPlacement,
                                options: [
                                    { label: __( 'Top', 'braftonium' ), value: 'top' },
                                    { label: __( 'Bottom', 'braftonium' ), value: 'bottom' },
                                ],
                                onChange: function ( v ) {
                                    setAttributes( { dotsPlacement: v } );
                                },
                            } )
                    ),
                    el(
                        PanelBody,
                        { title: __( 'Arrow Options', 'braftonium' ), initialOpen: false },
                        el( RadioControl, {
                            label: __( 'Visibility', 'braftonium' ),
                            selected: arrowsVisibility,
                            options: [
                                { label: __( 'Visible', 'braftonium' ), value: 'visible' },
                                { label: __( 'Hidden', 'braftonium' ), value: 'hidden' },
                            ],
                            onChange: function ( v ) {
                                setAttributes( { arrowsVisibility: v } );
                            },
                        } ),
                        arrowsVisibility === 'visible' &&
                            el(
                                Fragment,
                                null,
                                el( RadioControl, {
                                    label: __( 'Type', 'braftonium' ),
                                    selected: arrowsType,
                                    options: [
                                        { label: __( 'Text', 'braftonium' ), value: 'text' },
                                        { label: __( 'Image', 'braftonium' ), value: 'image' },
                                    ],
                                    onChange: function ( v ) {
                                        setAttributes( { arrowsType: v } );
                                    },
                                } ),
                                arrowsType === 'text' &&
                                    el(
                                        Fragment,
                                        null,
                                        el( TextControl, {
                                            label: __( 'Left Arrow Text', 'braftonium' ),
                                            value: arrowsLeftText,
                                            onChange: function ( v ) {
                                                setAttributes( { arrowsLeftText: v } );
                                            },
                                        } ),
                                        el( TextControl, {
                                            label: __( 'Right Arrow Text', 'braftonium' ),
                                            value: arrowsRightText,
                                            onChange: function ( v ) {
                                                setAttributes( { arrowsRightText: v } );
                                            },
                                        } )
                                    ),
                                arrowsType === 'image' &&
                                    el(
                                        Fragment,
                                        null,
                                        el(
                                            MediaUploadCheck,
                                            null,
                                            el( MediaUpload, {
                                                onSelect: function ( m ) {
                                                    setAttributes( { arrowsLeftImage: m.url } );
                                                },
                                                allowedTypes: [ 'image' ],
                                                render: function ( o ) {
                                                    return el(
                                                        'div',
                                                        { style: { marginBottom: '16px' } },
                                                        el( Text, null, __( 'Left Arrow Image', 'braftonium' ) ),
                                                        arrowsLeftImage
                                                            ? el(
                                                                  Fragment,
                                                                  null,
                                                                  el( 'img', { src: arrowsLeftImage, style: { width: '100px', display: 'block', marginTop: '8px' } } ),
                                                                  el( Button, { onClick: o.open, variant: 'secondary' }, __( 'Change', 'braftonium' ) )
                                                              )
                                                            : el( Button, { onClick: o.open, variant: 'secondary' }, __( 'Select Image', 'braftonium' ) )
                                                    );
                                                },
                                            } )
                                        ),
                                        el(
                                            MediaUploadCheck,
                                            null,
                                            el( MediaUpload, {
                                                onSelect: function ( m ) {
                                                    setAttributes( { arrowsRightImage: m.url } );
                                                },
                                                allowedTypes: [ 'image' ],
                                                render: function ( o ) {
                                                    return el(
                                                        'div',
                                                        null,
                                                        el( Text, null, __( 'Right Arrow Image', 'braftonium' ) ),
                                                        arrowsRightImage
                                                            ? el(
                                                                  Fragment,
                                                                  null,
                                                                  el( 'img', { src: arrowsRightImage, style: { width: '100px', display: 'block', marginTop: '8px' } } ),
                                                                  el( Button, { onClick: o.open, variant: 'secondary' }, __( 'Change', 'braftonium' ) )
                                                              )
                                                            : el( Button, { onClick: o.open, variant: 'secondary' }, __( 'Select Image', 'braftonium' ) )
                                                    );
                                                },
                                            } )
                                        )
                                    )
                            )
                    ),
                    el(
                        PanelBody,
                        { title: __( 'Playback Options', 'braftonium' ), initialOpen: false },
                        el( RangeControl, {
                            label: __( 'Autoplay Speed (ms)', 'braftonium' ),
                            value: playbackAutoplaySpeed,
                            onChange: function ( v ) {
                                setAttributes( { playbackAutoplaySpeed: v } );
                            },
                            min: 1000,
                            max: 10000,
                            step: 500,
                        } ),
                        el( RangeControl, {
                            label: __( 'Slide Speed (ms)', 'braftonium' ),
                            value: playbackSlideSpeed,
                            onChange: function ( v ) {
                                setAttributes( { playbackSlideSpeed: v } );
                            },
                            min: 100,
                            max: 2000,
                            step: 100,
                        } )
                    ),
                    el(
                        PanelBody,
                        { title: __( 'Presentation Options', 'braftonium' ), initialOpen: false },
                        el( Text, { style: { display: 'block', marginBottom: '12px' } }, __( 'Slides per view, scroll amount and breakpoint per screen size.', 'braftonium' ) ),
                        tierControls( 'desktop', __( 'Desktop / PC', 'braftonium' ), responsive, function ( r ) {
                            setAttributes( { responsive: r } );
                        } ),
                        tierControls( 'tablet', __( 'Tablet', 'braftonium' ), responsive, function ( r ) {
                            setAttributes( { responsive: r } );
                        } ),
                        tierControls( 'cell', __( 'Cell / Mobile', 'braftonium' ), responsive, function ( r ) {
                            setAttributes( { responsive: r } );
                        } ),
                        el( ToggleControl, {
                            label: __( 'Infinite Loop', 'braftonium' ),
                            checked: presentationInfinite,
                            onChange: function ( v ) {
                                setAttributes( { presentationInfinite: v } );
                            },
                        } )
                    ),
                    el(
                        PanelBody,
                        { title: __( 'Background Color', 'braftonium' ), initialOpen: false },
                        el( ColorPicker, {
                            color: rgbaString( bgColor ),
                            onChange: function ( value ) {
                                setAttributes( { bgColor: toRgba( value ) } );
                            },
                            enableAlpha: true,
                        } )
                    )
                ),
                el( 'div', blockProps, el( 'div', innerBlocksProps ) )
            );
        },
        save: function () {
            return el( InnerBlocks.Content, null );
        },
    } );
} )();
