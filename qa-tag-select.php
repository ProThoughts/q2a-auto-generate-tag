<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

require_once QA_PLUGIN_DIR.'q2a-auto-generate-tag/agt-db-client.php';

class qa_tag_select
{
	const TAG_STRING = 'tag_string';
	const CATEGORIES = 'categories';
	const KEYWORDS = 'keywords';

	const TAG_STA = 0;
	const CAT_STA = 1;
	const CAT_END = 2;
	const KEY_STA = 3;
	const KEY_END = 9;

	public $file;
	public $lines;
	public $conditions;

	public function __construct()
	{
		$this->file = null;
		$this->lines = array();
		$filepath = QA_PLUGIN_DIR.'q2a-auto-generate-tag/tag.csv';
		if (!file_exists($filepath)) {
			error_log($filepath . ' file is not found!');
			return;
		};

		$this->file = new SplFileObject($filepath);
		$this->file->setFlags(SplFileObject::READ_CSV);

		foreach ($this->file as $line) {
			if (isset($line[0])) {
				$this->lines[] = $line;
			}
		}
		if (count($this->lines) > 0) {
			$this->parse_condition();
		}
	}

	public function get_lines()
	{
		return $this->lines;
	}

	public function get_conditions()
	{
		return $this->conditions;
	}

	public function __destruct()
	{
		$this->file = null;
	}

	public function get_tags($categoryid = null, $title = null, $content = null)
	{
		$tags = array();
		if (isset($categoryid)) {
			$category = agt_db_client::get_category_title($categoryid);
		} else {
			$category = '';
		}
		foreach ($this->conditions as $cond) {
			if ($this->is_category_match($category, $cond)) {
				$tags[] = $cond[self::TAG_STRING];
				continue;
			}
			if ($this->is_keywords_match($title, $content, $cond)) {
				$tags[] = $cond[self::TAG_STRING];
			}
		}

		return $tags;
	}

	private function parse_condition()
	{
		$conditions = array();
		foreach ($this->lines as $line) {
			$tmp = array();
			$tmp[self::TAG_STRING] = $line[self::TAG_STA];
			for($j = self::CAT_STA; $j <= self::CAT_END; $j++) {
				$tmp[self::CATEGORIES][] = $line[$j];
			}
			for ($k = self::KEY_STA; $k <= self::KEY_END; $k++) {
				$tmp[self::KEYWORDS][] = $line[$k];
			}
			$conditions[] = $tmp;
		}
		$this->conditions = $conditions;
	}

	private function is_category_match($incategory = '', $cond = array())
	{
		foreach ($cond[self::CATEGORIES] as $cat) {
			if ($incategory === $cat) {
				return true;
			}
		}
		return false;
	}

	private function is_keywords_match($intitle = '', $incontent = '', $cond = array())
	{
		foreach ($cond[self::KEYWORDS] as $word) {
			if (empty($word)) {
				continue;
			}
			if ((isset($intitle) && strpos($intitle, $word) !== false) ||
				(isset($incontent) && strpos($incontent, $word) !== false)) {
				return true;
			}
		}
		return false;
	}
}
