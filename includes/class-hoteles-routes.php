<?php


class Hoteles_routes{


    private $plugin_name;
    private $version;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

    }
    
    public function get_all_routes(){

        register_rest_route(
            'wphot/v1',
            'hoteles',
            [
                'methods' => 'GET',
                'callback' => [$this,'get_hotels_request'],
            ]
        );
    }

    public function get_hotels_request( WP_REST_Request $params ){
        $post_id = $params->get_param('id');
        $result =	get_post($post_id);
        wp_send_json(unserialize($result->post_content));
    }
    


    
}