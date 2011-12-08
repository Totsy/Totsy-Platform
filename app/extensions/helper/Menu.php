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
		'menu' => 'totsy_common\models\Menu',
		'event' => 'app\models\Event'
	);

	protected $_strings = array(
		'menu' => '<div id="{:key}"{:options}><ul class="menu main-nav">{:html}</ul></div>',
		'sub-menu' => '<ul>{:html}</ul>',
		'menu-list-item' => '<li{:options}>{:html}{:items}</li>',
		'menu-block-item' => '<div{:options}>{:html}{:items}</div>',
		'menu-label' => '<span>{:content}</span>'
	);

	public function render($key, array $options = array()) {
		$defaults = array('block' => false);
		$options += $defaults;
		$cur = $base = $this->_context->request()->params;

		if ($cur['controller'] == 'pages' && $cur['action'] == 'view' && isset($cur['args'][0])) {
			$cur = array('controller' => $cur['controller'], 'action' => $cur['args'][0]);
		} else {
			$cur = array('controller' => $cur['controller'], 'action' => $cur['action']);
		}
		if ($cur['action'] == 'index') {
			unset($cur['action']);
		}
		if (isset($base['item'])) {
			$cur['item'] = $base['item'];
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
			$item = is_a($menu->items, 'Iterator') ? $menu->items[$i] : $menu->items->{$i};

			if ($item->enabled === false) {
				continue;
			}
			$items[] = $this->_item($item, $active, $options);
		}
		return $items;
	}

	protected function _isActive($item, $active, $children = true) {
		$url = is_string($item->url) ? $item->url : $item->url->data();

		if ($url == $active) {
			return true;
		}
		if ($item->items && $children) {
			$menu = $item;

			if (is_string($item->items)) {
				$model = $this->_classes['menu'];
				$menu = $model::first(array('conditions' => array('key' => $item->items)));
			}
			foreach (range(0, count($menu->items->data()) - 1) as $i) {
				$current = is_a($menu->items, 'Iterator') ? $menu->items[$i] : $menu->items->{$i};

				if (!$this->_isActive($current, $active, $children)) {
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

		// Btw, this is a hack. :-)
		if ($item->label == 'Sales' && $item->url->data() == array('controller' => 'events')) {
			$menu = $this->_classes['menu'];
			$event = $this->_classes['event'];
			$query = array('fields' => array('name', 'url'));
			$item->items = $event::open($query)->map(function($item) use ($menu) {
				return $menu::create(array(
					'label' => $item->name, 'url' => array(
						'controller' => 'events', 'action' => 'view', 'event' => $item->url
					)
				));
			});
			$item->items->first()->class = 'first';
			$item->items->end()->class = 'last';
		}
		if ($item->url) {
			$url = is_string($item->url) ? $item->url : $item->url->data();
			$link = array('escape' => false, 'title' => $item->label);
			$label = $this->_context->html->link($label, $url, $link);
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
}

?>