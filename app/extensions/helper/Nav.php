<?php
namespace app\extensions\helper;

/**
 * The 'Nav' class extends the generic `lithium\template\Helper` class to provide
 * a html menu list based on a backend document store.
 */

class Nav extends \lithium\template\Helper{
	
	/**
	 * Builds a HTML unordered list (`ul`) to be used as navigation element
	 * 
	 * @return boolean
	 */
	public function build($doc) {
		$sucess = false;
		$html = $this->_context->html;
		var_dump($doc->data());
		echo "<ul>";
		foreach ($doc as $nav) {
			if ($nav->children) {
				$this->build($nav->children);
			}
		}
		return $sucess;
	}
	
}