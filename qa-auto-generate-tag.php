<?php

require_once QA_PLUGIN_DIR.'q2a-auto-generate-tag/qa-tag-select.php';

class qa_auto_generate_tag
{

	function process_event($event, $userid, $handle, $cookieid, $params)
	{

		if (qa_using_tags() && $event === 'q_post') {
			$tagsel = new qa_tag_select();
			$tags = $tagsel->get_tags($params['categoryid'], $params['title'], $params['content']);
			if (count($tags) > 0) {
				$oldquestion = $this->get_oldquestion($params['postid']);
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

	private function get_oldquestion($postid = null)
	{
		$post = qa_db_single_select(qa_db_full_post_selectspec(null, $postid));
		return $post;
	}
}
