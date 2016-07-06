<?php

/*
	Plugin Name: Auto Generate Tag Plugin
	Plugin URI:
	Plugin Description: Automatically attach a tag when post a question.
	Plugin Version: 1.0
	Plugin Date: 2016-07-06
	Plugin Author: 38qa.net
	Plugin Author URI:
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.7
	Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

// layer
// qa_register_plugin_layer('qa-auto-generate-tag-layer.php','Auto Generate Tag Layer');
// event
qa_register_plugin_module('event', 'qa-auto-generate-tag.php', 'qa_auto_generate_tag', 'Auto Generate Tag Event');
