<?php
namespace app\extensions\helper;
use app\models\Menu;
use app\models\Event;

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
			$menuItem = $nav->data();

			if(isset($options['li'])){
				$li .= '<li ' . $this->getElements('li') .'>';
			} 
			if(isset($menuItem['class'])) {
				$li .= "<li class = \"$menuItem[class]\"" . '>';
			}
			else {
				$li .= "<li>";
			}
			$url = '/'. $menuItem['route']['controller'];
			if (isset($menuItem['route']['view'])) {
				$url .= '/'.$menuItem['route']['view'];
			} 
			if (isset($menuItem['route']['action'])) {
				$url .= '/'.$menuItem['route']['action'];
			}
			$li .= $this->_context->html->link("<span>$menuItem[title]</span>", $url, array('title' => "$menuItem[title]", 'escape' => false ));	
			if (isset($menuItem['children'])) {
				$subdoc = Menu::find('all', array('conditions' => array('active' => 'true', 'parent' => $menuItem['title'])));
				$li .= $this->build($subdoc);
			}
			if ($menuItem['title'] == 'Sales') {
				$todayList = Event::today(array('fields' => 'name'));
				$saleItems = '';
				foreach ($todayList as $item) {
					$saleUrl = $this->_context->html->link("<span>$item->name</span>", "/events/view/$item->name", array(
						'title' => "$item->name", 
						'escape' => false 
					));
					$saleItems .= "<li>$saleUrl</li>";
				}
				$li .= "<ul>$saleItems</ul>";
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