<?php

class TestTSCustomCore extends PHPUnit_Framework_TestCase {

    /**
     * @covers TwelveSands::__construct
     */
    public function test_ts_new() {
        $ts = new TwelveSands();

        $this->assertNotNull( $ts );
    }

}
