<?php

include "include/contentlist_controller.php";

acf_register_block_type(array(
    'name'                => 'contentlist',
    'title'               => __('Content List'),
    'description'         => __('Displays a List of Queried Items'),

    'enqueue_assets'      => function (){
        wp_enqueue_style('contentlist', plugin_dir_url(__FILE__).'contentlist.css');
    },

    'category'            => 'braftonium',
    'mode'                => 'preview',  
    'render_callback'     => 'braftonium_blocks_template',

    'icon' => '<svg stroke-width=".501" stroke-linejoin="bevel" fill-rule="evenodd" xmlns="http://www.w3.org/2000/svg" overflow="visible" width="500" height="500" viewBox="0 0 375 375">
    <g fill="#000" stroke="none" font-family="Times New Roman" font-size="16">
      <path d="M2.684 113.384V1.007h367.234v112.377H2.684Zm122.113-17.927c3.685-1.426 6.847-3.458 9.486-6.098 2.64-2.641 4.691-5.852 6.153-9.632 1.463-3.78 2.194-8.027 2.194-12.738 0-4.673-.731-8.881-2.194-12.624-1.462-3.743-3.513-6.924-6.153-9.545-2.639-2.622-5.801-4.637-9.486-6.042-3.684-1.405-7.767-2.108-12.249-2.108-4.52 0-8.631.703-12.335 2.108-3.703 1.405-6.893 3.42-9.571 6.042-2.678 2.621-4.748 5.802-6.21 9.545-1.462 3.743-2.193 7.951-2.193 12.624 0 4.711.731 8.958 2.193 12.738 1.462 3.78 3.532 6.991 6.21 9.632 2.678 2.64 5.868 4.672 9.571 6.098 3.704 1.424 7.815 2.136 12.335 2.136 4.482 0 8.565-.712 12.249-2.136Zm107.043 1.715a35.652 35.652 0 0 0 6.666-1.514A30.772 30.772 0 0 0 245 92.629c2.108-1.29 4.074-2.965 5.897-5.02l-5.127-6.448c-.722-1.046-1.786-1.568-3.191-1.568a6.636 6.636 0 0 0-3.048.74c-.969.494-2.032 1.045-3.19 1.653-1.159.607-2.479 1.158-3.96 1.652-1.481.494-3.248.741-5.299.741-3.836 0-6.969-1.111-9.4-3.333-2.431-2.224-3.931-5.805-4.501-10.743h34.07c.797 0 1.453-.095 1.965-.285.513-.19.921-.532 1.225-1.025.304-.496.513-1.16.627-1.996.114-.837.171-1.92.171-3.249 0-4.256-.655-8.066-1.966-11.429-1.31-3.365-3.133-6.206-5.469-8.524-2.336-2.319-5.128-4.085-8.375-5.3-3.247-1.216-6.827-1.825-10.739-1.825-4.52 0-8.575.779-12.164 2.336-3.589 1.557-6.647 3.667-9.173 6.327-2.526 2.659-4.463 5.754-5.811 9.288-1.348 3.534-2.023 7.277-2.023 11.229 0 5.243.789 9.84 2.365 13.792 1.576 3.95 3.722 7.256 6.438 9.915 2.715 2.66 5.887 4.664 9.514 6.014 3.628 1.348 7.511 2.022 11.651 2.022 2.013 0 4.131-.139 6.353-.421Zm-154.734-.497V81.291H46.34V13.65H27.084v83.025h50.022Zm92.88 0V60.999c2.659-5.314 6.305-7.971 10.939-7.971 1.329 0 2.431.085 3.304.256.874.171 1.634.255 2.279.255.76 0 1.368-.158 1.823-.477.456-.318.76-.87.912-1.655l2.279-12.797c-1.633-1.441-3.798-2.163-6.495-2.163-3.19 0-6.134.978-8.831 2.931-2.696 1.953-5.127 4.613-7.292 7.984l-.969-5.577c-.152-.766-.332-1.418-.541-1.955-.209-.536-.513-.968-.912-1.294-.398-.326-.892-.565-1.481-.719-.589-.153-1.32-.229-2.193-.229h-10.483v59.087h17.661Zm109.049 0V53.938c1.254-1.219 2.592-2.189 4.017-2.914a9.844 9.844 0 0 1 4.529-1.084c2.583-.002 4.539.682 5.868 2.051 1.33 1.367 1.994 3.743 1.994 7.125v37.559h17.662V59.116c0-2.964.788-5.235 2.364-6.812 1.577-1.577 3.561-2.366 5.954-2.364 5.242-.002 7.862 3.058 7.862 9.176v37.559h17.662V59.122c0-3.534-.456-6.696-1.367-9.487-.912-2.794-2.251-5.149-4.017-7.067-1.766-1.918-3.96-3.382-6.58-4.388-2.621-1.007-5.622-1.51-9.002-1.51-1.823 0-3.656.192-5.498.573a22.396 22.396 0 0 0-5.242 1.772 19.39 19.39 0 0 0-4.614 3.148c-1.425 1.296-2.631 2.86-3.618 4.691-1.216-3.089-2.991-5.558-5.327-7.408-2.336-1.85-5.289-2.776-8.859-2.776-1.71 0-3.276.184-4.701.548a19.01 19.01 0 0 0-3.988 1.5 19.951 19.951 0 0 0-3.447 2.25 33.355 33.355 0 0 0-3.076 2.857l-.969-3.233c-.303-1.002-.826-1.752-1.566-2.254-.741-.5-1.662-.75-2.764-.75h-10.938v59.087h17.661Zm-175.66-16.492c-1.937-2.869-2.905-7.229-2.905-13.08 0-5.852.968-10.203 2.905-13.051 1.938-2.851 4.995-4.275 9.173-4.277 4.064.002 7.055 1.426 8.973 4.277 1.918 2.848 2.877 7.199 2.877 13.051 0 5.851-.959 10.211-2.877 13.08-1.918 2.869-4.909 4.305-8.973 4.305-4.178 0-7.235-1.436-9.173-4.305Zm113.737-28.447c1.899-1.921 4.539-2.882 7.919-2.882 1.9 0 3.514.323 4.843.97 1.329.648 2.402 1.494 3.219 2.541a9.959 9.959 0 0 1 1.766 3.565c.361 1.331.541 2.7.541 4.108h-22.048c.608-3.614 1.861-6.381 3.76-8.302ZM3.688 240.813V137.467h366.23v103.346H81.187v-17.332a21.124 21.124 0 0 0 5.698 3.477c2.089.861 4.671 1.29 7.748 1.29 3.76 0 7.188-.779 10.283-2.336 3.096-1.559 5.755-3.724 7.977-6.497 2.222-2.772 3.95-6.041 5.184-9.802 1.235-3.762 1.852-7.864 1.852-12.308 0-4.749-.541-8.995-1.624-12.737-1.082-3.742-2.573-6.904-4.472-9.489-1.899-2.583-4.159-4.558-6.78-5.925-2.621-1.367-5.469-2.053-8.546-2.053-2.051 0-3.941.211-5.669.629a23.246 23.246 0 0 0-4.843 1.737 24.427 24.427 0 0 0-4.216 2.651 29.61 29.61 0 0 0-3.674 3.42l-1.311-4.331c-.303-.988-.826-1.729-1.566-2.224-.741-.494-1.662-.741-2.764-.741H63.526v72.571H3.688Zm152.006-14.047c3.095-.987 5.697-2.374 7.805-4.159 2.108-1.786 3.694-3.913 4.757-6.382 1.064-2.469 1.595-5.166 1.595-8.09 0-2.393-.389-4.444-1.168-6.154-.778-1.709-1.813-3.162-3.105-4.36a18.842 18.842 0 0 0-4.387-3.019 44.619 44.619 0 0 0-5.013-2.137 111.188 111.188 0 0 0-5.014-1.652 42.89 42.89 0 0 1-4.387-1.567c-1.291-.55-2.326-1.186-3.105-1.908-.778-.722-1.168-1.633-1.168-2.735 0-1.519.599-2.708 1.795-3.562 1.196-.855 2.915-1.282 5.156-1.282 1.557 0 2.944.18 4.159.541a29.4 29.4 0 0 1 3.276 1.168l2.706 1.168a6.277 6.277 0 0 0 2.507.541c.798 0 1.453-.152 1.966-.456.512-.303.997-.816 1.452-1.538l3.989-6.21a22.952 22.952 0 0 0-3.818-3.054 26.8 26.8 0 0 0-4.785-2.425 31.353 31.353 0 0 0-5.612-1.598 32.96 32.96 0 0 0-6.182-.571c-3.912 0-7.33.503-10.255 1.509-2.924 1.007-5.355 2.366-7.292 4.075-1.937 1.711-3.39 3.696-4.359 5.957a17.982 17.982 0 0 0-1.453 7.154c0 2.698.399 4.997 1.197 6.896.797 1.901 1.842 3.506 3.133 4.816a17.37 17.37 0 0 0 4.416 3.249 40.484 40.484 0 0 0 5.042 2.194c1.709.607 3.39 1.15 5.042 1.625 1.652.475 3.124.988 4.415 1.538 1.292.553 2.336 1.208 3.134 1.967.797.76 1.196 1.729 1.196 2.906 0 .646-.133 1.272-.399 1.882-.265.607-.683 1.158-1.253 1.652-.57.494-1.329.883-2.279 1.17-.949.283-2.108.427-3.475.427-1.937 0-3.542-.228-4.814-.684-1.273-.456-2.384-.949-3.333-1.481-.95-.532-1.833-1.027-2.65-1.481-.816-.458-1.737-.686-2.763-.686-1.101 0-1.994.222-2.678.665-.683.442-1.272 1.048-1.766 1.817l-4.102 6.577c1.14 1.008 2.507 1.953 4.102 2.831a32.022 32.022 0 0 0 5.213 2.273 42.758 42.758 0 0 0 5.84 1.514c2.013.374 4.026.561 6.039.561 4.026 0 7.587-.494 10.683-1.482Zm47.993.947a23.516 23.516 0 0 0 4.473-1.463 21.647 21.647 0 0 0 3.902-2.25 33.72 33.72 0 0 0 3.532-2.953l1.083 3.366c.684 1.945 2.127 2.917 4.33 2.917h10.939v-59.088h-17.662v42.122c-1.595 1.444-3.238 2.564-4.928 3.362-1.69.797-3.504 1.196-5.441 1.196-2.583 0-4.539-.797-5.868-2.393-1.33-1.595-1.994-3.856-1.994-6.783v-37.504h-17.662v37.497c0 3.266.437 6.268 1.311 9.003.873 2.736 2.165 5.11 3.874 7.123s3.807 3.58 6.295 4.702c2.488 1.12 5.346 1.681 8.575 1.681 1.899 0 3.646-.178 5.241-.535Zm-155.523-.383v-83.04H28.793v83.04h19.371Zm215.126 0v-42.738c1.254-1.219 2.592-2.189 4.017-2.913a9.845 9.845 0 0 1 4.529-1.085c2.583-.001 4.539.683 5.868 2.052 1.33 1.367 1.994 3.742 1.994 7.124v37.56h17.662v-37.56c0-2.964.788-5.235 2.364-6.811 1.577-1.578 3.561-2.366 5.954-2.365 5.242-.001 7.862 3.058 7.862 9.176v37.56h17.662v-37.553c0-3.534-.456-6.696-1.367-9.488-.912-2.793-2.251-5.148-4.017-7.066-1.766-1.918-3.96-3.382-6.58-4.389-2.621-1.006-5.622-1.509-9.002-1.509-1.823 0-3.656.191-5.498.572a22.398 22.398 0 0 0-5.242 1.773 19.39 19.39 0 0 0-4.614 3.148c-1.425 1.296-2.631 2.859-3.618 4.69-1.216-3.089-2.991-5.558-5.327-7.408-2.336-1.85-5.289-2.775-8.859-2.775-1.71 0-3.276.183-4.701.547a19.122 19.122 0 0 0-3.988 1.5 20.015 20.015 0 0 0-3.447 2.251 33.204 33.204 0 0 0-3.076 2.856l-.969-3.233c-.303-1.002-.826-1.752-1.566-2.253-.741-.501-1.662-.751-2.764-.751h-10.938v59.088h17.661ZM85.517 214.373c-1.557-.589-3-1.624-4.33-3.105v-25.074a25.737 25.737 0 0 1 2.279-2.422 11.03 11.03 0 0 1 2.478-1.737 12.892 12.892 0 0 1 2.906-1.056c1.045-.247 2.213-.37 3.504-.37 1.405 0 2.678.285 3.817.854 1.14.57 2.118 1.512 2.934 2.822.817 1.31 1.453 3.029 1.909 5.156.456 2.129.683 4.749.683 7.866 0 3.266-.284 6.04-.854 8.319-.57 2.279-1.358 4.131-2.365 5.557-1.006 1.424-2.193 2.459-3.56 3.105-1.368.645-2.868.968-4.501.968-1.709 0-3.343-.294-4.9-.883ZM2.684 372.255V265.897h371.248v106.358H2.684Zm147.54-12.854c3.685-1.426 6.847-3.458 9.486-6.097 2.64-2.642 4.691-5.853 6.153-9.632 1.463-3.781 2.194-8.027 2.194-12.738 0-4.673-.731-8.882-2.194-12.624-1.462-3.743-3.513-6.924-6.153-9.545-2.639-2.622-5.801-4.637-9.486-6.042-3.684-1.405-7.767-2.108-12.249-2.108-4.52 0-8.631.703-12.335 2.108-3.703 1.405-6.893 3.42-9.571 6.042-2.678 2.621-4.748 5.802-6.21 9.545-1.462 3.742-2.193 7.951-2.193 12.624 0 4.711.731 8.957 2.193 12.738 1.462 3.779 3.532 6.99 6.21 9.632 2.678 2.639 5.868 4.671 9.571 6.097 3.704 1.425 7.815 2.137 12.335 2.137 4.482 0 8.565-.712 12.249-2.137Zm98.59 0c3.685-1.426 6.847-3.458 9.486-6.097 2.64-2.642 4.691-5.853 6.153-9.632 1.463-3.781 2.194-8.027 2.194-12.738 0-4.673-.731-8.882-2.194-12.624-1.462-3.743-3.513-6.924-6.153-9.545-2.639-2.622-5.801-4.637-9.486-6.042-3.684-1.405-7.767-2.108-12.249-2.108-4.52 0-8.631.703-12.335 2.108-3.703 1.405-6.893 3.42-9.571 6.042-2.678 2.621-4.748 5.802-6.21 9.545-1.462 3.742-2.193 7.951-2.193 12.624 0 4.711.731 8.957 2.193 12.738 1.462 3.779 3.532 6.99 6.21 9.632 2.678 2.639 5.868 4.671 9.571 6.097 3.704 1.425 7.815 2.137 12.335 2.137 4.482 0 8.565-.712 12.249-2.137Zm-171.851-1.888c5.299-2.07 9.828-4.958 13.588-8.663 3.761-3.705 6.676-8.093 8.746-13.165 2.07-5.072 3.105-10.61 3.105-16.613 0-5.966-1.035-11.484-3.105-16.556-2.07-5.073-4.985-9.452-8.746-13.137-3.76-3.686-8.289-6.575-13.588-8.664-5.298-2.089-11.176-3.133-17.633-3.135H27.084v83.04H59.33c6.457 0 12.335-1.035 17.633-3.107Zm119.02 3.107v-85.293h-17.662v85.293h17.662Zm98.02 0v-35.676c2.659-5.314 6.305-7.972 10.939-7.972 1.329 0 2.431.086 3.304.257.874.171 1.634.255 2.279.255.76 0 1.368-.159 1.823-.477.456-.318.76-.87.912-1.655l2.279-12.797c-1.633-1.442-3.798-2.163-6.495-2.163-3.19 0-6.134.978-8.831 2.931-2.696 1.953-5.127 4.613-7.292 7.984l-.969-5.577c-.152-.766-.332-1.418-.541-1.955-.209-.536-.513-.968-.912-1.294-.398-.326-.892-.565-1.481-.719-.589-.153-1.32-.229-2.193-.229h-10.483v59.087h17.661Zm-165.201-16.492c-1.937-2.869-2.905-7.229-2.905-13.08 0-5.853.968-10.203 2.905-13.052 1.938-2.85 4.995-4.274 9.173-4.276 4.064.002 7.055 1.426 8.973 4.276 1.918 2.849 2.877 7.199 2.877 13.052 0 5.851-.959 10.211-2.877 13.08-1.918 2.869-4.909 4.305-8.973 4.305-4.178 0-7.235-1.436-9.173-4.305Zm98.59 0c-1.937-2.869-2.905-7.229-2.905-13.08 0-5.853.968-10.203 2.905-13.052 1.938-2.85 4.995-4.274 9.173-4.276 4.064.002 7.055 1.426 8.973 4.276 1.918 2.849 2.877 7.199 2.877 13.052 0 5.851-.959 10.211-2.877 13.08-1.918 2.869-4.909 4.305-8.973 4.305-4.178 0-7.235-1.436-9.173-4.305Zm-180.938 1.665v-53.385H59.33c3.685.001 6.97.618 9.857 1.851 2.886 1.236 5.327 3.002 7.321 5.3 1.994 2.298 3.522 5.099 4.586 8.404 1.063 3.304 1.595 7.007 1.595 11.109 0 4.141-.532 7.863-1.595 11.167-1.064 3.305-2.592 6.106-4.586 8.404-1.994 2.298-4.435 4.064-7.321 5.298-2.887 1.235-6.172 1.852-9.857 1.852H46.454Z"/>
    </g>
  </svg>
  ',

    'supports'            => [
        'anchor'          => true,
        'customClassName' => true,
        'align'           => [ 'left', 'center', 'right', 'full' ],
        'jsx'             => true,
        'color'           => [
            'text'        => true,
            'background'  => false
        ],
        'html'            => false
    ]
));

?>