<?php
register_block_pattern(
    'braftonium/' . str_replace( '.php', '', basename( __FILE__ ) ) . '-center',
    array(
        'title'       => __( 'Banner Center', 'braftonium' ),
        'description' => _x( 'Banner with centered content', 'Banner with centered content', 'braftonium' ),
        'categories'  => array( 'braftonium' ),
        'content'     => '<!-- wp:braftonium/banner {"overlayColor":{"r":221,"g":130,"b":130,"a":0.38},"alignContent":"center","align":"full"} -->
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center">Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry.</p>
<!-- /wp:paragraph -->
<!-- /wp:braftonium/banner -->',
    )
);

register_block_pattern(
    'braftonium/' . str_replace( '.php', '', basename( __FILE__ ) ) . '-left',
    array(
        'title'       => __( 'Banner Left', 'braftonium' ),
        'description' => _x( 'Banner with left aligned content', 'Banner with left aligned content', 'braftonium' ),
        'categories'  => array( 'braftonium' ),
        'content'     => '<!-- wp:braftonium/banner {"overlayColor":{"r":85,"g":145,"b":143,"a":0.34},"alignContent":"left","align":"full"} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2>Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Lorem Ipsum</strong> is simply dummy text of the printing and typesetting industry.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:braftonium/banner -->',
    )
);

register_block_pattern(
    'braftonium/' . str_replace( '.php', '', basename( __FILE__ ) ) . '-right',
    array(
        'title'       => __( 'Banner Right', 'braftonium' ),
        'description' => _x( 'Banner with right aligned content', 'Banner with right aligned content', 'braftonium' ),
        'categories'  => array( 'braftonium' ),
        'content'     => '<!-- wp:braftonium/banner {"overlayColor":{"r":221,"g":153,"b":51,"a":0.51},"alignContent":"right","align":"full"} -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading -->
<h2>Heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:braftonium/banner -->',
    )
);
