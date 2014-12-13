<?php

class TestTwelveSands extends PHPUnit_Framework_TestCase {

    /**
     * @covers TwelveSands::__construct
     */
    public function test_ts_new() {
        $ts = new TwelveSands( array() );

        $this->assertNotNull( $ts );
    }

}
