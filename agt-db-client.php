<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

require_once QA_INCLUDE_DIR.'app/posts.php';

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

	public static function update_all_question_tags()
	{
		$cnt = 0;
		$questions = self::get_all_questions();
		foreach ($questions as $question) {
			$cnt += self::update_question_tags($question);
		}
		return $cnt;
	}

	private static function update_question_tags($oldquestion)
	{
		if (isset($oldquestion['categoryid'])) {
			$category = self::get_category_title($oldquestion['categoryid']);
		} else {
			$category = '';
		}
		$tagsel = new qa_tag_select();
		$tags = $tagsel->get_tags($category, $oldquestion['title'], $oldquestion['content']);
		if (count($tags) > 0) {
			if (qa_tags_to_tagstring($tags) !== $oldquestion['tags']) {
				$text = qa_post_content_to_text($oldquestion['content'], $oldquestion['format']);
				qa_question_set_content($oldquestion, $oldquestion['title'],
									$oldquestion['content'], $oldquestion['format'],
									$text, qa_tags_to_tagstring($tags),
									$oldquestion['notify'], $oldquestion['userid'],
									$oldquestion['handle'], $oldquestion['cookieid'],
									null, null, false, false);

				return 1;
			}
		} else {
			error_log("Did not match a tag. postid: ". $oldquestion['postid']);
		}
		return 0;
	}

	private static function get_all_questions()
	{
		$questions1 = qa_db_select_with_pending(self::qa_db_all_selectspec('Q'));
		$questions2 = qa_db_select_with_pending(self::qa_db_all_selectspec('Q_HIDDEN'));
		$questions = array_merge($questions1, $questions2);
		return $questions;
	}

	private static function  qa_db_all_selectspec($type = null)
	{
		if (!isset($type)) {
			$type = 'Q';
		}
		$sortsql='ORDER BY ^posts.created DESC';
		$selectspec = qa_db_posts_basic_selectspec(null, true);

		$selectspec['source'].=" JOIN (SELECT postid FROM ^posts WHERE ".
			"type = $ ".$sortsql.") y ON ^posts.postid=y.postid";

		array_push($selectspec['arguments'], $type);

		$selectspec['sortdesc'] = 'created';

		return $selectspec;
	}
}
