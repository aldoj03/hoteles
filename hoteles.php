


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

	// var_dump(json_encode($params->get_params()));
	// wp_send_json($params->get_params());
	$apiKey = "625f4c71c0828f829bd1c878b4f6c3d6";  
    $Secret = "e805df16c7";
    $xsignature = hash("sha256", $apiKey.$Secret.time());
	wp_send_json( getHotelsRooms($apiKey,$xsignature,$arrayIds));

}

function preprareUrl( $params){

	return   http_build_query($params);
}




function get_hotels_request(WP_REST_Request $params){
	$url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?language=CAS';

	if(count($params->get_params()) > 0){
		 $url = $url . '&'. http_build_query($params->get_params());
	}

    $apiKey = "625f4c71c0828f829bd1c878b4f6c3d6";  
    $Secret = "e805df16c7";
    $xsignature = hash("sha256", $apiKey.$Secret.time());
// var_dump($url);
    
    
$getHoteles_array = json_decode( getHotels($url,$apiKey,$xsignature) );
$arrayIds = array();
// var_dump($getHoteles_array);
// die();
foreach ($getHoteles_array->hotels as $key => $value) {
	array_push($arrayIds,$value->code);
	unset($value->rooms);
	unset($value->facilities);
    unset($value->issues);
    if($value->images){

        $value->images = array_slice($value->images,0, 5);
    }
}






$getHotelsRooms_array = json_decode(getHotelsRooms($apiKey,$xsignature, $arrayIds) );

// var_dump($getHotelsRooms_array->hotels->hotels);
// die();
$arrayTotal = commbineArrays($getHotelsRooms_array->hotels->hotels, $getHoteles_array);



wp_send_json($arrayTotal);

// echo '<script>console.log('.json_encode($arrayTotal ) .')</script>';
}




//obtiene lista de hoteles
function getHotels($url,$apiKey,$xsignature){

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
		// var_dump($getHotelsResponse);
		// die();
        return wp_remote_retrieve_body( $getHotelsResponse );
}


//inserta habitaciones dispobibles en array de hoteles

function commbineArrays($arrayHotels, $arrayRooms){
$arrayTosend = array();


    foreach ($arrayHotels as $key => $value1) {
        
        foreach ($arrayRooms->hotels as $key => $value2) {
            if($value1->code == $value2->code){
                $value2->rooms = $value1->rooms;
                $value2->minRate = $value1->minRate;
                array_push($arrayTosend,$value2) ;
            }
            
        }
        
	}
	if ( is_array( $arrayTosend ) && ! is_wp_error( $arrayTosend ) ) {
	
		return $arrayTosend;
		
	}else{
		wp_send_json($arrayTosend);

	}
}


    //obtiene disponibilidad de habitaciones segun parametros
function getHotelsRooms($apiKey,$xsignature,$arrayIds){

	// $body = array(
	// 	'hotels' =>array("hotel"=> $arrayIds),
	// 	"stay"=> array(
	// 		"checkIn"=>"2021-06-15",
	// 		"checkOut"=> "2021-06-20"
	// 	),
	// 	"occupancies"=> array(
	// 		array("rooms"=> 2,
	// 		"adults"=> 2,
	// 		"children"=> 0)
	
	// 	));
	$body = array(
		"geolocation"=>array("latitude"=> 39.57119,
			"longitude"=> 2.646633999999949,
			"radius"=> 10,
			"unit"=> "km"
			
		) ,
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