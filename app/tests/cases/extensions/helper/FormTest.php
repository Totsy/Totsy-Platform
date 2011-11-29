<?php

namespace app\tests\cases\extensions\helper;

use lithium\tests\mocks\template\helper\MockFormRenderer;
use lithium\action\Request;
use lithium\net\http\Router;

/**
 * This tests against either a custom form helper - if present - or with the
 * same expectations against the Lithium form helper. This is to ensure that
 * the custom helper implementation itself works with the hardcoded expectations
 * and that - in case the custom helper is removed - the core helper will meet
 * the same exact expectations. This proves that both implementations work
 * similar.
 *
 * @see FormTest::_helper()
 */
class FormTest extends \lithium\test\Unit {

	protected $_classes = array(
		'app' => '\app\extensions\helper\Form',
		'lithium' => 'lithium\template\helper\Form'
	);

	public function testSelectWithOptgroups() {
		$helper = $this->_helper();

		$data = array(
			'United States' => array(
				"AL" => 'Alabama',
				"AK" => 'Alaska'
			),
			'Canada' => array(
				"AB" => 'Alberta'
			)
		);
		$expected = array(
			'select' => array('name' => 'state'),
			array('option' => array('value' => '')),
			'Select a state',
			'/option',
			array('optgroup' => array('label' => 'United States')),
			array('option' => array('value' => 'AL')),
			'Alabama',
			'/option',
			array('option' => array('value' => 'AK')),
			'Alaska',
			'/option',
			'/optgroup',
			array('optgroup' => array('label' => 'Canada')),
			array('option' => array('value' => 'AB')),
			'Alberta',
			'/option',
			'/optgroup',
			'/select'
		);
		$result = $helper->select('state', $data, array('empty' => 'Select a state'));
		$this->assertTags($result, $expected);
	}

	public function testSelectNumeric() {
		$helper = $this->_helper();

		$id = 'aba234234alkdj234l';
		$data = array(
			0 => '0',
			1 => 1,
			2 => 2
		);

		$expected = array(
			'select' => array('name' => "cart[{$id}]", 'id' => $id, 'class' => 'quantity'),
			array('option' => array('value' => '0')),
			'0',
			'/option',
			array('option' => array('value' => '1')),
			'1',
			'/option',
			array('option' => array('value' => '2', 'selected' => 'selected')),
			'2',
			'/option',
			'/select'
		);
		$result = $helper->select("cart[{$id}]", $data, array(
    		'id' => $id, 'value' => 2, 'class' => 'quantity'
		));
		$this->assertTags($result, $expected);
	}

	protected function _helper() {
		$request = new Request();
		$context = new MockFormRenderer(compact('request'));

		$hasCustomHelper = file_exists(LITHIUM_APP_PATH . '/extensions/helper/Form.php');
		return $this->_instance($hasCustomHelper ? 'app' : 'lithium', array('context' => $context));
	}
}

?>