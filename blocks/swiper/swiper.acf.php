<?php

    acf_register_block_type(array(
        'name'                => 'swiper',
        'title'               => __('Swiper'),
        'description'         => __('Brafton Content Swiper'),
        'enqueue_assets'      => function (){
            $pluginDirUrl = plugin_dir_url(__FILE__);
            wp_enqueue_script('swiper', $pluginDirUrl.'swiper/swiper-bundle.min.js');
            wp_enqueue_style('swiper', $pluginDirUrl.'swiper/swiper-bundle.min.css');
            wp_enqueue_style('braftonium-swiper', $pluginDirUrl.'swiper.css');
        },
        'category'            => 'braftonium',
        'mode'                => 'preview',  
        'render_callback'     => 'braftonium_blocks_template',
        'icon'                => '<svg stroke-width=".501" stroke-linejoin="bevel" fill-rule="evenodd" xmlns="http://www.w3.org/2000/svg" overflow="visible" width="387.6" height="318.6" viewBox="0 0 290.7 238.95">
        <g fill="#000" stroke="none" font-family="Times New Roman" font-size="16">
          <path d="M42.69 199.926V.012h201.162v199.914H42.69Zm190.356-10.806V10.818H53.496V189.12h179.55ZM105.599 211.936c7.455 0 13.508 6.05 13.508 13.506s-6.053 13.508-13.508 13.508c-7.457 0-13.508-6.052-13.508-13.508 0-7.456 6.051-13.506 13.508-13.506ZM146.121 211.936c7.456 0 13.508 6.05 13.508 13.506s-6.052 13.508-13.508 13.508c-7.456 0-13.508-6.052-13.508-13.508 0-7.456 6.052-13.506 13.508-13.506ZM186.644 211.936c7.457 0 13.508 6.05 13.508 13.506s-6.051 13.508-13.508 13.508c-7.456 0-13.507-6.052-13.507-13.508 0-7.456 6.051-13.506 13.507-13.506Z"/>
          <g stroke-linejoin="miter" stroke-width="2.222" stroke-miterlimit="79.84">
            <path d="M159.179 77.229c0-6.668-3.922-11.504-11.537-11.504h-14.411v23.434h14.673c7.189 0 11.275-5.262 11.275-11.93ZM149.473 102.755h-16.242v26.831h16.208c8.401 0 13.106-5.619 13.106-13.464.035-7.747-4.838-13.367-13.072-13.367Z"/>
            <path d="M217.485 25.329H70.315V172.5h147.17V72.687l-12.19-10.458 12.19-11.341V25.329Zm-69.319 117.2h-32.097V64.124h-8.496V51.181h40.2c16.734 0 28.792 8.629 28.792 23.959 0 10.325-5.621 16.994-11.241 19.9v.263c10.065 3.007 14.933 12.421 14.933 21.865-.03 18.564-14.802 25.361-32.091 25.361Z"/>
          </g>
          <path d="M30 68.915v60l-30-30 30-30ZM260.7 68.915v60l30-30-30-30Z"/>
        </g>
      </svg>',
        'acf_block_version'   => 2,
        'supports'            => [
            'anchor'          => true,
            'customClassName' => true,
            'align'           => [ 'left', 'center', 'right', 'full' ],
            'align_content'   => [ 'top', 'center', 'bottom' ],
            'jsx'             => true,
            'color'           => [
                'text'        => true
            ],
            'spacing'         => [
                'margin'      => ['top','bottom'],
                'padding'     => ['top','bottom']
            ],
            'html'            => false
        ]
    ));
?>