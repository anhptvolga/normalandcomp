

<?php

require_once '../classes/KDimNode.php';

class sortChildrensTest extends PHPUnit_Framework_TestCase {
	
	public static function setUpBeforeClass() {
		echo "im warming up\n";
	}
	
	public function test_onlyOperand() {
		$this->assertEquals(1,1);
	}
	
	public function test_onlyOperator() {
		$this->assertEquals(1,1);
	}
	
	public function test_mixed() {
		$this->assertEquals(1,1);
	}
	
	public function test_sorted() {
		$this->assertEquals(1,1);
	}
	
	public function test_reverse() {
		$this->assertEquals(1,1);
	}
	
	public function test_hardChildrens() {
		$this->assertEquals(1,1);
	}
	
	public static function tearDownAfterClass() {
		
	}
}

?>