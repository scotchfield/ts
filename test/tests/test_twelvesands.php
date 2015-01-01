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

    public function tearDown() {
        unset( $this->ts );
        unset( $this->ag );
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

    /**
     * @covers TwelveSands::pack_character
     */
    public function test_ts_pack_character_simple_char() {
        $this->ag->char = array( 'id' => 1,
            'info' => array( 'test' => 2 ),
            'equipped' => array( 'test' => 3 ),
            'encounter' => array( 'test' => 4 ) );

        $result = $this->ts->pack_character();

        $this->assertTrue( $result );

        $result = $this->ag->c( 'db' )->fetch_all(
            'SELECT * FROM character_meta WHERE id=1 AND meta_key=?',
            array( ts_meta_type_character ) );

        //todo: change update_*_meta to force a key to exist if it doesn't
        // none of this ensure meta stuff
        //print_r( $result );
    }

    /**
     * @covers TwelveSands::regen_stamina
     */
    public function test_ts_regen_stamina_no_char() {
        $result = $this->ts->regen_stamina();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::regen_stamina
     */
    public function test_ts_regen_stamina_char_no_stamina() {
        $this->ag->char = array( 'id' => 1 );

        $result = $this->ts->regen_stamina();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::regen_stamina
     */
    public function test_ts_regen_stamina_char() {
        $this->ag->char = array( 'id' => 1,
            'info' => array( 'stamina' => 0 ) );

        $result = $this->ts->regen_stamina();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::header_output_head
     */
    public function test_ts_header_output_head() {
        ob_start();
        $result = $this->ts->header_output_head();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::header
     */
    public function test_ts_header_no_char_no_state() {
        ob_start();
        $result = $this->ts->header();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::header
     */
    public function test_ts_header_title_state() {
        $this->ag->set_state( 'title' );

        $result = $this->ts->header();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::header
     */
    public function test_ts_header_char() {
        $this->ag->char = array( 'id' => 1, 'character_name' => 'test' );

        ob_start();
        $result = $this->ts->header();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::footer
     */
    public function test_ts_footer_no_state() {
        ob_start();
        $result = $this->ts->footer();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::footer
     */
    public function test_ts_footer_title_state() {
        $this->ag->set_state( 'title' );

        $result = $this->ts->footer();

        $this->assertFalse( $result );
    }

    /**
     * @covers TwelveSands::about
     */
    public function test_ts_about() {
        ob_start();
        $result = $this->ts->about();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TwelveSands::contact
     */
    public function test_ts_contact_no_state() {
        ob_start();
        $result = $this->ts->contact();
        ob_end_clean();

        $this->assertTrue( $result );
    }





}
