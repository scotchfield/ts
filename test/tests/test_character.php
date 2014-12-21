<?php

class TestTSCharacter extends PHPUnit_Framework_TestCase {

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
     * @covers TSCharacter::__construct
     */
    public function test_character_new() {
        $this->assertNotFalse( $this->ag->c( 'ts_character' ) );
    }

    /**
     * @covers TSCharacter::profile_content
     */
    public function test_character_profile_content() {
        $component = new TSCharacter( $this->ag );

        ob_start();
        $result = $component->profile_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSCharacter::char_content
     */
    public function test_character_char_content_no_id() {
        $component = new TSCharacter( $this->ag );

        ob_start();
        $result = $component->char_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCharacter::char_content
     */
    public function test_character_char_content_invalid_char() {
        $component = new TSCharacter( $this->ag );

        $this->ag->set_arg( 'id', -1 );

        ob_start();
        $result = $component->char_content();
        ob_end_clean();

        $this->assertFalse( $result );

        $this->ag->clear_args();
    }

    /**
     * @covers TSCharacter::char_content
     */
    public function test_character_char_content() {
        $component = new TSCharacter( $this->ag );

        $this->ag->c( 'db' )->db_execute(
            'INSERT INTO characters ( id, user_id, character_name ) VALUES ' .
                '( 1, 1, "test" )' );
        $this->ag->set_arg( 'id', 1 );

        ob_start();
        $result = $component->char_content();
        ob_end_clean();

        $this->assertTrue( $result );

        $this->ag->clear_args();
    }

    /**
     * @covers TSCharacter::print_character
     */
    public function test_character_print_character_no_info() {
        $component = new TSCharacter( $this->ag );

        $result = $component->print_character( array() );

        $this->assertFalse( $result );
    }

}
