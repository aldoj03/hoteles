


<?php

/*
Plugin Name: Busqueda de Hoteles
Description: Filtro de hoteles
Author: Seo Contenidos
Version: 0.0.1
Author URI: https://seocontenidos.net/
License: GPLv2 or later
Text Domain: wp-test
*/



add_action( 'rest_api_init', 'add_custom_users_api');
 
function add_custom_users_api(){
	//primera request
    register_rest_route(
     'wphot/v1',
     'hoteles', 
     [
        'methods' => 'GET',
        'callback' => 'get_hotels_request',
	]);
	//filtrar request
    register_rest_route(
     'wphot/v1',
     'hoteles/filtered', 
     [
        'methods' => 'POST',
		'callback' => 'get_hotels_filtered_request',
	]);
	

}

function get_hotels_filtered_request(WP_REST_Request $params){

	$checkDays = $params->get_param('stay');
	$apiKey = "625f4c71c0828f829bd1c878b4f6c3d6";  
    $Secret = "e805df16c7";
	$xsignature = hash("sha256", $apiKey.$Secret.time());
	$array_ids = [];

	$response =  getHotelsRooms($apiKey,$xsignature);
	$response_decoded = json_decode($response);
	

	foreach ($response_decoded->hotels->hotels as $key => $value) {
		
		array_push($array_ids , $value->code);
		
	}
	$reponse_details = json_decode(getHotels_details($apiKey,$xsignature,$array_ids));
	$final_array->hotels = commbineArrays($response_decoded, $reponse_details);
	$final_array->checkDays = json_decode($checkDays) ;
	$final_array->checkDays->checkIn = $response_decoded->hotels->checkIn;
	$final_array->checkDays->checkOut = $response_decoded->hotels->checkOut;
	$final_array->checkDays->total = $response_decoded->hotels->total;
	wp_send_json($final_array );

}

function preprareUrl( $params){

	return   http_build_query($params);
}




//obtiene lista de hoteles
function getHotels_details($apiKey,$xsignature,$ids){

	$ids_string = implode(',',$ids);
	// wp_send_json($ids_string);
	
	$url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?codes='.$ids_string.'&language=CAS&fields=ranking,description,images';

	$getHotelsResponse  = wp_remote_get( $url, array(
		'headers'=> array(
		'Accept' => 'application/json',
		'Accept-Encoding'=>'application/gzip',
		'Content-Type'=>'application/json',
		'Api-key'=>$apiKey,
		'X-Signature'=>$xsignature
		),
		'timeout' => 8
		) );
        return wp_remote_retrieve_body( $getHotelsResponse );
}


//inserta habitaciones dispobibles en array de hoteles

function commbineArrays($arrayHotels, $arrayDetails){
$arrayTosend = array();


    foreach ($arrayHotels->hotels->hotels as $key => $hotel) {
		foreach ($arrayDetails->hotels as $key => $details) {
			if($details->code == $hotel->code){
				$hotel->description = $details->description;
                $hotel->ranking = $details->ranking;
                $hotel->images = $details->images[0];
                array_push($arrayTosend,$hotel) ;
            }
            
        }
        
	}
	
		return $arrayTosend;
		
	}


    //obtiene disponibilidad de habitaciones segun parametros
function getHotelsRooms($apiKey,$xsignature){


	$body = array(
		"geolocation"=>array("latitude"=> 39.57119,
			"longitude"=> 2.646633999999949,
			"radius"=> 10,
			"unit"=> "km"
			
		) ,
		"filter"=>array(
			"maxHotels"=> 20
		),
		
		"stay"=> array(
			"checkIn"=>"2021-06-15",
			"checkOut"=> "2021-06-20"
		),
		"occupancies"=> array(
			array("rooms"=> 2,
			"adults"=> 2,
			"children"=> 0)
	
		)

	);
	$responseHotelsRooms = wp_remote_post( 'https://api.test.hotelbeds.com/hotel-api/1.0/hotels', 
	array(
		'headers'=> array(
			'Accept' => 'application/json',
			'Accept-Encoding'=>'application/gzip',
			'Content-Type'=>'application/json',
			'Api-key'=>$apiKey,
			'X-Signature'=>$xsignature
		),
		"body"=> json_encode($body),
		'timeout' => 10 
	));
	if ( is_array( $responseHotelsRooms ) && ! is_wp_error( $responseHotelsRooms ) ) {
		
		return wp_remote_retrieve_body($responseHotelsRooms);
	}
	else{
		wp_send_json(is_wp_error( $responseHotelsRooms ));

	}

}



// function that runs when shortcode is called
function hot_form_request__function() { 
 
	// Things that you want to do. 
	$form ='
	 <form action="" method="POST">
	 <input type="hidden" value="hotels_form" name="action">
	 	<div>
		 	<h4>Lugar</h4>
			 <select name="lugar" id=""></select>
		 </div>
	 	<div>
			<div>
				<h4>Entrada</h4>
				<input type="date" name="entrada" id="" required>
			</div>
			<div>
				<h4>Salida</h4>
				<input type="date" name="salida" id="" required>
			</div>
		 </div>
		 <div>
		 	<h4>Habitaciones</h4>
			 <div>
				 <h5>Adultos</h5>
				 <input type="number" name="adultos" id="" required>
			 </div>
			 <div>
				 <h5>Niños</h5>
				 <input type="number" name="ninos" id="" required>
			 </div>
			 <div>
				 <h5>Habitaciones</h5>
				 <input type="number" name="habitaciones" id="" required>
			 </div>
		 </div>
		 <button type="sumbmit" >Enviar</button>
	 </form>
	';
	return $form;
	} 
	// register shortcode
add_shortcode('hot-form-request', 'hot_form_request__function'); 



add_shortcode('hot-results-page', 'hot_results_page__function');


function hot_results_page__function(){

	$urlBase = plugin_dir_url( __FILE__ ) .'angularApp/src/index.html';

	$page = '<style>
		html,body{
			overflow: hidden
		}
		#hotelesApp{
			background: white
		}
	</style><iframe id="hotelesApp" title="Filtro de Hoteles" frameBorder="0"
    src="'.$urlBase.'" style="
    position: fixed !important;
    height: 100vh !important;
    width: 100% !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 99999 !important;"
   >
</iframe>';

	return $page;
}


function hot_handle_post_request() {
    /**
     * At this point, $_GET/$_POST variable are available
     *
     * We can do our normal processing here
     */ 

    // Sanitize the POST field
    // Generate email content
	// Send to appropriate email

	if(isset($_POST['action']) && isset($_POST['action']) == 'hotels_form' ){
		echo '<script>localStorage.setItem("entrada",'.$_POST['entrada'].')</script>';
		echo '<script>localStorage.setItem("salida",'.$_POST['salida'].')</script>';
		echo '<script>localStorage.setItem("ninos",'.$_POST['ninos'].')</script>';
		echo '<script>localStorage.setItem("adultos",'.$_POST['adultos'].')</script>';
		echo '<script>localStorage.setItem("habitaciones",'.$_POST['habitaciones'].')</script>';

		// get_hotels_filtered_request(){}

		wp_redirect( get_permalink( get_page_by_title( 'Resultado Hoteles' ) ));
		exit;
	}
	
}
add_action( 'init', 'hot_handle_post_request');


function hot_activate_plugin(){

	$page = get_page_by_path( resultado-hoteles );

     if ( !isset($page) ){

		 
			 $new_page = array(
				 'slug' => 'resultado-hoteles',
				 'title' => 'Resultado Hoteles',
				 'content' => "[hot-results-page]"
			 );
			 $new_page_id = wp_insert_post( array(
				 'post_title' => $new_page['title'],
				 'post_type'     => 'page',
				 'post_name'  => $new_page['slug'],
				 'comment_status' => 'closed',
				 'ping_status' => 'closed',
				 'post_content' => $new_page['content'],
				 'post_status' => 'publish',
				 'post_author' => 1,
				 'menu_order' => 0
			 ));
	 }


	 register_post_type(
		'hoteles',
		// CPT Options
		array(
		   'labels' => array(
			  'name' => __('Hoteles'),
			  'singular_name' => __('Hotel')
		   ),
		   'public' => false,
		   'has_archive' => false,
		   'rewrite' => array('slug' => 'hotel'),
		   'show_in_rest' => true,
  
		)
	 );

}


register_activation_hook( __FILE__, 'hot_activate_plugin' );


