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

    /**
     * @covers TSZone::zone_content
     */
    public function test_zone_zone_content_no_id_no_zone() {
        $result = $this->ag->c( 'ts_zone' )->zone_content();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSZone::zone_content
     */
    public function test_zone_zone_content_simple_zone() {
        $this->ag->set_arg( 'zone_id', 1 );

        $this->ag->c( 'zone' )->add_zone( 1, '{"name":"test"}' );

        ob_start();
        $result = $this->ag->c( 'ts_zone' )->zone_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSZone::zone_content
     */
    public function test_zone_zone_content() {
        $this->ag->set_arg( 'zone_id', 1 );

        $zone = array(
            'name' => 'test',
            'description' => 'test_description',
            'actions' => array( 1 => 2 ),
            'people' => array( 3 => 4 ),
            'places' => array( 5 => 6 ),
        );

        $this->ag->c( 'zone' )->add_zone( 1, json_encode( $zone ) );

        ob_start();
        $result = $this->ag->c( 'ts_zone' )->zone_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSZone::str_to_int
     */
    public function test_zone_str_to_int_not_empty() {
        $this->assertNotEquals( '',
            $this->ag->c( 'ts_zone' )->str_to_int( 'test' ) );
    }

    /**
     * @covers TSZone::store_content
     */
    public function test_zone_store_content_no_zone_id() {
        $result = $this->ag->c( 'ts_zone' )->store_content();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSZone::store_content
     */
    public function test_zone_store_content_invalid_zone() {
        $this->ag->set_arg( 'zone_id', 'test' );

        $result = $this->ag->c( 'ts_zone' )->store_content();

        $this->assertFalse( $result );
    }


    /**
     * @covers TSZone::store_buy
     */
    public function test_zone_store_buy_no_zone_id() {
        $result = $this->ag->c( 'ts_zone' )->store_buy();

        $this->assertFalse( $result );
    }


    /**
     * @covers TSZone::store_sell
     */
    public function test_zone_store_sell_no_valid_nonce() {
        $result = $this->ag->c( 'ts_zone' )->store_sell();

        $this->assertFalse( $result );
    }


}
