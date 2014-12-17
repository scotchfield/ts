<?php

class TestTwelveSands extends PHPUnit_Framework_TestCase {

    /**
     * @covers TwelveSands::__construct
     */
    public function test_ts_new() {
        $ag = new ArcadiaGame();
        $ts = new TwelveSands( $ag );

        $this->assertNotNull( $ts );
    }

    /**
     * @covers TwelveSands::post_load
     */
    public function test_ts_post_load() {
        $ag = new ArcadiaGame();
        $ts = new TwelveSands( $ag );

        $ts->post_load();

        $this->assertNotFalse( $ag->c( 'dashboard' ) );
    }

    /**
     * @covers TwelveSands::default_state
     */
    public function test_ts_default_state_no_user() {
        $ag = new ArcadiaGame();
        $ts = new TwelveSands( $ag );

        $ts->default_state();

        $this->assertEquals( $ag->get_state(), 'title' );
    }

    /**
     * @covers TwelveSands::default_state
     */
    public function test_ts_default_state_no_char() {
        $ag = new ArcadiaGame();
        $ts = new TwelveSands( $ag );

        $ag->user = TRUE;

        $ts->default_state();

        $this->assertEquals( $ag->get_state(), 'select' );
    }

    /**
     * @covers TwelveSands::default_state
     */
    public function test_ts_default_state_user_and_char() {
        $ag = new ArcadiaGame();
        $ts = new TwelveSands( $ag );

        $ag->user = TRUE;
        $ag->char = TRUE;

        $ts->default_state();

        $this->assertEquals( $ag->get_state(), 'zone' );
    }

}
