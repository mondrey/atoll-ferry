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

		add_action('wp_ajax_get_schedule_for_departure_island', array($this, 'get_schedule_for_departure_island_callback'));
		add_action('wp_ajax_nopriv_get_schedule_for_departure_island', array($this, 'get_schedule_for_departure_island_callback'));

	}

	public function ferry_schedule_finder() {

		$ferry_schedules = self::get_ferry_schedule();
ob_start();
?>
    <div id="speedboat-search">
        <label for="departure-island">Departure Island:</label>
        <select id="departure-island">
            <!-- Add departure islands dynamically if needed -->
            <?php
			echo self::create_select_list( $ferry_schedules );
			?>
        </select>

        <button id="search-btn">Search</button>

        <div id="results"></div>
    </div>
<?php
return ob_get_clean();

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

	public function get_schedule_for_departure_island_callback() {
		$selectedIsland = $_POST['selectedIsland'];
		global $data; // Assuming $data is a global variable containing your schedule data
	
		// Call the existing PHP function to get the schedule
		$scheduleOutput = self::get_schedule_for_departure_island($selectedIsland, $data);
	
		echo $scheduleOutput;
	
		wp_die(); // Always include this line to terminate the script
	}	

	public function get_schedule_for_departure_island($selectedIsland, $data) {
		$scheduleOutput = '';
	
		// Check if the selected island exists in the $data array
		if (array_key_exists($selectedIsland, $data)) {
			// Loop through the $data array for the selected departure island
			foreach ($data[$selectedIsland] as $destinationIsland => $schedule) {
				$daysOfWeek = key($schedule);
				$time = current($schedule);
	
				// Build the output string
				$scheduleOutput .= "Departure Island: $selectedIsland, Destination Island: $destinationIsland, Days: $daysOfWeek, Time: $time<br>";
			}
		} else {
			$scheduleOutput = "No schedule found for the selected departure island.";
		}
	
		return $scheduleOutput;
	}

	public function get_ferry_schedule() {

		$data = array();
// 301

$data['Himandhoo']['Maalhos'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,7:00am',
];

$data['Maalhos']['Feridhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:05am,7:30am',
];

$data['Feridhoo']['Mathiveri'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:35am,8:30am',
];

$data['Mathiveri']['Bodufolhudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:35am,8:55am',
];

$data['Bodufolhudhoo']['Ukulhas'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '9:00am,9:35am',
];

$data['Ukulhas']['Rasdhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '10:00am,10:55am',
];

$data['Rasdhoo']['Ukulhas'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:00pm,13:55pm',
];

$data['Ukulhas']['Bodufolhudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:00pm,14:35pm',
];

$data['Bodufolhudhoo']['Mathiveri'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:40pm,15:00pm',
];

$data['Mathiveri']['Feridhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:05pm,16:05pm',
];

$data['Feridhoo']['Maalhos'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:10pm,16:35pm',
];

$data['Maalhos']['Himandhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:40pm,17:10pm',
];


// 302


$data['Mandhoo']['Ku’nburudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,8:05am',
];

$data['Ku’nburudhoo']['Mahibadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:10am,8:30am',
];

$data['Mahibadhoo']['Hangnaameedhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:35am,9:15am',
];

$data['Hangnaameedhoo']['Omadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '9:20am,9:50am',
];

$data['Omadhoo']['Mahibadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '9:55am,10:10am',
];

$data['Mahibadhoo']['Omadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:30pm,13:45pm',
];

$data['Omadhoo']['Hangnaameedhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:50pm,14:20pm',
];

$data['Hangnaameedhoo']['Mahibadhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:25pm,15:05pm',
];

$data['Mahibadhoo']['Ku’nburudhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:10pm,15:30pm',
];

$data['Ku’nburudhoo']['Mandhoo'] = [
	'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:35pm,17:10pm',
];


// 303

$data['Thoddoo']['Rasdhoo'] = [
    'Saturday,Tuesday' => '6:30am,8:10am',
];

$data['Rasdhoo']['Thoddoo'] = [
    'Saturday,Tuesday' => '15:10pm,16:20pm',
];
		
$data['Thoddoo']['Rasdhoo'] = [
    'Sunday, Wednesday' => '6:30am,7:40am',
];

$data['Rasdhoo']['Ukulhas'] = [
    'Sunday, Wednesday' => '7:45am,8:35am',
];

$data['Ukulhas']['Rasdhoo'] = [
    'Sunday, Wednesday' => '9:45am,10:35am',
];

$data['Rasdhoo']['Male’'] = [
    'Sunday, Wednesday' => '11:00am,14:10pm',
];


$data['Male’']['Rasdhoo'] = [
    'Monday, Thursday' => '9:00am,12:10pm',
];

$data['Rasdhoo']['Ukulhas'] = [
    'Monday, Thursday' => '12:15pm,13:05pm',
];

$data['Ukulhas']['Rasdhoo'] = [
    'Monday, Thursday' => '14:00pm,14:50pm',
];

$data['Rasdhoo']['Thoddoo'] = [
    'Monday, Thursday' => '15:10pm,16:20pm',
];

// 304

$data['Fenfushi']['Maamigili'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,6:55am',
];

$data['Maamigili']['Dhidhdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:00am,7:25am',
];

$data['Dhidhdhoo']['Dhigurah'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:30am,8:00am',
];

$data['Dhigurah']['Dha’ngethi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:05am,8:35am',
];

$data['Dha’ngethi']['Mahibadhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:40am,9:40am',
];

$data['Mahibadhoo']['Dha’ngethi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '13:30pm,14:30pm',
];

$data['Dha’ngethi']['Dhigurah'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:35pm,15:05pm',
];

$data['Dhigurah']['Dhidhdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:10pm,15:40pm',
];

$data['Dhidhdhoo']['Maamigili'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:45pm,16:10pm',
];

$data['Maamigili']['Fenfushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:15pm,16:40pm',
];

// 305

$data['Male']['Mahibadhoo'] = [
    'Saturday, Monday, Wednesday' => '8:30am,12:40pm',
];
$data['Mahibadhoo']['Male'] = [
    'Sunday, Tuesday, Thursday' => '11:00am,15:20pm',
];


// 306
$data['Rakeedhoo']['Keyodhoo'] = [
    'Saturday, Monday, Wednesday' => '7:00am,8:10am',
];

$data['Keyodhoo']['Felidhoo'] = [
    'Saturday, Monday, Wednesday' => '8:20am,8:30am',
];

$data['Felidhoo']['Thinadhoo'] = [
    'Saturday, Monday, Wednesday' => '8:40am,8:50am',
];

$data['Thinadhoo']['Fulidhoo'] = [
    'Saturday, Monday, Wednesday' => '9:00am,10:30am',
];

$data['Fulidhoo']['K.Maafushi'] = [
    'Saturday, Monday, Wednesday' => '10:45am,12:25pm',
];

$data['K.Maafushi']['Male'] = [
    'Saturday, Monday, Wednesday' => '12:35pm,2:05pm',
];


$data['Male']['K.Maafushi'] = [
    'Sunday, Tuesday, Thursday' => '10:00am,11:30am',
];

$data['K.Maafushi']['Fulidhoo'] = [
    'Sunday, Tuesday, Thursday' => '11:35am,1:20pm',
];

$data['Fulidhoo']['Thinadhoo'] = [
    'Sunday, Tuesday, Thursday' => '1:30pm,3:00pm',
];

$data['Thinadhoo']['Felidhoo'] = [
    'Sunday, Tuesday, Thursday' => '3:05pm,3:15pm',
];

$data['Felidhoo']['Keyodhoo'] = [
    'Sunday, Tuesday, Thursday' => '3:20pm,3:30pm',
];

$data['Keyodhoo']['Rakeedhoo'] = [
    'Sunday, Tuesday, Thursday' => '3:35pm,4:50pm',
];

// 307
$data['Kaashidhoo']['Gaafaru'] = [
    'Saturday, Monday, Wednesday' => '7:00am,8:20am',
];

$data['Gaafaru']['Male'] = [
    'Saturday, Monday, Wednesday' => '8:30am,11:50am',
];

$data['Male']['Gaafaru'] = [
    'Sunday, Tuesday, Thursday' => '10:45am,12:25pm',
];

$data['Gaafaru']['Kaashidhoo'] = [
    'Sunday, Tuesday, Thursday' => '12:35pm,3:20pm',
];

// 308

$data['Dhiffushi']['Thulusdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,7:05am',
];

$data['Thulusdhoo']['Huraa'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:10am,7:35am',
];

$data['Huraa']['Himmafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:40am,7:55am',
];

$data['Himmafushi']['Male'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '8:00am,8:40am',
];

$data['Male']['Himmafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:30pm,15:10pm',
];

$data['Himmafushi']['Huraa'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:15pm,15:30pm',
];

$data['Huraa']['Thulusdhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:35pm,16:00pm',
];

$data['Thulusdhoo']['Dhiffushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:05pm,16:40pm',
];


// 309

$data['Guraidhoo']['Maafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:00am,7:20am',
];

$data['Maafushi']['Gulhi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:25am,7:45am',
];

$data['Gulhi']['Male'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:50am,9:05am',
];

$data['Male']['Gulhi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:00pm,16:15pm',
];

$data['Gulhi']['Maafushi'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:20pm,16:40pm',
];

$data['Maafushi']['Guraidhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '16:45pm,17:05pm',
];

// 310

$data['Feridhoo']['Maalhos'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '6:30am,6:55am',
];

$data['Maalhos']['Himandhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:00am,7:30am',
];

$data['Himandhoo']['Adh.Mahibadhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '7:35am,9:10am',
];

$data['Adh.Mahibadhoo']['Himandhoo'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '14:00pm,15:35pm',
];

$data['Himandhoo']['Maalhos'] = [
    'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday' => '15:40pm,16:10pm',
];

$data['Maalhos']['Feridhoo'] = [
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

	}

}
