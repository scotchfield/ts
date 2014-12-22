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

    /**
     * @covers TSCharacter::print_character
     */
    public function test_character_print_character_no_equipped() {
        $component = new TSCharacter( $this->ag );

        $result = $component->print_character( array( 'info' => array() ) );

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCharacter::print_character
     */
    public function test_character_print_character_no_character_name() {
        $component = new TSCharacter( $this->ag );

        $result = $component->print_character(
            array( 'id' => 1, 'info' => array(), 'equipped' => array() ) );

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCharacter::print_character
     */
    public function test_character_print_character() {
        $component = new TSCharacter( $this->ag );

        ob_start();
        $result = $component->print_character(
            array( 'id' => 1,
                   'equipped' => array( 'weapon' => array( 'name' => 'a' ) ),
                   'character_name' => 'test',
                   'info' => array(
                       'health' => 100, 'stamina' => 100, 'stamina_max' => 100,
                       'gold' => 100, 'xp' => 100 ) ) );
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSCharacter::achievements_content
     */
    public function test_character_achievements_content_empty() {
        $component = new TSCharacter( $this->ag );

        ob_start();
        $result = $component->achievements_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSCharacter::inventory_content
     */
    public function test_character_inventory_content_empty() {
        $component = new TSCharacter( $this->ag );

        ob_start();
        $result = $component->inventory_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSCharacter::equip_item
     */
    public function test_character_equip_item_empty() {
        $component = new TSCharacter( $this->ag );

        $result = $component->equip_item();

        $this->assertFalse( $result );
    }


}
