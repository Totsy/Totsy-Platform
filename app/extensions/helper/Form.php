<?php

namespace app\extensions\helper;

class Form extends \lithium\template\helper\Form {

	protected function _init() {
		$this->_strings += array(
			'select-option-group'     => '<optgroup label="{:title}">',
			'select-option-group-end' => '</optgroup>',
		);
		parent::_init();
	}

	/**
	 * Generates a `<select />` list using the `$list` parameter for the `<option />` tags. The
	 * default selection will be set to the value of `$options['value']`, if specified.
	 *
	 * For example: {{{
	 * $this->form->select('colors', array(1 => 'red', 2 => 'green', 3 => 'blue'), array(
	 * 	'id' => 'Colors', 'value' => 2
	 * ));
	 * // Renders a '<select />' list with options 'red', 'green' and 'blue', with the 'green'
	 * // option as the selection
	 * }}}
	 *
	 * @param string $name The `name` attribute of the `<select />` element.
	 * @param array $list An associative array of key/value pairs, which will be used to render the
	 *              list of options.
	 * @param array $options Any HTML attributes that should be associated with the `<select />`
	 *             element. If the `'value'` key is set, this will be the value of the option
	 *             that is selected by default.
	 * @return string Returns an HTML `<select />` element.
	 */
	public function select($name, $list = array(), array $options = array()) {
		$defaults = array('empty' => false, 'value' => null);
		list($name, $options, $template) = $this->_defaults(__FUNCTION__, $name, $options);
		list($scope, $options) = $this->_options($defaults, $options);

		if ($scope['empty']) {
			$list = array('' => ($scope['empty'] === true) ? '' : $scope['empty']) + $list;
		}
		$startTemplate = ($scope['multiple']) ? 'select-multi-start' : 'select-start';
		$output = $this->_render(__METHOD__, $startTemplate, compact('name', 'options'));

		foreach ($list as $value => $title) {
			if (!is_array($title)) {
				$output .= $this->_option($title, $value, $scope['value']);
				continue;
			}
			$items = $title;
			$output .= $this->_render(__METHOD__, 'select-option-group', array('title' => $value));

			foreach ($items as $value => $title) {
				$output .= $this->_option($title, $value, $scope['value']);
			}
			$output .= $this->_context->strings('select-option-group-end');
		}
		return $output . $this->_context->strings('select-end');
	}

	protected function _option($title, $value, $selected, array $options = array()) {
		$selected = (
			$value === $selected ||
			(is_array($selected) && in_array($value, $selected))
		);
		$options += ($selected ? array('selected' => true) : array());
		return $this->_render(__METHOD__, 'select-option', compact('value', 'title', 'options'));
	}

	/**
	 * Builds the defaults array for a method by name, according to the config.
	 *
	 * @param string $method The name of the method to create defaults for.
	 * @param string $name The `$name` supplied to the original method.
	 * @param string $options `$options` from the original method.
	 * @return array Defaults array contents.
	 */
	protected function _defaults($method, $name, $options) {
		$methodConfig = isset($this->_config[$method]) ? $this->_config[$method] : array();
		$options += $methodConfig + $this->_config['base'];

		$hasValue = (
			(!isset($options['value']) || empty($options['value'])) &&
			$name && $this->_binding && is_scalar($value = $this->_binding->{$name})
		);
		if ($hasValue) {
			$options['value'] = $value;
		}
		if (isset($options['default']) && empty($options['value'])) {
			$options['value'] = $options['default'];
		}
		unset($options['default']);
		$template = isset($this->_templateMap[$method]) ? $this->_templateMap[$method] : $method;

		if (strpos($name, '.')) {
			$parts = explode('.', $name);
			$prefix = array_shift($parts);
			$name = $prefix . '[' . join('][', $parts) . ']';
		}
		return array($name, $options, $template);
	}
}

?>