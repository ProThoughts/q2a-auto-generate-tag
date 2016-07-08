<?php

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}

class qa_tag_select
{
	const TAG_STRING = 'tag_string';
	const CATEGORIES = 'categories';
	const KEYWORDS = 'title';
	const KEYWORDS2 = 'titlecontent';

	const TAG_STA = 0;
	const CAT_STA = 1;
	const CAT_END = 2;
	const KEY_STA = 3;
	const KEY_END = 12;
	const KEY2_STA = 13;
	const KEY2_END = 22;

	public $lines;
	public $conditions;

	public function __construct()
	{
		$this->lines = array();
		$filepath = QA_PLUGIN_DIR.'q2a-auto-generate-tag/tag.csv';
		if (!file_exists($filepath)) {
			error_log($filepath . ' file is not found!');
			return;
		};
		$this->read_csv($filepath);

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

	public function get_tags($category = null, $title = null, $content = null)
	{
		$tags = array();
		foreach ($this->conditions as $cond) {
			if ($this->is_category_match($category, $cond)) {
				$tags[] = $cond[self::TAG_STRING];
				continue;
			}
			if ($this->is_title_match($title, $cond)) {
				$tags[] = $cond[self::TAG_STRING];
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
			for ($n = self::KEY2_STA; $n <= self::KEY2_END; $n++) {
				$tmp[self::KEYWORDS2][] = $line[$n];
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

	private function is_title_match($intitle = '', $cond = array())
	{
		foreach ($cond[self::KEYWORDS] as $word) {
			if (empty($word)) {
				continue;
			}
			if (isset($intitle) && strpos($intitle, $word) !== false) {
				return true;
			}
		}
		return false;
	}

	private function is_keywords_match($intitle = '', $incontent = '', $cond = array())
	{
		foreach ($cond[self::KEYWORDS2] as $word) {
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

	private function read_csv($filepath)
	{
		// ロケールの設定
		// echo setlocale(LC_ALL, '0') . "\n"; // 現在のロケールを確認
		setlocale(LC_ALL, 'ja_JP.UTF-8');

		$file = new SplFileObject($filepath);
		$file->setFlags(SplFileObject::READ_CSV);

		foreach ($file as $line) {
			if (isset($line[0])) {
				$this->lines[] = $line;
			}
		}
		$file = null;
		setlocale(LC_ALL, '');	// 元に戻す
	}
}
