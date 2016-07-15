<?php

/*
	Plugin Name: Custom vote button Plugin
	Plugin URI:
	Plugin Description: Customize Vote Button
	Plugin Version: 1.0
	Plugin Date: 2016-07-15
	Plugin Author: 38qa.net
	Plugin Author URI: http://38qa.net/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// layer
qa_register_plugin_layer('qa-custom-vote-button-layer.php','Custom vote button Layer');
