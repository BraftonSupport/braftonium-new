<?php

    acf_register_block_type(array(
        'name'                => 'slide',
        'title'               => __('Single Slide'),
        'description'         => __('Single Slide for the Brafton Content Slider'),

        'enqueue_assets'      => function (){
            wp_enqueue_style('braftonium-slide-css', plugin_dir_url(__FILE__).'slide.css');
        },

        'parent'              => ['acf/slider'],

        'category'            => 'braftonium',
        'mode'                => 'preview',  
        'render_callback'     => 'braftonium_blocks_template',

        'icon' => '<svg stroke-width=".501" stroke-linejoin="bevel" fill-rule="evenodd" xmlns="http://www.w3.org/2000/svg" overflow="visible" width="268.2" height="266.6" viewBox="0 0 201.15 199.95">
        <g fill="#000" stroke="none" font-family="Times New Roman" font-size="16">
          <path d="M0 199.95V.036h201.162V199.95H0Zm190.356-10.806V10.842H10.806v178.302h179.55Z"/>
          <g stroke-linejoin="miter" stroke-width="2.222" stroke-miterlimit="79.84">
            <path d="M116.489 77.253c0-6.668-3.922-11.504-11.537-11.504H90.541v23.434h14.673c7.189 0 11.275-5.262 11.275-11.93ZM106.783 102.779H90.541v26.831h16.208c8.401 0 13.106-5.619 13.106-13.464.035-7.747-4.838-13.367-13.072-13.367Z"/>
            <path d="M174.795 25.353H27.625v147.171h147.17V72.711l-12.19-10.458 12.19-11.341V25.353Zm-69.319 117.2H73.379V64.148h-8.496V51.205h40.2c16.734 0 28.792 8.629 28.792 23.959 0 10.325-5.621 16.994-11.241 19.9v.263c10.065 3.007 14.933 12.421 14.933 21.865-.03 18.564-14.802 25.361-32.091 25.361Z"/>
          </g>
        </g>
      </svg>',

        'supports'            => [
            'anchor'          => true,
            'customClassName' => true,
            'jsx'             => true,
            'spacing'         => [
                'margin'      => ['top','bottom','left','right'],
                'padding'     => ['top','bottom','left','right']
            ],
            'html'            => false
        ]
    ));

?>