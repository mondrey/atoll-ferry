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

		add_action('wp_ajax_get_all_schedules_for_departure_island', array($this, 'get_all_schedules_for_departure_island'));
		add_action('wp_ajax_nopriv_get_all_schedules_for_departure_island', array($this, 'get_all_schedules_for_departure_island'));

		// Add this function to functions.php or your plugin file
		// add_action('wp_ajax_get_interconnected_ferry_schedules', array($this,  'get_interconnected_ferry_schedules'));
		//add_action('wp_ajax_nopriv_get_interconnected_ferry_schedules', array($this,  'get_interconnected_ferry_schedules'));


	}

	public function get_disclaimer() {
		$disclaimer = 'Disclaimer:';
		$disclaimer .= '<p>The information provided for public ferry schedules is sourced from the MTCC (Maldives Transport and Contracting Company) website. Although we have taken measures to ensure data accuracy, please note that schedules may be subject to change, and it is advisable to check with MTCC for the most up-to-date information.</p>';
		$disclaimer .= '<p>Source PDF: <a href="https://mtcc.mv/wp-content/uploads/2022/05/CTN-Ferry-Schedule-Effective-15.05.2022-K.And-Aa.-Adh.V.pdf" target="_blank">MTCC Ferry Schedule PDF</a> ( 15th March 2022 )</p>';
		$disclaimer .= '<p>Source Website: <a href="https://mtcc.mv/" target="_blank">MTCC</a></p>';
		return $disclaimer;
	}

	public function ferry_schedule_finder() {

		$ferry_schedules = self::get_ferry_schedule();

		// Example usage
		$departure = 'Male’';
		$destination = 'Maafushi';

		error_log( '------------------------------------');
		error_log( 'Initiating route to new destination');
		error_log( 'Departure : ' . $departure);
		error_log( 'Destination : ' . $destination);
		error_log( '------------------------------------');
		//$route = self::find_interconnected_route($departure, $destination, $ferry_schedules);
		$can_go = self::can_goto_islands_from($departure, $ferry_schedules);

		error_log( '------------------------------------');
		error_log( '----------------Can Go Array --------------------');
		error_log ( print_r( $can_go, true ) );

		$can_goto_islands = self::list_all_islands_in_can_goto_array( $can_go);
		error_log( '------------------------------------');
		error_log( '----------------Can Go List --------------------');
		error_log( print_r( $can_goto_islands, true ) );

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
		<div class="loader-ellipsis-wrap">
			<div class="loader-ellipsis-wrap-inner">
				<div class="loader-ellipsis"><div></div><div></div><div></div><div></div></div>
			</div>
		</div>

        <div id="schedule-output"></div>
		<div class="disclaimer-wrap">
			<?php echo self::get_disclaimer(); ?>
<?php
		// echo self::visual_can_goto_array( $can_go, $destination );
?>
    </div>
<?php
return ob_get_clean();

	}

	public function get_island_names() {
		$islands = array(
			'DGU' => 'Dhigurah',
			'DHS' => 'Dhiffushi',
			'MMI' => 'Maamigili',
			'MLH' => 'Maalhos',
			'HMA' => 'Himandhoo',
			'HIM' => 'Himmafushi',
			'MAA' => 'Maafushi',
			'FER' => 'Feridhoo',
			'MAT' => 'Mathiveri',
			'BOD' => 'Bodufolhudhoo',
			'UKU' => 'Ukulhas',
			'RAS' => 'Rasdhoo',
			'MAN' => 'Mandhoo',
			'KUN' => 'Ku’nburudhoo',
			'MAH' => 'Mahibadhoo',
			'HAN' => 'Hangnaameedhoo',
			'OMA' => 'Omadhoo',
			'THO' => 'Thoddoo',
			'MAL' => 'Male’',
			'FEN' => 'Fenfushi',
			'DHI' => 'Dhidhdhoo',
			'DHA' => 'Dha’ngethi',
			'RAK' => 'Rakeedhoo',
			'KEY' => 'Keyodhoo',
			'FEL' => 'Felidhoo',
			'VTH' => 'V.Thinadhoo',
			'FUL' => 'Fulidhoo',
			'KAA' => 'Kaashidhoo',
			'GAA' => 'Gaafaru',
			'THU' => 'Thulusdhoo',
			'HUR' => 'Huraa',
			'GUR' => 'Guraidhoo',
			'GUL' => 'Gulhi',
			'ADM' => 'Adh.Mahibadhoo'
		);

		return $islands;
	}

	public function data_sets() {
		$data_sets = ['301','302','303-1','303-2','303-3','304','305-1','305-2','306-1','306-2','307-1','307-2','308','309','310'];

		return $data_sets;
	}

	public function visual_can_goto_array( $can_go, $destination ) {

		$route = '';

		$ferry_schedules = self::get_ferry_schedule();
		$got_island_names = self::get_island_names();

		$can_goto_islands = self::list_all_islands_in_can_goto_array( $can_go) ;

		$route .= '<div id="main-island-filter">';
		$route .= '<h6>Ferry destinations</h6>';

		
		
		foreach ( $can_goto_islands as $index => $uniqueIslands ) {
			$starting_point_class = '';
			if ( $destination == $uniqueIslands ) {
				$starting_point_class = " departure-filter";
			}
			$route .= '<span data-island="'.$uniqueIslands.'" class="island-filters island-filter-'.$uniqueIslands.$starting_point_class.'">'. $got_island_names[ $uniqueIslands ].'</span>';
		}
		$route .= '</div>';
		
		foreach ($can_go as $index => $schedule) {
			$count = 0;

			$island_names = '';
			$filter_islands = self::list_all_islands_in_schedule_array( $schedule ) ;

			foreach ( $filter_islands as $uniqueIslands ) {
				$island_names .= ' filter-' . $uniqueIslands;
			}

			$route .= '<div class="interconnected-route '. $island_names . '">';
			if ( isset( $ferry_schedules['days'][$index] ) ) {
				$route .= '<div class="interconnected-route-days">';
				$route .= $ferry_schedules['days'][$index];
				$route .= '</div>';
			}

			$route .= '<div class="interconnected-route-path-wrap">';
			foreach ($schedule as $record) {
				$fromIsland = $record['from']['island'];
				$fromTime = $record['from']['time'];
				$toIsland = $record['to']['island'];
				$toTime = $record['to']['time'];

				$count++;

				$destination_mark = '';
				if ( $destination == $toIsland ) {
					$destination_mark = 'destination-mark';
				}

				$arrow = '<i class="fa-solid fa-arrow-right-long"></i>';
				$arrow = '<span class="right-arrow"></span>';

				if ( 1 == $count ) {
					$route .= '<span class="from-point-of-departure">';
				}
				$route .= '<span class="from-island '. $destination_mark .'"><span class="from-time">'. $fromTime .'</span>';
				$route .= '<span class="from-island-name tag-island tag-'.$fromIsland.'">'. $got_island_names[ $fromIsland ].'</span>';
				$route .= '</span>';

				$route .= '</span>';
				$route .= $arrow;
				$route .= '<span class="up-to-arrow">';
				$route .= '<span class="to-island '. $destination_mark .'"><span class="to-time">'. $toTime .'</span>';
				$route .= '<span class="to-island-name tag-island tag-'.$toIsland.'">'. $got_island_names[ $toIsland ].'</span>';
				$route .= '</span>';

			}
			$route .= '</span>';
			$route .= '<div class="scroll-indicator">';
			$route .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"></path></svg>';
			$route .= '</div>';
			$route .= '</div>';
			$route .= '</div>';
		}

		return $route;
	}

	public function list_all_islands_in_schedule_array($schedule) {

		$uniqueIslands = array();

		foreach ($schedule as $record) {
			$fromIsland = $record['from']['island'];
			$toIsland = $record['to']['island'];

			// Add from and to islands to the unique islands list
			$uniqueIslands[$fromIsland] = true;
			$uniqueIslands[$toIsland] = true;
		}

		// Get the list of unique island names
		$uniqueIslandNames = array_keys($uniqueIslands);

		// Print the result
		return $uniqueIslandNames;
	}

	public function list_all_islands_in_can_goto_array($data_array) {

		$uniqueIslands = array();

		foreach ($data_array as $schedule) {
			foreach ($schedule as $record) {
				$fromIsland = $record['from']['island'];
				$toIsland = $record['to']['island'];

				// Add from and to islands to the unique islands list
				$uniqueIslands[$fromIsland] = true;
				$uniqueIslands[$toIsland] = true;
			}
		}

		// Get the list of unique island names
		$uniqueIslandNames = array_keys($uniqueIslands);

		// Print the result
		return $uniqueIslandNames;
	}

	public function can_goto_islands_from($departure, $data_array) {
		//error_log( '----- the data set' );
		//error_log( print_r( $data ,true ) );

		$data_sets = self::data_sets();
		$traceroute = array();
		foreach ($data_sets as $data_set) {

			$route_found = false;
			$destinationIsland = false;
			$destination = '';
			$schedule = '';
			$transfer_days = '';
			$time_start = '';
			$time_end = '';
			$prev_departureIsland = '';
			$last_stop = array();

			$data = $data_array[$data_set];
			$count = 0;

			foreach ($data as $record) {
				foreach ($record as $departureIsland => $destinations) {
					if ( $route_found ) {
						$count++;
						if ( $destinationIsland == $departureIsland ) {
							// error_log ( $count . ' ---- ' . $data_set . " Next Departure: $departureIsland" . ' ' . $transfer_day_time );
							$traceroute[$data_set][$count]['from']['island'] = $prev_departureIsland;
							$traceroute[$data_set][$count]['from']['time'] = $time_start;
							$traceroute[$data_set][$count]['to']['island'] = $departureIsland;
							$traceroute[$data_set][$count]['to']['time'] = $time_end;
						} else {
							$route_found = false;
							//$traceroute = array();
							// error_log ( $data_set . " Route Ended without Reaching" . ' ' . $transfer_day_time );
						}
					}
					// Output departure and destination
					if ( $departure == $departureIsland ) {
						// error_log ( $count . ' ---- ' . $data_set . " Departure: $departureIsland" . ' ' . $transfer_day_time );
						// error_log ( $count . ' ---- ' . $data_set . " Route Started" . ' ' . $transfer_day_time );
						//$traceroute = array();  // Reset for Multiple route checking
						//$traceroute[] = $departureIsland . '|' . $transfer_day_time;
						$route_found = true;
					}

					$prev_departureIsland = $departureIsland;
	
					foreach ($destinations as $destinationIsland => $transfer_time) {
						// Extract days and times
						//error_log("Current Schedule: " . print_r($schedule, true));
						$times = explode(',', $transfer_time);

						$time_start = trim($times[0]);
						$time_end = trim($times[1]);
	
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
			}
		}
		
		return $traceroute; // No valid route found
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

	public function create_select_list($data_array) {
		$islands = [];
	
		$data_sets = self::data_sets();
		$got_island_names = self::get_island_names();

		foreach ($data_sets as $data_set) {

			$data = $data_array[$data_set];
			// Loop through the $data array to collect unique island names
			foreach ($data as $departureIsland => $arrivalData) {
				//$islands[] = $departureIsland;
				foreach ($arrivalData as $arrivalIsland => $schedule) {
					$islands[] = $arrivalIsland;
				}
			}
		}
		
		// Remove duplicates and sort the island names
		$uniqueIslands = array_unique($islands);
		sort($uniqueIslands);
	
		$selectList = '';
		// Create a select list
		$selectList .= '<option value="none">' . __('Choose depature island') . '</option>';
		foreach ($uniqueIslands as $island) {
			$selectList .= '<option value="' . $island . '">' . $got_island_names[ $island ] . '</option>';
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
				$schedules .= "Island: $island<br>";
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
	

	public function get_all_schedules_for_departure_island() {
		$selectedIsland = $_POST['selectedIsland'];
	
		// Call the existing PHP function to get the schedule
		//$scheduleOutput = self::get_schedule_for_departure_island($selectedIsland, $ferry_schedule_data);
		//$scheduleOutput .= '<hr/>';
		//$scheduleOutput .= self::get_schedule_for_destination_island($selectedIsland, $ferry_schedule_data);

		$ferry_schedules = self::get_ferry_schedule();
		$can_go = self::can_goto_islands_from($selectedIsland, $ferry_schedules);
		$scheduleOutput = self::visual_can_goto_array( $can_go, $selectedIsland );;
	
		echo $scheduleOutput;
	
		wp_die();
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
$data['days']['301'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['301'][]['HMA']['MLH'] = '6:30am,7:00am';
$data['301'][]['MLH']['FER'] =  '7:05am,7:30am';
$data['301'][]['FER']['MAT'] = '7:35am,8:30am';
$data['301'][]['MAT']['BOD'] = '8:35am,8:55am';
$data['301'][]['BOD']['UKU'] = '9:00am,9:35am';
$data['301'][]['UKU']['RAS'] = '10:00am,10:55am';
$data['301'][]['RAS']['UKU'] = '13:00pm,13:55pm';
$data['301'][]['UKU']['BOD'] = '14:00pm,14:35pm';
$data['301'][]['BOD']['MAT'] = '14:40pm,15:00pm';
$data['301'][]['MAT']['FER'] = '15:05pm,16:05pm';
$data['301'][]['FER']['MLH'] = '16:10pm,16:35pm';
$data['301'][]['MLH']['HMA'] = '16:40pm,17:10pm';


// 302

$data['days']['302'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['302'][]['MAN']['KUN'] = '6:30am,8:05am';
$data['302'][]['KUN']['MAH'] = '8:10am,8:30am';
$data['302'][]['MAH']['HAN'] = '8:35am,9:15am';
$data['302'][]['HAN']['OMA'] = '9:20am,9:50am';
$data['302'][]['OMA']['MAH'] = '9:55am,10:10am';
$data['302'][]['MAH']['OMA'] = '13:30pm,13:45pm';
$data['302'][]['OMA']['HAN'] = '13:50pm,14:20pm';
$data['302'][]['HAN']['MAH'] = '14:25pm,15:05pm';
$data['302'][]['MAH']['KUN'] = '15:10pm,15:30pm';
$data['302'][]['KUN']['MAN'] = '15:35pm,17:10pm';

// 303
$data['days']['303-1'] = 'Saturday,Tuesday';
$data['303-1'][]['THO']['RAS'] = '6:30am,8:10am';
$data['303-1'][]['RAS']['THO'] = '15:10pm,16:20pm';

$data['days']['303-2'] = 'Sunday, Wednesday';
$data['303-2'][]['THO']['RAS'] = '6:30am,7:40am';
$data['303-2'][]['RAS']['UKU'] = '7:45am,8:35am';
$data['303-2'][]['UKU']['RAS'] = '9:45am,10:35am';
$data['303-2'][]['RAS']['MAL'] = '11:00am,14:10pm';

$data['days']['303-3'] = 'Monday, Thursday';
$data['303-3'][]['MAL']['RAS'] = '9:00am,12:10pm';
$data['303-3'][]['RAS']['UKU'] = '12:15pm,13:05pm';
$data['303-3'][]['UKU']['RAS'] = '14:00pm,14:50pm';
$data['303-3'][]['RAS']['THO'] = '15:10pm,16:20pm';
// 304
$data['days']['304'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['304'][]['FEN']['MMI'] = '6:30am,6:55am';
$data['304'][]['MMI']['DHI'] = '7:00am,7:25am';
$data['304'][]['DHI']['DGU'] = '7:30am,8:00am';
$data['304'][]['DGU']['DHA'] = '8:05am,8:35am';
$data['304'][]['DHA']['MAH'] = '8:40am,9:40am';
$data['304'][]['MAH']['DHA'] = '13:30pm,14:30pm';
$data['304'][]['DHA']['DGU'] = '14:35pm,15:05pm';
$data['304'][]['DGU']['DHI'] = '15:10pm,15:40pm';
$data['304'][]['DHI']['MMI'] = '15:45pm,16:10pm';
$data['304'][]['MMI']['FEN'] = '16:15pm,16:40pm';
// 305
$data['days']['305-1'] = 'Saturday, Monday, Wednesday';
$data['305-1'][]['MAL']['MAH'] = '8:30am,12:40pm';
$data['305-2'][]['MAH']['MAL'] = '11:00am,15:20pm';

// 306
$data['days']['306-1'] = 'Saturday, Monday, Wednesday';
$data['306-1'][]['RAK']['KEY'] = '7:00am,8:10am';
$data['306-1'][]['KEY']['FEL'] = '8:20am,8:30am';
$data['306-1'][]['FEL']['VTH'] = '8:40am,8:50am';
$data['306-1'][]['VTH']['FUL'] = '9:00am,10:30am';
$data['306-1'][]['FUL']['MAA'] = '10:45am,12:25pm';
$data['306-1'][]['MAA']['MAL'] = '12:35pm,2:05pm';

$data['days']['306-2'] = 'Sunday, Tuesday, Thursday';
$data['306-2'][]['MAL']['MAA'] = '10:00am,11:30am';
$data['306-2'][]['MAA']['FUL'] = '11:35am,1:20pm';
$data['306-2'][]['FUL']['VTH'] = '1:30pm,3:00pm';
$data['306-2'][]['VTH']['FEL'] = '3:05pm,3:15pm';
$data['306-2'][]['FEL']['KEY'] = '3:20pm,3:30pm';
$data['306-2'][]['KEY']['RAK'] = '3:35pm,4:50pm';
// 307
$data['days']['307-1'] = 'Saturday, Monday, Wednesday';
$data['307-1'][]['KAA']['GAA'] = '7:00am,8:20am';
$data['307-1'][]['GAA']['MAL'] = '8:30am,11:50am';
$days['307-2'] = 'Sunday, Tuesday, Thursday';
$data['307-2'][]['MAL']['GAA'] = '10:45am,12:25pm';
$data['307-2'][]['GAA']['KAA'] = '12:35pm,3:20pm';
// 308
$data['days']['308'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['308'][]['DHS']['THU'] = '6:30am,7:05am';
$data['308'][]['THU']['HUR'] = '7:10am,7:35am';
$data['308'][]['HUR']['HIM'] = '7:40am,7:55am';
$data['308'][]['HIM']['MAL'] = '8:00am,8:40am';
$data['308'][]['MAL']['HIM'] = '14:30pm,15:10pm';
$data['308'][]['HIM']['HUR'] = '15:15pm,15:30pm';
$data['308'][]['HUR']['THU'] = '15:35pm,16:00pm';
$data['308'][]['THU']['DHS'] = '16:05pm,16:40pm';

// 309
$data['days']['309'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['309'][]['GUR']['MAA'] = '7:00am,7:20am';
$data['309'][]['MAA']['GUL'] = '7:25am,7:45am';
$data['309'][]['GUL']['MAL'] = '7:50am,9:05am';
$data['309'][]['MAL']['GUL'] = '15:00pm,16:15pm';
$data['309'][]['GUL']['MAA'] = '16:20pm,16:40pm';
$data['309'][]['MAA']['GUR'] = '16:45pm,17:05pm';
// 310
$data['days']['310'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['310'][]['FER']['MLH'] = '6:30am,6:55am';
$data['310'][]['MLH']['HMA'] = '7:00am,7:30am';
$data['310'][]['HMA']['ADM'] = '7:35am,9:10am';
$data['310'][]['ADM']['HMA'] = '14:00pm,15:35pm';
$data['310'][]['HMA']['MLH'] = '15:40pm,16:10pm';
$data['310'][]['MLH']['FER'] = '16:15pm,16:40pm';
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

		wp_enqueue_script('velocity', plugin_dir_url(__FILE__) . 'js/velocity.min.js', array('jquery'), null, true);
		wp_enqueue_script('velocity-ui', plugin_dir_url(__FILE__) . 'js/velocity.ui.js', array('jquery'), null, true);

		wp_register_script('select2', plugin_dir_url(__FILE__) . 'js/select2/js/select2.full.min.js', array('jquery'), null, true);
        wp_register_style('select2', plugin_dir_url(__FILE__) . 'js/select2/css/select2.min.css', array(), false, 'screen');

		wp_enqueue_script('select2');
		wp_enqueue_style('select2');

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
