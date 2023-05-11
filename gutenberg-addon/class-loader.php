<?php
/**
 * Class Api exemple
 */
class BraftoniumClassLoader {
    private $version;
    private $namespace;
    private $plugin_dir_path;
    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        $this->version   = '1';
        $this->namespace = $plugin_name . '/v' . $this->version;
        $this->plugin_dir_path = plugin_dir_path( dirname( __FILE__ ) );
    }

    public function run() {
        add_action( 'rest_api_init', array( $this, 'register_braftonium_class_list' ) );
    }

    public function register_braftonium_class_list() {
        register_rest_route(
            $this->namespace,
            '/braftonium-class-list',
            array(
                'methods'             => 'POST',
                'callback'            => array( $this, 'get_class_list' ),
                'permission_callback' => function () {
                    return current_user_can( 'edit_posts' );
                },
                'args'  => array(
                    'blockType'    => array(
                        'required'  => false
                    )
                )
            )
        );
    }

    public function get_class_list($request) {
        $blockType = $request['blockType']?? null;
        $classList = [
    
        ];
        /**
         * 
         */
        return apply_filters('braftonium_class_list',$classList, $blockType);
    }
}
$classListApi = new BraftoniumClassLoader('braftonium');
$classListApi->run();