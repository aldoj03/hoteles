<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 *
 */
class Hoteles_Public
{


	private $plugin_name;

	
	private $version;

	

	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode('hot-results-page', [$this, 'hot_results_page__function']);
		add_shortcode('hot-form-request', [$this, 'form_shortcode']);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/hoteles-public.css', array(), $this->version, 'all');
	}

	
	public function enqueue_scripts()
	{


		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hoteles-public.js', array('jquery'), $this->version, false);
	}


	// results angular shortcode
	public	function hot_results_page__function()
	{

		$urlBase = plugin_dir_url(__FILE__) . 'angularApp/src/index.php#' . $_GET['id'];
		$page = '<style>
			html,body{
				overflow: hidden
			}		
		</style><iframe id="hotelesApp" title="Filtro de Hoteles" frameBorder="0"
		src="' . $urlBase . '" >
	</iframe>';

		return $page;
	}

	public	function handle_post_request()
	{

		if (isset($_POST['action']) && $_POST['action'] === 'hotels_form') {
		
			$checkIn = $_POST['entrada'];
			$checkOut = $_POST['salida'];
			$result_array = new stdClass();
			
			$result_array->hotels = $this->get_hotels_filtered_request($checkIn, $checkOut);
			
			if (isset($result_array->hotels)) {
				$hotels_array_string =  serialize($result_array->hotels);

				$post_arr = array(
					'post_content' => $hotels_array_string,
					'post_type'    => 'hoteles',

				);
				
				$id_post = wp_insert_post($post_arr,true );
				
				
				wp_redirect(rtrim(get_permalink(get_page_by_title('Resultado Hoteles'))) . '?id="' . $id_post);
			}

			wp_send_json(false);
		}
	}


	// get hoteles
	public	function get_hotels_filtered_request($query, $query2)
	{


		$apiKey = API_KEY;
		$Secret = SECRET;
		$xsignature = hash("sha256", $apiKey . $Secret . time());
		$array_ids = [];

		$response = $this->getHotelsRooms($apiKey, $xsignature, $query, $query2);
		
		$response_decoded = json_decode($response);
		
		$final_array = new stdClass();
		foreach ($response_decoded->hotels->hotels as $key => $value) {

			array_push($array_ids, $value->code);
		}
		$reponse_details = json_decode($this->getHotels_details($apiKey, $xsignature, $array_ids));
		
		$final_array->hotels = $this->commbineArrays($response_decoded, $reponse_details);
		$final_array->checkDays = new stdClass();
		$final_array->checkDays->checkIn = $response_decoded->hotels->checkIn;
		$final_array->checkDays->checkOut = $response_decoded->hotels->checkOut;
		$final_array->checkDays->total = $response_decoded->hotels->total;
		// var_dump($final_array);
		// die();
		return $final_array;
	}






	//obtiene detalles de los hoteles
	public function getHotels_details($apiKey, $xsignature, $ids)
	{

		$ids_string = implode(',', $ids);
		// wp_send_json($ids_string);

		$url = 'https://api.test.hotelbeds.com/hotel-content-api/1.0/hotels?codes=' . $ids_string . '&language=CAS&fields=ranking,description,images,boardCodes';

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
		// var_dump($getHotelsResponse);
		// die();
		return wp_remote_retrieve_body($getHotelsResponse);
	}


	//inserta detalles de hotel en primer array

	public function commbineArrays($arrayHotels, $arrayDetails)
	{
		$arrayTosend = array();


		foreach ($arrayHotels->hotels->hotels as $key => $hotel) {
			foreach ($arrayDetails->hotels as $key => $details) {
				if ($details->code == $hotel->code) {
					$hotel->description = $details->description;
					$hotel->ranking = $details->ranking;
				
					$hotel->boardCodes = $details->boardCodes;
					$hotel->images = $details->images[0];
					array_push($arrayTosend, $hotel);
				}
			}
		}

		return $arrayTosend;
	}


	//obtiene disponibilidad de habitaciones segun parametros
	public function getHotelsRooms($apiKey, $xsignature, $query, $query2)
	{


		$body = array(
			"geolocation" => array(
				"latitude" => 39.57119,
				"longitude" => 2.646633999999949,
				"radius" => 20,
				"unit" => "km"

			),
			"filter" => array(
				"maxHotels" => 70
			),

			"stay" => array(
				"checkIn" => $query,
				"checkOut" => $query2
			),
			"occupancies" => array(
				array(
					"rooms" => 2,
					"adults" => 2,
					"children" => 0
				)

			)

		);
		$responseHotelsRooms = wp_remote_post(
			'https://api.test.hotelbeds.com/hotel-api/1.0/hotels',
			array(
				'headers' => array(
					'Accept' => 'application/json',
					'Accept-Encoding' => 'application/gzip',
					'Content-Type' => 'application/json',
					'Api-key' => $apiKey,
					'X-Signature' => $xsignature
				),
				"body" => json_encode($body),
				'timeout' => 15
			)
		);
		
		if (is_array($responseHotelsRooms) && !is_wp_error($responseHotelsRooms)) {
			return wp_remote_retrieve_body($responseHotelsRooms);
		} else {
			wp_send_json(is_wp_error($responseHotelsRooms));
		}
	}


	public function form_shortcode()
	{

		// Things that you want to do.
		$checkIn = date("Y-m-d");
		$checkOut = date("Y-m-d", strtotime($checkIn . "+ 30 days"));

		$form = '
	 <form action="" method="POST" style="display: flex">
	 <input type="hidden" value="hotels_form" name="action">
	 	<div>
		 	<h4>Destino</h4>
			 <input type="text" size="25">
		 </div>
	 	<div style="display: flex;margin-left: 2vw;">
			<div>
				<h4>Entrada</h4>
				<input type="date" name="entrada" id=""  min="' . $checkIn . '" required>
			</div>
			<div style="margin-left: 2vw;">
				<h4>Salida</h4>
				<input type="date" name="salida" id="" min="' . $checkIn . '" max="' . $checkOut . '"  required>
			</div>
		 </div>
		 <div style="margin-left: 2vw;">
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
		 <div style="display: flex;align-items: center;margin-left: 2vw;">
		 	<button type="sumbmit" >Enviar</button>
		 </div>
	 </form>
	';
		return $form;
	}
}
