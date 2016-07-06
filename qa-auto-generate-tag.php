<?php

require_once QA_PLUGIN_DIR.'q2a-auto-generate-tag/agt-db-client.php';
require_once QA_PLUGIN_DIR.'q2a-auto-generate-tag/qa-tag-select.php';

class qa_auto_generate_tag
{

	function process_event($event, $userid, $handle, $cookieid, $params)
	{

		if (qa_using_tags() && $event === 'q_post') {
			if (isset($params['categoryid'])) {
				$category = agt_db_client::get_category_title($params['categoryid']);
			} else {
				$category = '';
			}
			$tagsel = new qa_tag_select();
			$tags = $tagsel->get_tags($category, $params['title'], $params['content']);
			if (count($tags) > 0) {
				$oldquestion = agt_db_client::get_oldquestion($params['postid']);
				qa_question_set_content($oldquestion, $oldquestion['title'],
									$oldquestion['content'], $oldquestion['format'],
									$oldquestion['text'], qa_tags_to_tagstring($tags),
									$oldquestion['notify'], $userid, $handle,
									$cookieid, $null, null, false, false);
			} else {
				error_log('No Tags postid: '. $params['postid']);
			}
		}
	}

}
