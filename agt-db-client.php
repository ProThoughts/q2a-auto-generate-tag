<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

class agt_db_client
{

	public static function get_category_title($categoryid = null)
	{
		$title = '';
		$categories = qa_db_single_select(qa_db_full_category_selectspec($categoryid, true));
		if (isset($categories['title'])) {
			$title = $categories['title'];
		}
		return $title;
	}

	public static function get_oldquestion($postid = null)
	{
		$post = qa_db_single_select(qa_db_full_post_selectspec(null, $postid));
		return $post;
	}
}
