<?php
/*
Plugin Name: Trade rates - Realtime
Plugin URI: http://eulands.com
Description: This is a trade rates widget for wordpress. Place [wp_forex_rates] in the content to insert into a post.
Author: euLands.com
Version: 1.1.4
Author URI: http://www.eulands.com
License: GNU GPL see http://www.gnu.org/licenses/licenses.html#GPL
*/
class wp_forex_rates {
	
	function forex_rates_init() {
		$class_name = 'wp_forex_rates';
		$calc_title = 'Trade rates - Realtime';
		$source = 'wss://demo-quotes.jfdbrokers.com';
		$quote1 = 'quote 1';
		$quote2 = 'quote 2';
		$quote3 = 'quote 3';
		$quote4 = 'quote 4';
		$quote5 = 'quote 5';
		$calc_credit = 0;
		$calc_desc = 'Trade rates realtime.';
		
		if (!function_exists('wp_register_sidebar_widget')) return;
		
		wp_register_sidebar_widget(
			$class_name,
			$calc_title,
			array($class_name, 'forex_rates_widget'),
			array(
				'classname' => $class_name,
				'description' => $calc_desc,
				'source' => $source ,
				'quote1' => $quote1 ,
				'quote2' => $quote2 ,
				'quote3' => $quote3 ,
				'quote4' => $quote4 ,
				'quote5' => $quote5 
			)
		);
		
		wp_register_widget_control(
			$class_name,
			$calc_title,
			array($class_name, 'forex_rates_control'),
		    array('width' => '100%')
		);
		
		add_shortcode(
			$class_name,
			array($class_name, 'forex_rates_shortcode')
		);
	}
	
	function forex_rates_display($is_widget, $args=array()) {
		
		if($is_widget){
			extract($args);
			$options = get_option('wp_forex_rates');
			$title = $options['title'];
			$credit = $options['credit'];
			$quote1 = trim($options['quote1']);
			$quote2 = trim($options['quote2']);
			$quote3 = trim($options['quote3']);
			$quote4 = trim($options['quote4']);
			$quote5 = trim($options['quote5']);
			if ($credit == 1) {
				$displayCase = '<sup style="float:right">By <a href="http://plugin-wp.com" title="Wordpress Plugins and Widgets" >Wordpress Plugins and Widgets</a></sup>';
				$source = trim($options['source']);
				//
				// Contact us for full quotes table and support
				// http://plugin-wp.com
				//
			}
			else {
				$displayCase = '<p><b>Display link</b> checkbox is disabled. Please verify the plugin settings in control pannel.</p>';
			}
			$output[] = $before_widget . $before_title . $title . $after_title;
		}
		
		$output[] = '
		<div id="date" style="display:none"></div>
		<table>
			<tr><td id="label_'.$quote1.'"></td><td id="data_'.$quote1.'"></td><td id="time_'.$quote1.'"></td></tr>
			<tr><td id="label_'.$quote2.'"></td><td id="data_'.$quote2.'"></td><td id="time_'.$quote2.'"></td></tr>
			<tr><td id="label_'.$quote3.'"></td><td id="data_'.$quote3.'"></td><td id="time_'.$quote3.'"></td></tr>
			<tr><td id="label_'.$quote4.'"></td><td id="data_'.$quote4.'"></td><td id="time_'.$quote4.'"></td></tr>
			<tr><td id="label_'.$quote5.'"></td><td id="data_'.$quote5.'"></td><td id="time_'.$quote5.'"></td></tr>
		</table>
		'.$displayCase.'
		<script type="text/javascript">
			function elm(obj) {
				var theObj;
				if(document.all){
					if(typeof obj=="string"){
						return document.all(obj);
					}else{
						return obj.style;
					}
				}
				if(document.getElementById){
					if(typeof obj=="string"){
						return document.getElementById(obj);
					}else{
						return obj.style;
					}
				}
				return null;
			}
		function Print(msg){
			elm("date").innerHTML = msg;
			}
		function Error(msg){
			elm("date").innerHTML = msg;
			}
		function timeConverter(UNIX_timestamp){
			var a = new Date(UNIX_timestamp * 1000);
			var months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
			var year = a.getFullYear();
			var month = months[a.getMonth()];
			var date = a.getDate();
			var hour = a.getHours();
			var min = a.getMinutes();
			var sec = a.getSeconds();
			var time = /*date + " " + month + " " + year + " " +*/ hour + ":" + min + ":" + sec ;
			return time;
			}
		function Message(msg){
			var datas = msg.split("QUOTE|");
			for (i=1; i<msg.length; i++){
				var symbol_raw = datas[i].split("|");
				elm("label_"+symbol_raw[1]).innerHTML = symbol_raw[1] ;
				elm("data_"+symbol_raw[1]).innerHTML = symbol_raw[2];
				elm("time_"+symbol_raw[1]).innerHTML = timeConverter(symbol_raw[0]);
				}
			}
		var socket = new WebSocket("'.$source.'");

		socket.onopen = function(){
			Print("WebSocket connected.");
			socket.send("subscribe_market_data?symbol='.implode("|",array("$quote1","$quote2","$quote3","$quote4","$quote5")).'");
			}
		socket.onclose = function(event){
			if(event.wasClean){
				Print("WebSocket closed.");
				} else  {
				Error("WebSocket connection closed unexpectedly.");
				}
			Print("Code: " + event.code + " reason: " + event.reason);
			}
		socket.onmessage = function(event){
			Message(event.data);
			}
		socket.onerror = function(error){
			Error("Error: " + error.message);
			}
		</script>';
		$output[] = $after_widget;
		return join($output, "\n");
	}
	
	function forex_rates_control() {
		$class_name = 'wp_forex_rates';
		$calc_title = 'Realtime Trade rates';
		$calc_credit = 0;
		$source = 'wss://demo-quotes.jfdbrokers.com'; //example
		$quote1 = 'EURUSD';
		$quote2 = 'EURCAD';
		$quote3 = 'EURGBP';
		$quote4 = 'EURCHF';
		$quote5 = 'EURJPY';
	    $options = get_option($class_name);
		
		if (!is_array($options)) 
			$options = array(
				'title'=>$calc_title,
				'credit'=>$calc_credit,
				'source'=>$source,
				'quote1'=>$quote1,
				'quote2'=>$quote2,
				'quote3'=>$quote3,
				'quote4'=>$quote4,
				'quote5'=>$quote5
			);
			
		if ($_POST[$class_name.'_submit']) {
			$options['title'] = strip_tags(stripslashes($_POST[$class_name.'_title']));
			$options['credit'] = strip_tags(stripslashes($_POST[$class_name.'_credit']));
			
			$options['source'] = strip_tags(stripslashes($_POST[$class_name.'_source']));
			$options['quote1'] = strip_tags(stripslashes($_POST[$class_name.'_quote1']));
			$options['quote2'] = strip_tags(stripslashes($_POST[$class_name.'_quote2']));
			$options['quote3'] = strip_tags(stripslashes($_POST[$class_name.'_quote3']));
			$options['quote4'] = strip_tags(stripslashes($_POST[$class_name.'_quote4']));
			$options['quote5'] = strip_tags(stripslashes($_POST[$class_name.'_quote5']));
			update_option($class_name, $options);
		}
		
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$credit = htmlspecialchars($options['credit'], ENT_QUOTES);
		$source = htmlspecialchars($options['source'], ENT_QUOTES);
		$quote1 = htmlspecialchars($options['quote1'], ENT_QUOTES);
		$quote2 = htmlspecialchars($options['quote2'], ENT_QUOTES);
		$quote3 = htmlspecialchars($options['quote3'], ENT_QUOTES);
		$quote4 = htmlspecialchars($options['quote4'], ENT_QUOTES);
		$quote5 = htmlspecialchars($options['quote5'], ENT_QUOTES);
		
		if ($credit == "1") $defaultChecked = "checked='checked'"; else $defaultChecked="";
		
		echo '<p>Title: <input style="width: 180px;" name="'.$class_name.'_title" type="text" value="'.$title.'" /></p>';
		echo '<p>source: <input style="width: 180px;" name="'.$class_name.'_source" type="text" value="'.$source.'" /></p>';
		echo '<p>quote 1: <input style="width: 180px;" name="'.$class_name.'_quote1" type="text" value="'.$quote1.'" /></p>';
		echo '<p>quote 2: <input style="width: 180px;" name="'.$class_name.'_quote2" type="text" value="'.$quote2.'" /></p>';
		echo '<p>quote 3: <input style="width: 180px;" name="'.$class_name.'_quote3" type="text" value="'.$quote3.'" /></p>';
		echo '<p>quote 4: <input style="width: 180px;" name="'.$class_name.'_quote4" type="text" value="'.$quote4.'" /></p>';
		echo '<p>quote 5: <input style="width: 180px;" name="'.$class_name.'_quote5" type="text" value="'.$quote5.'" /></p>';
		echo '<p><input name="'.$class_name.'_credit" type="checkbox" '.$defaultChecked.' value="1" />Display link</p>';
		
		if ($credit != "1") {
			echo "<p style='color:#f00;'><b>'Display link'</b> need to be enabled for the plugin to work properly. If they're not, the customers can't recieve the trade quotes.</p>";
			echo '<p>Thank you for your help <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=S4QZYVAKJGFFQ" target="_blank">
					<img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" alt="PayPal - The safer, easier way to pay online!"></a>
				</p>';
			}
		echo '<input type="hidden" name="'.$class_name.'_submit" value="1" />';
		
		}
	
	function forex_rates_shortcode($args, $content=null) {
		return wp_forex_rates::forex_rates_display(false, $args);
		}
	
	function forex_rates_widget($args) {
		echo wp_forex_rates::forex_rates_display(true, $args);
		}
}

add_action('widgets_init', array('wp_forex_rates', 'forex_rates_init'));

?>