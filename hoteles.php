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
    register_rest_route(
     'wphot/v1',
     'hoteles', 
     array(
        'methods' => 'GET',
        'callback' => 'get_custom_users_data',
    ));
}




function get_custom_users_data(){
    


    $apiKey = "625f4c71c0828f829bd1c878b4f6c3d6";  
    $Secret = "e805df16c7";
    $xsignature = hash("sha256", $apiKey.$Secret.time());
    
    
$getHoteles_array = json_decode( getHotels($apiKey,$xsignature) );
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
function getHotels($apiKey,$xsignature){

	$getHotelsResponse  = wp_remote_get( 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?countryCode=ES&destinationCode=MAD&fields=all&language=CAS', array(
		'headers'=> array(
		'Accept' => 'application/json',
		'Accept-Encoding'=>'application/gzip',
		'Content-Type'=>'application/json',
		'Api-key'=>$apiKey,
		'X-Signature'=>$xsignature
		),
        ) );
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

    return $arrayTosend;
}


    //obtiene disponibilidad de habitaciones segun parametros
function getHotelsRooms($apiKey,$xsignature,$arrayIds){

	$body = array(
		'hotels' =>array("hotel"=> $arrayIds),
		"stay"=> array(
			"checkIn"=>"2021-06-15",
			"checkOut"=> "2021-06-20"
		),
		"occupancies"=> array(
			array("rooms"=> 2,
			"adults"=> 2,
			"children"=> 0)
	
		),
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
		"body"=> json_encode($body)
	  
	));

	return wp_remote_retrieve_body($responseHotelsRooms);
}