<?php
function lud_single_ajax(){
	echo '<div style="background: red; height: 200px; width: 200px"></div>';
	die();
}

add_action('wp_ajax_lud_single_ajax', 'lud_single_ajax');
add_action('wp_ajax_nopriv_lud_single_ajax', 'lud_single_ajax');