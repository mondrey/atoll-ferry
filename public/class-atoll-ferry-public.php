<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Atoll_Ferry
 * @subpackage Atoll_Ferry/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Atoll_Ferry
 * @subpackage Atoll_Ferry/public
 * @author     Your Name <email@example.com>
 */
class Atoll_Ferry_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $atoll_ferry    The ID of this plugin.
	 */
	private $atoll_ferry;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $atoll_ferry       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $atoll_ferry, $version ) {

		$this->atoll_ferry = $atoll_ferry;
		$this->version = $version;

		add_shortcode('ferryfinder', array($this, 'ferry_schedule_finder'));

		// add_action('wp_ajax_get_schedule_for_departure_island', array($this, 'get_schedule_for_departure_island_callback'));
		// add_action('wp_ajax_nopriv_get_schedule_for_departure_island', array($this, 'get_schedule_for_departure_island_callback'));

		// Add this function to functions.php or your plugin file
		add_action('wp_ajax_get_interconnected_ferry_schedules', array($this,  'get_interconnected_ferry_schedules'));
		add_action('wp_ajax_nopriv_get_interconnected_ferry_schedules', array($this,  'get_interconnected_ferry_schedules'));


	}

	public function ferry_schedule_finder() {

		$ferry_schedules = self::get_ferry_schedule();

		// Example usage
		$departure = 'Male’';
		$destination = 'K.Maafushi';

		error_log( '------------------------------------');
		error_log( 'Initiating route to new destination');
		error_log( 'Departure : ' . $departure);
		error_log( 'Destination : ' . $destination);
		error_log( '------------------------------------');
		//$route = self::find_interconnected_route($departure, $destination, $ferry_schedules);
		$route = self::can_goto_islands_from($departure, $ferry_schedules);
		
		if ($route !== false) {
			return "Valid Route: " . implode(' -> ', $route);
		} else {
			return "Invalid Route: No interconnected route found from $departure to $destination";
		}

ob_start();
?>
    <div id="speedboat-search">
        <label for="island-select">Departure Island:</label>
        <select id="island-select">
            <!-- Add departure islands dynamically if needed -->
            <?php
			echo self::create_select_list( $ferry_schedules );
			?>
        </select>
        <select id="island-destination">
            <!-- Add departure islands dynamically if needed -->
            <?php
			echo self::create_select_list( $ferry_schedules );
			?>
        </select>

        <button id="search-btn">Search</button>

        <div id="schedule-output"></div>
    </div>
<?php
return ob_get_clean();

	}

	public function can_goto_islands_from($departure, $data_array) {
		//error_log( '----- the data set' );
		//error_log( print_r( $data ,true ) );

		$data_sets = ['301','302','303-1','303-2','303-3','304','305-1','305-2','306-1','306-2','307-1','307-2','308','309','310'];
		foreach ($data_sets as $data_set) {

			$route_found = false;
			$destinationIsland = false;
			$destination = 'sdkjfhkdjfsh';
			$schedule = '';
			$transfer_days = '';
			$transfer_days_prev = '';
			$transfer_day_time = '';
			$time_start = '';
			$time_end = '';
			$prev_departureIsland = '';
	
			$traceroute = array();
			$last_stop = array();

			$data = $data_array[$data_set];
			$count = 0;

			foreach ($data as $record) {
				foreach ($record as $departureIsland => $destinations) {
					if ( $route_found ) {
						$count++;
						if ( $destinationIsland == $departureIsland ) {
							$transfer_days_prev = $transfer_days;
							// error_log ( $count . ' ---- ' . $data_set . " Next Departure: $departureIsland" . ' ' . $transfer_day_time );
							$traceroute[$data_set][$count]['from']['island'] = $prev_departureIsland;
							$traceroute[$data_set][$count]['from']['time'] = $time_start;
							$traceroute[$data_set][$count]['to']['island'] = $departureIsland;
							$traceroute[$data_set][$count]['to']['time'] = $time_end;
						} else {
							$route_found = false;
							$traceroute = array();
							// error_log ( $data_set . " Route Ended without Reaching" . ' ' . $transfer_day_time );
						}
					}
					// Output departure and destination
					if ( $departure == $departureIsland ) {
						// error_log ( $count . ' ---- ' . $data_set . " Departure: $departureIsland" . ' ' . $transfer_day_time );
						// error_log ( $count . ' ---- ' . $data_set . " Route Started" . ' ' . $transfer_day_time );
						$traceroute = array();  // Reset for Multiple route checking
						//$traceroute[] = $departureIsland . '|' . $transfer_day_time;
						$route_found = true;
					}

					$prev_departureIsland = $departureIsland;
	
					foreach ($destinations as $destinationIsland => $schedule) {
						// Extract days and times
						//error_log("Current Schedule: " . print_r($schedule, true));
						if ( is_array($schedule) ) {
							foreach ( $schedule as $daysOfWeek => $timeOfDay ) {
								$transfer_days = $daysOfWeek;
								$transfer_time = $timeOfDay;

								$times = explode(',', $transfer_time);

								$time_start = trim($times[0]);
								$time_end = trim($times[1]);
							}
							$transfer_day_time = $transfer_days . ' ' . $transfer_time;
						}
	
						// Output departure and destination
						// if ( $departure == $destinationIsland ) {
						// 	error_log ( "Departure: $departureIsland, Destination: $destinationIsland\n" );
						// }
						if ( $route_found ) {
							//error_log ( "Found Island: $destinationIsland" );
							//$last_stop = $count . ' ---- ' . $data_set . " LAST STOP: $destinationIsland" . ' ' . $transfer_day_time;

							$last_stop['from']['island'] = $departureIsland;
							$last_stop['from']['time'] = $time_start;
							$last_stop['to']['island'] = $destinationIsland;
							$last_stop['to']['time'] = $time_end;
							
							if ( $destination == $destinationIsland ) {
								// error_log ( $data_set . " Destination Reached: $destinationIsland" );
								$traceroute[] = $destinationIsland . '|' . $transfer_day_time;
								$route_found = false; // Finished route
								// error_log ( print_r( $traceroute, true ) );
								$traceroute = array();
							}
						}
					}
				}
			}
			if ( $route_found ) {
				$count++;
				$traceroute[$data_set][$count]['from']['island'] = $last_stop['from']['island'];
				$traceroute[$data_set][$count]['from']['time'] = $last_stop['from']['time'];
				$traceroute[$data_set][$count]['to']['island'] = $last_stop['to']['island'];
				$traceroute[$data_set][$count]['to']['time'] = $last_stop['to']['time'];

				error_log ( print_r( $traceroute, true ) );
			}
		}
		
		return false; // No valid route found
	}

	public function find_interconnected_route($departure, $destination, $data, $visited = []) {
		//error_log( '----- the data set' );
		//error_log( print_r( $data ,true ) );

		$route_found = false;
		$destinationIsland = false;

		$traceroute = array();

		foreach ($data as $record) {
			foreach ($record as $departureIsland => $destinations) {

				if ( $route_found ) {
					if ( $destinationIsland == $departureIsland ) {
						error_log ( "Next Departure: $departureIsland" );
						$traceroute[] = $departureIsland;
					} else {
						$route_found = false;
						$traceroute = array();
						error_log ( "Route Ended without Reaching" );
					}
				}
				// Output departure and destination
				if ( $departure == $departureIsland ) {
					error_log ( "Departure: $departureIsland" );
					error_log ( "Route Started" );
					$traceroute = array();  // Reset for Multiple route checking
					$traceroute[] = $departureIsland;
					$route_found = true;
				}

				foreach ($destinations as $destinationIsland => $schedule) {
					// Extract days and times
					//[$daysOfWeek, $departureTimes] = array_map('trim', explode(' => ', current($schedule)));

					// Output departure and destination
					// if ( $departure == $destinationIsland ) {
					// 	error_log ( "Departure: $departureIsland, Destination: $destinationIsland\n" );
					// }
					if ( $route_found ) {
						if ( $destination == $destinationIsland ) {
							error_log ( "Destination Reached: $destinationIsland" );
							$traceroute[] = $destinationIsland;
							$route_found = false; // Finished route
							error_log ( print_r( $traceroute, true ) );
							$traceroute = array();
						}
					}
				}
			}
		}
		
		return false; // No valid route found
	}

	public function create_select_list($data) {
		$islands = [];
	
		// Loop through the $data array to collect unique island names
		foreach ($data as $departureIsland => $arrivalData) {
			$islands[] = $departureIsland;
			foreach ($arrivalData as $arrivalIsland => $schedule) {
				$islands[] = $arrivalIsland;
			}
		}
	
		// Remove duplicates and sort the island names
		$uniqueIslands = array_unique($islands);
		sort($uniqueIslands);
	
		$selectList = '';
		// Create a select list
		foreach ($uniqueIslands as $island) {
			$selectList .= '<option value="' . $island . '">' . $island . '</option>';
		}
	
		return $selectList;
	}

	public function get_interconnected_ferry_schedules() {
		$departureIsland = sanitize_text_field($_POST['departureIsland']);
		$destinationIsland = sanitize_text_field($_POST['destinationIsland']);
	
		$ferrySchedules = self::get_ferry_schedule(); // Replace with your actual function to retrieve ferry schedules
	
		// Call the function to get interconnected ferry schedules
		$interconnectedSchedules = self::get_interconnected_schedules($departureIsland, $destinationIsland, $ferrySchedules);
	
		// Output the interconnected schedules
		echo $interconnectedSchedules;
	
		wp_die(); // Always include this at the end to terminate the script
	}
	
	public function get_interconnected_schedules($departureIsland, $destinationIsland, $ferrySchedules) {
		// Implement the logic to find interconnected schedules
		$schedules = '';
	
		// Check if the departure and destination islands exist in the ferry schedules
		if (array_key_exists($departureIsland, $ferrySchedules) && array_key_exists($destinationIsland, $ferrySchedules)) {
			// Call the function to find interconnected schedules
			$interconnectedIslands = self::find_interconnected_islands($departureIsland, $destinationIsland, $ferrySchedules, []);
	
			// Format the output string
			foreach ($interconnectedIslands as $island) {
				$schedules .= "Island: $island<br>"; // You can add more details here based on your needs
			}
		} else {
			$schedules = "Invalid departure or destination island.";
		}
	
		return $schedules;
	}
	
	public function find_interconnected_islands($currentIsland, $destinationIsland, $ferrySchedules, $visited) {
		$visited[] = $currentIsland;
	
		// Check if the current island is the destination
		if ($currentIsland === $destinationIsland) {
			return $visited;
		}
	
		// Check if the current island has interconnected islands
		if (isset($ferrySchedules[$currentIsland])) {
			foreach ($ferrySchedules[$currentIsland] as $interconnectedIsland => $schedule) {
				if (!in_array($interconnectedIsland, $visited)) {
					// Recursively call the function for interconnected islands
					$visited = self::find_interconnected_islands($interconnectedIsland, $destinationIsland, $ferrySchedules, $visited);
				}
			}
		}
	
		return $visited;
	}
	

	public function get_schedule_for_departure_island_callback() {
		$selectedIsland = $_POST['selectedIsland'];
		$ferry_schedule_data = self::get_ferry_schedule();
	
		// Call the existing PHP function to get the schedule
		$scheduleOutput = self::get_schedule_for_departure_island($selectedIsland, $ferry_schedule_data);
		$scheduleOutput .= '<hr/>';
		//$scheduleOutput .= self::get_schedule_for_destination_island($selectedIsland, $ferry_schedule_data);
	
		echo $scheduleOutput;
	
		wp_die(); // Always include this line to terminate the script
	}

	public function get_schedule_for_destination_island($selectedIsland, $data) {
		$scheduleOutput = '';
	
		// Loop through the $data array to find schedules where the selected island is the destination
		foreach ($data as $departureIsland => $destinations) {
			foreach ($destinations as $destinationIsland => $schedule) {
				if ($destinationIsland === $selectedIsland) {
					$daysOfWeek = key($schedule);
					$time = current($schedule);
	
					// Build the output string
					$scheduleOutput .= "Departure Island: $departureIsland, Destination Island: $destinationIsland, Days: $daysOfWeek, Time: $time<br>";
				}
			}
		}
	
		// Check if any schedule was found
		if (empty($scheduleOutput)) {
			$scheduleOutput = "No schedule found for the selected destination island.";
		}
	
		return $scheduleOutput;
	}	

	public function get_schedule_for_departure_island($selectedIsland, $data) {
		$scheduleOutput = '';
	
		// Check if the selected island exists in the $data array
		if (array_key_exists($selectedIsland, $data)) {
			// Loop through the $data array for the selected departure island

			foreach ($data[$selectedIsland] as $destinationIsland => $schedule) {
				$daysOfWeek = key($schedule);
				$times = explode(',', current($schedule));

                // Check if both departure and arrival times are present
                if (count($times) === 2) {
                    $departureTime = trim($times[0]);
                    $arrivalTime = trim($times[1]);

					// Build the output string
					$scheduleOutput .= '<div class="schedule-islands"><span class="depature-island">'.$selectedIsland . '</span><i class="fa-solid fa-arrow-right-long"></i><span class="arrival-island">' . $destinationIsland . '</span></div>';
					$scheduleOutput .= '<p>'.$daysOfWeek . '</p>';
					$scheduleOutput .= '<p>Departure from ' .$selectedIsland .' at '. $departureTime . '</p>';
					$scheduleOutput .= '<p>Arrival to ' . $destinationIsland . ' at ' . $arrivalTime . '</p>';
					$scheduleOutput .= '<hr/>';

                } else {
                    $scheduleOutput .= "Invalid time format for $departureIsland to $destinationIsland schedule.<br>";
                }
			}
		} else {
			$scheduleOutput = "No schedule found for the selected departure island.";
		}
	
		return $scheduleOutput;
	}

	public function get_ferry_schedule() {

		$data = array();
// 301

$data['301'][]['Himandhoo']['Maalhos'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,7:00am',
];

$data['301'][]['Maalhos']['Feridhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:05am,7:30am',
];

$data['301'][]['Feridhoo']['Mathiveri'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:35am,8:30am',
];

$data['301'][]['Mathiveri']['Bodufolhudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:35am,8:55am',
];

$data['301'][]['Bodufolhudhoo']['Ukulhas'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '9:00am,9:35am',
];

$data['301'][]['Ukulhas']['Rasdhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '10:00am,10:55am',
];

$data['301'][]['Rasdhoo']['Ukulhas'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:00pm,13:55pm',
];

$data['301'][]['Ukulhas']['Bodufolhudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:00pm,14:35pm',
];

$data['301'][]['Bodufolhudhoo']['Mathiveri'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:40pm,15:00pm',
];

$data['301'][]['Mathiveri']['Feridhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:05pm,16:05pm',
];

$data['301'][]['Feridhoo']['Maalhos'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:10pm,16:35pm',
];

$data['301'][]['Maalhos']['Himandhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:40pm,17:10pm',
];


// 302


$data['302'][]['Mandhoo']['Ku’nburudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,8:05am',
];

$data['302'][]['Ku’nburudhoo']['Mahibadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:10am,8:30am',
];

$data['302'][]['Mahibadhoo']['Hangnaameedhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:35am,9:15am',
];

$data['302'][]['Hangnaameedhoo']['Omadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '9:20am,9:50am',
];

$data['302'][]['Omadhoo']['Mahibadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '9:55am,10:10am',
];

$data['302'][]['Mahibadhoo']['Omadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:30pm,13:45pm',
];

$data['302'][]['Omadhoo']['Hangnaameedhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:50pm,14:20pm',
];

$data['302'][]['Hangnaameedhoo']['Mahibadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:25pm,15:05pm',
];

$data['302'][]['Mahibadhoo']['Ku’nburudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:10pm,15:30pm',
];

$data['302'][]['Ku’nburudhoo']['Mandhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:35pm,17:10pm',
];


// 303

$data['303-1'][]['Thoddoo']['Rasdhoo'] = [
    'Saturday,Tuesday' => '6:30am,8:10am',
];

$data['303-1'][]['Rasdhoo']['Thoddoo'] = [
    'Saturday,Tuesday' => '15:10pm,16:20pm',
];
		
$data['303-2'][]['Thoddoo']['Rasdhoo'] = [
    'Sunday, Wednesday' => '6:30am,7:40am',
];

$data['303-2'][]['Rasdhoo']['Ukulhas'] = [
    'Sunday, Wednesday' => '7:45am,8:35am',
];

$data['303-2'][]['Ukulhas']['Rasdhoo'] = [
    'Sunday, Wednesday' => '9:45am,10:35am',
];

$data['303-2'][]['Rasdhoo']['Male’'] = [
    'Sunday, Wednesday' => '11:00am,14:10pm',
];


$data['303-3'][]['Male’']['Rasdhoo'] = [
    'Monday, Thursday' => '9:00am,12:10pm',
];

$data['303-3'][]['Rasdhoo']['Ukulhas'] = [
    'Monday, Thursday' => '12:15pm,13:05pm',
];

$data['303-3'][]['Ukulhas']['Rasdhoo'] = [
    'Monday, Thursday' => '14:00pm,14:50pm',
];

$data['303-3'][]['Rasdhoo']['Thoddoo'] = [
    'Monday, Thursday' => '15:10pm,16:20pm',
];

// 304

$data['304'][]['Fenfushi']['Maamigili'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,6:55am',
];

$data['304'][]['Maamigili']['Dhidhdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:00am,7:25am',
];

$data['304'][]['Dhidhdhoo']['Dhigurah'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:30am,8:00am',
];

$data['304'][]['Dhigurah']['Dha’ngethi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:05am,8:35am',
];

$data['304'][]['Dha’ngethi']['Mahibadhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:40am,9:40am',
];

$data['304'][]['Mahibadhoo']['Dha’ngethi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:30pm,14:30pm',
];

$data['304'][]['Dha’ngethi']['Dhigurah'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:35pm,15:05pm',
];

$data['304'][]['Dhigurah']['Dhidhdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:10pm,15:40pm',
];

$data['304'][]['Dhidhdhoo']['Maamigili'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:45pm,16:10pm',
];

$data['304'][]['Maamigili']['Fenfushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:15pm,16:40pm',
];

// 305

$data['305-1'][]['Male’']['Mahibadhoo'] = [
    'Saturday, Monday, Wednesday' => '8:30am,12:40pm',
];
$data['305-2'][]['Mahibadhoo']['Male’'] = [
    'Sunday, Tuesday, Thursday' => '11:00am,15:20pm',
];


// 306
$data['306-1'][]['Rakeedhoo']['Keyodhoo'] = [
    'Saturday, Monday, Wednesday' => '7:00am,8:10am',
];

$data['306-1'][]['Keyodhoo']['Felidhoo'] = [
    'Saturday, Monday, Wednesday' => '8:20am,8:30am',
];

$data['306-1'][]['Felidhoo']['Thinadhoo'] = [
    'Saturday, Monday, Wednesday' => '8:40am,8:50am',
];

$data['306-1'][]['Thinadhoo']['Fulidhoo'] = [
    'Saturday, Monday, Wednesday' => '9:00am,10:30am',
];

$data['306-1'][]['Fulidhoo']['K.Maafushi'] = [
    'Saturday, Monday, Wednesday' => '10:45am,12:25pm',
];

$data['306-1'][]['K.Maafushi']['Male’'] = [
    'Saturday, Monday, Wednesday' => '12:35pm,2:05pm',
];


$data['306-2'][]['Male’']['K.Maafushi'] = [
    'Sunday, Tuesday, Thursday' => '10:00am,11:30am',
];

$data['306-2'][]['K.Maafushi']['Fulidhoo'] = [
    'Sunday, Tuesday, Thursday' => '11:35am,1:20pm',
];

$data['306-2'][]['Fulidhoo']['Thinadhoo'] = [
    'Sunday, Tuesday, Thursday' => '1:30pm,3:00pm',
];

$data['306-2'][]['Thinadhoo']['Felidhoo'] = [
    'Sunday, Tuesday, Thursday' => '3:05pm,3:15pm',
];

$data['306-2'][]['Felidhoo']['Keyodhoo'] = [
    'Sunday, Tuesday, Thursday' => '3:20pm,3:30pm',
];

$data['306-2'][]['Keyodhoo']['Rakeedhoo'] = [
    'Sunday, Tuesday, Thursday' => '3:35pm,4:50pm',
];

// 307
$data['307-1'][]['Kaashidhoo']['Gaafaru'] = [
    'Saturday, Monday, Wednesday' => '7:00am,8:20am',
];

$data['307-1']['Gaafaru']['Male’'] = [
    'Saturday, Monday, Wednesday' => '8:30am,11:50am',
];

$data['307-2'][]['Male’']['Gaafaru'] = [
    'Sunday, Tuesday, Thursday' => '10:45am,12:25pm',
];

$data['307-2'][]['Gaafaru']['Kaashidhoo'] = [
    'Sunday, Tuesday, Thursday' => '12:35pm,3:20pm',
];

// 308

$data['308'][]['Dhiffushi']['Thulusdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,7:05am',
];

$data['308'][]['Thulusdhoo']['Huraa'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:10am,7:35am',
];

$data['308'][]['Huraa']['Himmafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:40am,7:55am',
];

$data['308'][]['Himmafushi']['Male’'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:00am,8:40am',
];

$data['308'][]['Male’']['Himmafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:30pm,15:10pm',
];

$data['308'][]['Himmafushi']['Huraa'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:15pm,15:30pm',
];

$data['308'][]['Huraa']['Thulusdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:35pm,16:00pm',
];

$data['308'][]['Thulusdhoo']['Dhiffushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:05pm,16:40pm',
];


// 309

$data['309'][]['Guraidhoo']['K.Maafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:00am,7:20am',
];

$data['309'][]['K.Maafushi']['Gulhi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:25am,7:45am',
];

$data['309'][]['Gulhi']['Male’'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:50am,9:05am',
];

$data['309'][]['Male’']['Gulhi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:00pm,16:15pm',
];

$data['309'][]['Gulhi']['K.Maafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:20pm,16:40pm',
];

$data['309'][]['K.Maafushi']['Guraidhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:45pm,17:05pm',
];

// 310

$data['310'][]['Feridhoo']['Maalhos'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,6:55am',
];

$data['310'][]['Maalhos']['Himandhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:00am,7:30am',
];

$data['310'][]['Himandhoo']['Adh.Mahibadhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:35am,9:10am',
];

$data['310'][]['Adh.Mahibadhoo']['Himandhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:00pm,15:35pm',
];

$data['310'][]['Himandhoo']['Maalhos'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:40pm,16:10pm',
];

$data['310'][]['Maalhos']['Feridhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:15pm,16:40pm',
];

return $data;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Atoll_Ferry_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Atoll_Ferry_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->atoll_ferry, plugin_dir_url( __FILE__ ) . 'css/atoll-ferry-public.css', array(), $this->version, 'all' );

        wp_register_style('fontawesome-6', plugin_dir_url(__FILE__) . 'css/fonts/fontawesome-free-6.4.0-web/css/fontawesome.css', false, 'screen');
        wp_register_style('fontawesome-6-brands', plugin_dir_url(__FILE__) . 'css/fonts/fontawesome-free-6.4.0-web/css/all.css', false, 'screen');
        wp_register_style('fontawesome-6-solid', plugin_dir_url(__FILE__) . 'css/fonts/fontawesome-free-6.4.0-web/css/solid.css', false, 'screen');

		wp_enqueue_style('fontawesome-6');
		wp_enqueue_style('fontawesome-6-brands');
		wp_enqueue_style('fontawesome-6-solid');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Atoll_Ferry_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Atoll_Ferry_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->atoll_ferry, plugin_dir_url( __FILE__ ) . 'js/atoll-ferry-public.js', array( 'jquery' ), $this->version, false );
		// Pass the PHP variables to JavaScript
		wp_localize_script($this->atoll_ferry, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

	}

}
