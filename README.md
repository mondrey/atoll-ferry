# Atoll Ferry

Welcome to Atoll Ferry, a WordPress plugin designed to impletement Maldives public ferry schedule finder a simple shortcode. This plugin is perfect for travel websites, tour operators, and community portals that need to provide ferry information across various islands.

Working implementation
https://inmaldives.life/transport/

## Description

Atoll Ferry allows you to integrate ferry schedules into your WordPress site effortlessly. By using the `[ferryfinder]` shortcode, you can display it anywhere on your site. The plugin pulls data from a predefined array.

Island names stored with abbreviations.

```php
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

```

And the days / routes / time are set as follows.
In this it is the 301 route.

```php
$data['days']['301'] = 'Saturday, Sunday, Monday, Tuesday, Wednesday, Thursday';
$data['301'][]['HMA']['MLH'] = '6:30am,7:00am';
$data['301'][]['MLH']['FER'] = '7:05am,7:30am';
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
```

## Features

- **Shortcode Integration**: Easily integrate ferry schedules using the `[ferryfinder]` shortcode.
- **Responsive Design**: Ensures that ferry schedules look good on all devices.

## Installation

1. Download the plugin from the GitHub repository.
2. Upload the plugin files to the `/wp-content/plugins/atoll-ferry` directory, or install the plugin through the WordPress plugins screen directly.
3. Activate the plugin through the 'Plugins' screen in WordPress.
4. Add shortcode [ferryfinder] to any page.

## Usage

To display ferry schedules on your WordPress site, simply add the shortcode `[ferryfinder]` to any post, page, or widget. 

## Changelog

### 1.0
- Initial release of Atoll Ferry.

## License

This project is licensed under the GNU General Public License v2.0. For more details, see the LICENSE file included with this plugin.

## Support

For support, feature requests, or bug reporting, please visit the issues section on our GitHub repository.

Thank you for using Atoll Ferry!

