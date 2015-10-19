<?php

namespace WebSocketClient;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-10-19 at 21:02:28.
 */
class RequestHandlerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var RequestHandler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
     protected function setUp() {
	$this->object = new RequestHandler();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /**
     * @covers ServerRequestHandler::time
     * @todo   Implement testTime().
     */
    public function testTime() {
	// Remove the following lines when you implement this test.
	$this->markTestIncomplete(
		'This test has not been implemented yet.'
	);
    }

    /**
     * @covers ServerRequestHandler::ping
     * @todo   Implement testPing().
     */
    public function testPing() {
	// Remove the following lines when you implement this test.
	$reply = $this->object->ping();
	$this->assertEquals('pong', $reply);
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
    }

    /**
     * @covers ServerRequestHandler::pong
     * @todo   Implement testPong().
     */
    public function testPong() {
	// Remove the following lines when you implement this test.
	$reply = $this->object->pong();
	$this->assertEquals('ping', $reply);
    }

    /**
     * @covers ServerRequestHandler::add
     * @todo   Implement testAdd().
     */
    public function testAdd($items = 10) {
	// Remove the following lines when you implement this test.
	for ($i = 0; $i < $items; $i++) {
	    $reply[] = $this->object->add("item_" . $i);
	    $this->assertGreaterThanOrEqual($i, $reply[$i], $reply[$i] . ' != ' . $i);
	}
	return $reply;
    }

    /**
     * @covers ServerRequestHandler::getList
     * @todo   Implement testGetList().
     */
    public function testGetList() {
	// Remove the following lines when you implement this test.
	$expected = rand(50, 2000);
	$this->testAdd($expected);
	$reply = $this->object->getList();
	$this->assertEquals(true, is_array($reply));
	$this->assertEquals($expected, count($reply), print_r($reply, true));
    }

    /**
     * @covers ServerRequestHandler::delete
     * @todo   Implement testDelete().
     */
    public function testDelete() {
	// Remove the following lines when you implement this test.
	$items = rand(200, 300);
	$keys = $this->testAdd($items);
	$now = count($this->object->getList());
	shuffle($keys);
	foreach ($keys as $key) {
	    $this->object->delete($key);
	    $this->assertArrayNotHasKey($key, $this->object->getList(), "Key $key still exists!");
	}
    }

    /**
     * @covers ServerRequestHandler::get
     * @todo   Implement testGet().
     */
    public function testGet() {
	$keys = $this->testAdd(rand(50, 2000));
	foreach ($keys as $value) {
	    $this->assertEquals('item_' . $value, $this->object->get((string) $value), $value . ' != ' . $this->object->get((string) $value));
	}
    }

    /**
     * @covers ServerRequestHandler::set
     * @todo   Implement testSet().
     */
    public function testSet() {
	for ($i = 0; $i <= 10; $i++) {
	    $keys[] = $i;
	    $this->assertEquals(true, $this->object->set($i, "item_" . $i));
	}
	foreach ($keys as $value) {
	    $this->assertEquals('item_' . $value, $this->object->get((string) $value), $value . ' != ' . $this->object->get((string) $value));
	}
    }
}
