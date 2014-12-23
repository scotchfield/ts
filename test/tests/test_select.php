<?php

class TestTSSelect extends PHPUnit_Framework_TestCase {

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
     * @covers TSSelect::__construct
     */
    public function test_select_new() {
        $this->assertNotFalse( $this->ag->c( 'ts_select' ) );
    }

    /**
     * @covers TSSelect::select_check
     */
    public function test_select_check_no_user() {

        $component = new TSSelect( $this->ag );

        $result = $component->select_check();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSSelect::select_check
     */
    public function test_select_check_no_char() {
        $component = new TSSelect( $this->ag );

        $this->ag->user = array( 'id' => 1 );

        $result = $component->select_check();

        $this->assertTrue( $result );

        unset( $this->ag->user );
    }

    /**
     * @covers TSSelect::select_check
     */
    public function test_select_check_char() {
        $component = new TSSelect( $this->ag );

        $this->ag->user = array( 'id' => 1 );
        $this->ag->char = array( 'id' => 1 );

        $result = $component->select_check();

        $this->assertFalse( $result );

        unset( $this->ag->char );
        unset( $this->ag->user );
    }

    /**
     * @covers TSSelect::select_content
     */
    public function test_select_content_no_char() {
        $component = new TSSelect( $this->ag );

        ob_start();
        $result = $component->select_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSSelect::select_content
     */
    public function test_select_content_char() {
        $component = new TSSelect( $this->ag );

        $this->ag->user = array( 'id' => 1, 'user_name' => 'test',
            'max_characters' => 2 );

        $this->ag->c( 'db' )->execute(
            'INSERT INTO characters ( user_id, character_name ) ' .
                'VALUES ( 1, "test" ) ' );

        ob_start();
        $result = $component->select_content();
        ob_end_clean();

        $this->assertTrue( $result );

        $this->ag->c( 'db' )->execute( 'DELETE FROM characters' );
    }

}
