<?php

class TestTwelveSands extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $ag = new ArcadiaGame();

        $ag->set_component( 'common', new ArcadiaCommon() );
        $ag->set_component( 'db',
            new ArcadiaDb( DB_ADDRESS, DB_NAME, DB_USER, DB_PASSWORD ) );
        $ag->set_component( 'user', new ArcadiaUser( $ag ) );

        $this->ag = $ag;
        $this->ts = new TwelveSands( $ag );
    }

    /**
     * @covers TwelveSands::__construct
     */
    public function test_ts_new() {
        $this->assertNotNull( $this->ts );

        $this->assertNotFalse( $this->ag->c( 'dashboard' ) );
    }

    /**
     * @covers TwelveSands::default_state
     */
    public function test_ts_default_state_no_user() {
        $this->ts->default_state();

        $this->assertEquals( $this->ag->get_state(), 'title' );
    }

    /**
     * @covers TwelveSands::default_state
     */
    public function test_ts_default_state_no_char() {
        $this->ag->user = TRUE;

        $this->ts->default_state();

        $this->assertEquals( $this->ag->get_state(), 'select' );
    }

    /**
     * @covers TwelveSands::default_state
     */
    public function test_ts_default_state_user_and_char() {
        $this->ag->user = TRUE;
        $this->ag->char = TRUE;

        $this->ts->default_state();

        $this->assertEquals( $this->ag->get_state(), 'zone' );
    }

    /**
     * @covers TwelveSands::login
     */
    public function test_ts_login_no_user() {
        $result = $this->ts->login();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::login
     */
    public function test_ts_login_no_user_component() {
        $ag = new ArcadiaGame();
        $ts = new TwelveSands( $ag );

        $ag->char = array( 'id' => 1 );

        $result = $ts->login();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::login
     */
    public function test_ts_login() {
        $this->ag->char = array( 'id' => 1 );

        $result = $this->ts->login();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::tip_print
     */
    public function test_ts_tip_print_no_char() {
        $result = $this->ts->tip_print();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::tip_print
     */
    public function test_ts_tip_print() {
        $this->ag->char = array( 'id' => 1 );

        $this->ag->c( 'user' )->add_character_meta( 1,
            ts_meta_type_character, TS_TIP, 'test' );

        ob_start();
        $result = $this->ts->tip_print();
        ob_end_clean();

        $this->assertTrue( $result );
        $this->assertEmpty( $this->ag->c( 'user' )->character_meta(
            ts_meta_type_character, TS_TIP ) );
    }

    /**
     * @covers TwelveSands::unpack_character
     */
    public function test_ts_unpack_character_no_char() {
        $result = $this->ts->unpack_character();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::unpack_character
     */
    public function test_ts_unpack_character_empty_metadata() {
        $this->ag->char = array( 'id' => 1 );

        $result = $this->ts->unpack_character();

        $this->assertTrue( $result );
        $this->assertTrue( isset( $this->ag->char[ 'info' ] ) );
        $this->assertTrue( isset( $this->ag->char[ 'equipped' ] ) );
        $this->assertFalse( isset( $this->ag->char[ 'encounter' ] ) );
    }

    /**
     * @covers TwelveSands::pack_character
     */
    public function test_ts_pack_character_no_char() {
        $result = $this->ts->pack_character();

        $this->assertFalse( $result );
    }






}
