<?php

class TestTSTitle extends PHPUnit_Framework_TestCase {

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
     * @covers TSTitle::__construct
     */
    public function test_title_new() {
        $this->assertNotFalse( $this->ag->c( 'ts_title' ) );
    }

    /**
     * @covers TSTitle::title_content
     */
    public function test_title_content_with_char() {
        $this->ag->char = array( 'id' => 1 );

        ob_start();
        $result = $this->ag->c( 'ts_title' )->title_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSTitle::title_content
     */
    public function test_title_content_no_char() {
        ob_start();
        $result = $this->ag->c( 'ts_title' )->title_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSTitle::title_content
     */
    public function test_title_content_with_notify() {
        $this->ag->set_arg( 'notify', 1 );

        ob_start();
        $result = $this->ag->c( 'ts_title' )->title_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }


}