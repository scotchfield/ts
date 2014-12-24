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
        $this->ag->c( 'db' )->execute( 'DELETE FROM game_meta' );

        unset( $this->ts );
        unset( $this->ag );
    }

    /**
     * @covers TSZone::__construct
     */
    public function test_zone_new() {
        $this->assertNotFalse( $this->ag->c( 'ts_zone' ) );
    }

    /**
     * @covers TSZone::get_zone
     */
    public function test_zone_get_zone_no_exists() {
        $result = $this->ag->c( 'ts_zone' )->get_zone( -1 );

        $this->assertFalse( $result );
    }

    /**
     * @covers TSZone::get_zone
     */
    public function test_zone_get_zone_no_meta() {
        $this->ag->c( 'zone' )->add_zone( 1, 'test' );

        $result = $this->ag->c( 'ts_zone' )->get_zone( 1 );

        $this->assertFalse( $result );
    }

    /**
     * @covers TSZone::get_zone
     */
    public function test_zone_get_zone_valid() {
        $this->ag->c( 'zone' )->add_zone( 1, '{"name":"test"}' );

        $result = $this->ag->c( 'ts_zone' )->get_zone( 1 );

        $this->assertEquals( $result, array( 'key_type' => '203',
            'meta_key' => '1', 'name' => 'test' ) );
    }

}