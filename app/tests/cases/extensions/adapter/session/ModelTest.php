<?php

namespace app\tests\cases\extensions\adapter\session;

use app\tests\mocks\extensions\adapter\session\MockModel;
use app\tests\mocks\models\MockSessionModel;

class ModelTest extends \lithium\test\Unit {
	public function setUp() {
		$this->model = new MockModel(array('model' => 'app\tests\mocks\models\MockSessionModel'));
		MockSessionModel::resetLog();
	}

	public function tearDown() {
	}

	public function testKey() {
		$expected = session_id() ?: null;
		$result = $this->model->key();
		$this->assertEqual($expected, $result);
	}

	public function test_OpenWithoutExisting() {
		$this->model->_open(null, null);

		$expected = 3;
		$result = count(MockSessionModel::$log);
		$this->assertEqual($expected, $result);

		list($method, $args) = MockSessionModel::$log[0];
		list($id, ) = $args;

		$expected = 'find';
		$result = $method;
		$this->assertEqual($expected, $result);

		$expected = session_id();
		$result = $id;
		$this->assertEqual($expected, $result);

		list($method, $args) = MockSessionModel::$log[1];

		$expected = 'key';
		$result = $method;
		$this->assertEqual($expected, $result);

		$expected = array(session_id());
		$result = $args;
		$this->assertEqual($expected, $result);

		list($method, $args) = MockSessionModel::$log[2];
		list($data, ) = $args;

		$expected = 'create';
		$result = $method;
		$this->assertEqual($expected, $result);

		$expected = array('_id' => session_id());
		$result = $data;
		$this->assertEqual($expected, $result);

		$stored = $this->model->_read();

		$expected = session_id();
		$result = $stored["_id"];
		$this->assertEqual($expected, $result);

		$result = is_int($stored["expiry"]);
		$message = $stored;
		$this->assertTrue($result, $message);
	}

	public function test_OpenWithExisting() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$expected = 1;
		$result = count(MockSessionModel::$log);
		$this->assertEqual($expected, $result);

		list($method, $args) = MockSessionModel::$log[0];
		list($id, ) = $args;

		$expected = 'find';
		$result = $method;
		$this->assertEqual($expected, $result);

		$expected = session_id();
		$result = $id;
		$this->assertEqual($expected, $result);

		$expected = array('faked' => 'faked');
		$result = $this->model->_read();
		$this->assertEqual($expected, $result);
	}

	public function test_Destroy() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$result = $this->model->_destroy(null);
		$this->assertTrue($result);

		$data = $this->model->data();
		$result = $data->deleted;
		$this->assertTrue($result);
	}

	public function test_Close() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$result = is_null($this->model->_read());
		$this->assertFalse($result);

		$this->model->_close();

		$result = is_null($this->model->_read());
		$this->assertTrue($result);
	}

	public function test_GC() {
		$this->model->_gc(0);

		$expected = 1;
		$result = count(MockSessionModel::$log);
		$this->assertEqual($expected, $result);

		list($method, $args) = MockSessionModel::$log[0];
		list($conditions, ) = $args;

		$expected = 'remove';
		$result = $method;
		$this->assertEqual($expected, $result);

		$result = isset($conditions['expiry']);
		$message = $conditions;
		$this->assertTrue($result, $message);

		$result = isset($conditions['expiry']['<=']);
		$message = $conditions['expiry'];
		$this->assertTrue($result, $message);
	}

	public function testDelete() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();
		$key = 'foo';
		$value = 'bar';
		$closure = $this->model->write($key, $value);
		$closure($this->model, compact('key', 'value'), null);

		$key = 'foo';
		$options = array();
		$closure = $this->model->delete($key, $options);

		$result = is_callable($closure);
		$this->assertTrue($result);

		$result = $closure($this->model, compact('key', 'options'), null);
		$this->assertTrue($result);

		$data = $this->model->_read();
		$result = isset($data['foo']);
		$this->assertFalse($result);
	}

	public function testRead() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();
		$key = 'foo';
		$value = 'bar';
		$closure = $this->model->write($key, $value);
		$closure($this->model, compact('key', 'value'), null);

		$key = 'foo';
		$options = array();
		$closure = $this->model->delete($key, $options);

		$result = is_callable($closure);
		$this->assertTrue($result);

		$expected = 'bar';
		$result = $closure($this->model, compact('key', 'options'), null);
		$this->assertEqual($expected, $result);
	}

	public function test_Read() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();
		$key = 'foo';
		$value = 'bar';
		$closure = $this->model->write($key, $value);
		$closure($this->model, compact('key', 'value'), null);

		$data = $this->model->_read();

		$result = is_array($data);
		$message = $data;
		$this->assertTrue($result, $message);

		$expected = array('faked' => 'faked', 'foo' => 'bar');
		$result = $data;
		$this->assertEqual($expected, $result);

		$expected = 'bar';
		$result = $this->model->_read('foo');
		$this->assertEqual($expected, $result);

		$this->model->_close();
		$result = $this->model->_read();
		$this->assertNull($result);
	}

	public function testWrite() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$key = 'foo';
		$value = 'bar';
		$options = array();
		$closure = $this->model->write($key, $value, $options);

		$result = is_callable($closure);
		$this->assertTrue($result);

		$result = $closure($this->model, compact('key', 'value', 'options'), null);
		$this->assertTrue($result);

		$expected = 'bar';
		$result = $this->model->_read('foo');
		$this->assertEqual($expected, $result);
	}

	public function test_Write() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$this->model->_write('id', null);

		$entity = $this->model->data();

		$result = $entity->saved;
		$this->assertTrue($result);

		$expected = 'id';
		$result = $entity->_id;
		$this->assertEqual($expected, $result);
	}

	public function testCheck() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$key = 'foo';
		$closure = $this->model->check($key);

		$result = is_callable($closure);
		$this->assertTrue($result);

		$result = $closure($this->model, compact('key'));
		$this->assertFalse($result);

		$key = 'foo';
		$value = 'bar';
		$closure = $this->model->write($key, $value);
		$closure($this->model, compact('key', 'value'), null);

		$key = 'foo';
		$closure = $this->model->check($key);

		$result = $closure($this->model, compact('key'));
		$this->assertTrue($result);
	}

	public function testClear() {
		MockSessionModel::fakeFind();
		$this->model->_open(null, null);
		MockSessionModel::unfakeFind();

		$options = array();
		$closure = $this->model->clear($options);

		$result = is_callable($closure);
		$this->assertTrue($result);

		$result = $closure($this->model, compact('options'), null);
		$this->assertTrue($result);

		$data = $this->model->data();
		$result = $data->deleted;
		$this->assertTrue($result);
	}
}

?>