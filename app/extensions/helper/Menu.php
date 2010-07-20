<?php

namespace app\extensions\helper;

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

class Menu extends \lithium\template\Helper {

	protected $_classes = array(
		'menu' => 'app\models\Menu',
		'event' => 'app\models\Event'
	);

	protected $_strings = array(
		'menu' => '<div id="{:key}"{:options}><div><ul class="menu">{:html}</ul></div></div>',
		'sub-menu' => '<ul>{:html}</ul>',
		'menu-list-item' => '<li{:options}>{:html}{:items}</li>',
		'menu-block-item' => '<div{:options}>{:html}{:items}</div>',
		'menu-label' => '<span>{:content}</span>'
	);

	public function render($key, array $options = array()) {
		$defaults = array('block' => false);
		$options += $defaults;
		$cur = $this->_context->request()->params;

		if ($cur['controller'] == 'pages' && $cur['action'] == 'view' && isset($cur['args'][0])) {
			$cur = array('controller' => $cur['controller'], 'action' => $cur['args'][0]);
		} else {
			$cur = array('controller' => $cur['controller'], 'action' => $cur['action']);
		}
		if ($cur['action'] == 'index') {
			unset($cur['action']);
		}
		$model = $this->_classes['menu'];
		$menu = $model::first(array('conditions' => compact('key')));

		if (!$menu) {
			return;
		}
		if ($menu->id) {
			$key = $menu->id;
		}

		$items = $this->_items($menu, $cur, $options);
		$keys = array('_id' => true, 'key' => true, 'label' => true, 'url' => true, 'items' => true);
		$options = array_diff_key($menu->data(), $keys);
		$html = join('', $items);
		return $this->_render(__METHOD__, 'menu', compact('key', 'options', 'html'));
	}

	protected function _items($menu, $active, array $options = array()) {
		$items = array();

		foreach (range(0, count($menu->items->data()) - 1) as $i) {
			$item = $menu->items->{$i};
			$items[] = $this->_item($item, $active, $options);
		}
		return $items;
	}

	protected function _isActive($item, $active, $children = true) {
		if ($item->url->data() == $active) {
			return true;
		}
		if ($item->items && $children) {
			$menu = $item;

			if (is_string($item->items)) {
				$model = $this->_classes['menu'];
				$menu = $model::first(array('conditions' => array('key' => $item->items)));
			}
			foreach (range(0, count($menu->items->data()) - 1) as $i) {
				if (!$this->_isActive($menu->items->{$i}, $active, $children)) {
					continue;
				}
				return true;
			}
		}
		return false;
	}

	protected function _item($item, $active, array $options = array()) {
		$defaults = array('label' => true, 'url' => true, 'items' => true);
		$itemOpts = array_diff_key($item->data(), $defaults);
		$label = $this->_render(__METHOD__, 'menu-label', array('content' => $item->label));

		if ($item->url) {
			$link = array('escape' => false, 'title' => $item->label);
			$label = $this->_context->html->link($label, $item->url->data(), $link);
		}
		if ($this->_isActive($item, $active)) {
			$itemOpts += array('class' => '');
			$itemOpts['class'] = trim($itemOpts['class'] . ' active');
		}

		$template = 'menu-' . ($options['block'] ? 'block' : 'list') . '-item';
		$content = array('html' => $label, 'options' => $itemOpts, 'items' => '');

		if ($item->items) {
			$menu = $item;

			if (is_string($item->items)) {
				$model = $this->_classes['menu'];
				$menu = $model::first(array('conditions' => array('key' => $item->items)));
			}
			$content['items'] = $this->_render(__METHOD__, 'sub-menu', array(
				'html' => join('', $this->_items($menu, $active, $options))
			));
		}
		return $this->_render(__METHOD__, $template, $content);
	}

	/**
	 * Builds a HTML unordered list (`ul`).
	 * 
	 * 
	 * @return string
	 */
	public function build($doc, array $options = array()) {
		$defaults = array('div' => null, 'ul' => null , 'li' => null);
		$this->options = $options ? $options : $defaults;
		$model = $this->_classes['menu'];

		if (isset($options['div'])){
			$div = '<div ' . $this->getElements('div') . '>';
		}
		if (isset($options['ul'])){
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
				$todayList = Event::open(array('fields' => array('name', 'url')));
				$saleItems = '';
				$x = 0;
				$last = count($todayList) - 1;
				foreach ($todayList as $item) {
					switch ($x) {
						case 0:
							$class = 'first';
							break;
						case $last:
							$class = 'last';
							break;
						default:
							$class = '';
							break;
					}
					$x++;
					$saleUrl = $this->_context->html->link("<span>$item->name</span>", "/events/view/$item->url", array(
						'title' => "$item->name", 
						'escape' => false 
					));
					$saleItems .= "<li class='$class'>$saleUrl</li>";
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