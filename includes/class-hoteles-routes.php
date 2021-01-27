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
		add_action('init',[$this,'add_cors_http_header']);

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
        register_rest_route(
            'wphot/v1',
            'hotel',
            [
                'methods' => 'GET',
                'callback' => [$this,'get_single_hotel_request'],
            ]
        );
    }

    public function get_hotels_request( WP_REST_Request $params ){
        $post_id = $params->get_param('id');
        $page = $params->get_param('page');
        $pageInit =  $page === 1 ? 0 : ($page * 20) - 20;
        $hotelFinal = $pageInit + 20;  
        $result =	get_post($post_id);

        $objectArray = unserialize($result->post_content);
        $hotelsArray = array_slice($objectArray->hotels, $pageInit,20); 
        $objectArray->hotels = $hotelsArray;

        wp_send_json($objectArray);
    }
    
    public function add_cors_http_header(  ){
		header("Access-Control-Allow-Origin: *");
    }
    
    public function get_single_hotel_request( WP_REST_Request $params ){
        $hotel_id = $params->get_param('id');
        $apiKey = API_KEY;
		$Secret = SECRET;
        $xsignature = hash("sha256", $apiKey . $Secret . time());

		$url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels/'.$hotel_id.'/details?language=CAS';

        
        $getHotelsResponse  = wp_remote_get($url, array(
			'headers' => array(
				'Accept' => 'application/json',
				'Accept-Encoding' => 'application/gzip',
				'Content-Type' => 'application/json',
				'Api-key' => $apiKey,
				'X-Signature' => $xsignature
			),
			'timeout' => 8
		));
		
		wp_send_json(wp_remote_retrieve_body($getHotelsResponse));
    }

    
}