<?php

class TestTSZone extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $ag = new ArcadiaGame();

        $ag->set_component( 'common', new ArcadiaCommon() );
        $ag->set_component( 'db',
            new ArcadiaDb( DB_ADDRESS, DB_NAME, DB_USER, DB_PASSWORD ) );
        $ag->set_component( 'user', new ArcadiaUser( $ag ) );

        $this->ag = $ag;
        $this->ts = new TwelveSands( $ag );
    }

    public function tearDown() {
        unset( $this->ts );
        unset( $this->ag );
    }

    /**
     * @covers TSZone::__construct
     */
    public function test_zone_new() {
        $this->assertNotNull( $this->ts->zone );
    }

}