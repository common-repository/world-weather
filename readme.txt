=== Plugin Name ===
Contributors: titacgs
Tags: weather, widget
Requires at least: 3.0
Tested up to: 3.1.2
Stable tag: 2.1.1

== Description ==

This plugin/widget allows you to insert weather status in your sidebar. It connects to World Weather Online to get weather forecasts. It shows the current temperature, max and min. You can choose the temperature scale, options are celsius and fahrenheit degrees. Also, you can get results from 1 to 5 days.

It has a really simple html markup:

- For one result

<div id="temp_actual">22 °C</div>
<div id="temp_min">Min 20 °C</div>
<div id="temp_max">Max 32 °C</div>

- For more than one, displays current temperature and a table

<div id="temp_actual">22 °C</div>
<table>
	<tr>
		<td width="45px">Date</td>
		<td width="45px">Min.</td>
		<td width="45px">Max.</td>
	</tr>
	<tr>
		<td>05/22</td>
		<td>Min 20 °C</td>
		<td>Max 32 °C</td>
	</tr>
	<tr>
		<td>05/23</td>
		<td>Min 23 °C</td>
		<td>Max 33 °C</td>
	</tr>
</table>


You can style it by adding the proper CSS for each id to your style.css file: temp_actual, temp_max, temp_min

<a href="http://blog.titacgs.com.ar/world-weather-plugin-para-wp/" target="_blank">Visit the plugin page</a> leave questions/comments/fixes.

Also, if you like this plugin <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=QZTRL5AJ35EHG&lc=VE&item_name=World%20Weather%20Plugin&item_number=WP%2dPlugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" target="_blank">you can donate some money!</a>

== Installation ==

1. Upload the entire world-weather folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Get an API Key from World Weather Online at http://www.worldweatheronline.com/register.aspx
3. In your Widgets menu, simply drag the widget labeled "World Weather" into a widgetized Area.
4. Configure the widget by setting a title, the API Key and the location.

== Changelog ==

= 1.0 =
* Release

= 1.1 =
* Choose temperature scale
* Fixed issues with form

= 1.2 =
* Changed API request URL

= 2.0 =
* Fixed mayor bug in widget admin display, not the plugin works!
* Added functionality, you can choose one to five days to display results.

= 2.1 =
* Fixed bug when displaying fahrenheit temperatures
* Added icon showing weather status
* Added donate button

= 2.1.1 =
* Fixed plugin info so it asks for automatical update
