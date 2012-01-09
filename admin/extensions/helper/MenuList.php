<?php
namespace admin\extensions\helper;
use totsy_common\models\Menu;

/**
 * The 'Menu' class creates a html menu list based on a backend document store.
 * 
 * Usage: 
 *
 *{{{
 * 	$options = array('div' => array('id' => 'main-nav'), 'ul' => array('class' => 'menu main-nav'));
 *	$doc = Menu::find('all', array('conditions' => array('location' => 'top', 'active' => 'true')));	
 *  $html = $this->menu->build($doc, $options); //returns html menu list
 *}}}
 *
 */

class MenuList extends \lithium\template\Helper{	
	
	/**
	 * Builds a HTML unordered list (`ul`).
	 * 
	 * 
	 * @return string
	 */
	public function build($doc, array $options = array()) {
		$html = "";
		$defaults = array('div' => null, 'ul' => null , 'li' => null);
		$this->options = $options ? $options : $defaults;
		$div = isset($options['div']) ? '<div>' : "";
		$endDiv = isset($options['div']) ? '</div>' : "";
		$ul = '<ul>';
		$li = '';

		if(isset($options['div'])){
			$div = '<div ' . $this->getElements('div') . '>';
		}
		if(isset($options['ul'])){
			$ul = '<ul '. $this->getElements('ul') . '>';
		}	
		
		foreach ($doc as $nav) {
			$element = $nav->data();
			
			if(isset($options['li'])){
				$li .= '<li ' . $this->getElements('li') .'>';
			} 
			if(isset($element['class'])) {
				$li .= "<li class = \"$element[class]\"" . '>';
			}
			else {
				$li .= "<li>";
			}
			$url = '/'. $element['route']['controller'];
			if (isset($element['route']['view'])) {
				$url .= '/'.$element['route']['view'];
			} 
			if (isset($element['route']['action'])) {
				$url .= '/'.$element['route']['action'];
			}
			$li .= $this->_context->html->link("<span>$element[title]</span>", $url, array('title' => "$element[title]", 'escape' => false ));	
			if (isset($element['children'])) {
				$subdoc = Menu::find('all', array('conditions' => array('active' => 'true', 'parent' => $element['title'])));
				$li .= $this->build($subdoc);
			}
			$li .= '</li>';
		}

		$html .= $div . "<div>" . $ul . $li . "</ul>". "</div>";
		$html .= $endDiv;

		return $html;
	}
	/**
	 * Loops through the mongodb document elements.
	 *
	 * @return string
	 */
	protected function getElements($name) {
		$element = '';
		foreach ($this->options[$name] as $key=>$value){
			$element .= "$key=\"$value\" ";
		}
		return $element;
	}
	
}