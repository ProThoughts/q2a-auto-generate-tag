<?php

if (!defined('QA_VERSION')) {
	require_once dirname(empty($_SERVER['SCRIPT_FILENAME']) ? __FILE__ : $_SERVER['SCRIPT_FILENAME']).'/../../qa-include/qa-base.php';
}

error_log('-----------------------------------');
error_log('auto generate tag start');
require_once QA_PLUGIN_DIR.'q2a-auto-generate-tag/qa-tag-select.php';
require_once QA_PLUGIN_DIR.'q2a-auto-generate-tag/agt-db-client.php';
$start = microtime(true);
if (qa_using_tags()) {
	$tagsel = new qa_tag_select();
	// $conditions = $tagsel->get_conditions();
	// foreach ($conditions as $cond) {
	// 	foreach ($cond as $key => $value) {
	// 		if (is_array($value)) {
	// 			echo $key . " : ";
	// 			foreach ($value as $val) {
	// 				echo $val . ",";
	// 			}
	// 			echo "\n";
	// 		} else {
	// 			echo $key . ' : ' . $value . "\n";
	// 		}
	// 	}
	// }
	$count = agt_db_client::update_all_question_tags();
	error_log('処理件数: ' . $count);
}
$end = microtime(true);
error_log("処理時間：" . ($end - $start) . "秒");
error_log('auto generate tag finished');
error_log('-----------------------------------');

/*
	Omit PHP closing tag to help avoid accidental output
*/
