<?php

class TestTSCraft extends PHPUnit_Framework_TestCase {

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
     * @covers TSCraft::__construct
     */
    public function test_craft_new() {
        $this->assertNotFalse( $this->ag->c( 'ts_craft' ) );
    }

    /**
     * @covers TSCraft::get_recipe
     */
    public function test_craft_get_recipe_does_not_exist() {
        $result = $this->ag->c( 'ts_craft' )->get_recipe( -1 );

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCraft::get_recipe
     */
    public function test_craft_get_recipe_exists() {
        $this->ag->c( 'db' )->execute(
            'INSERT INTO game_meta ( key_type, meta_key, meta_value ) ' .
                'VALUES ( ?, 1, "test" )',
            array( $this->ag->c( 'ts_craft' )->get_flag_game_meta() ) );

        $result = $this->ag->c( 'ts_craft' )->get_recipe( 1 );

        $this->assertEquals( $result, array(
            'key_type' => $this->ag->c( 'ts_craft' )->get_flag_game_meta(),
            'meta_key' => '1', 'meta_value' => 'test' ) );
    }

    /**
     * @covers TSCraft::get_recipes
     */
    public function test_craft_get_recipes_does_not_exist() {
        $result = $this->ag->c( 'ts_craft' )->get_recipes();

        $this->assertCount( 0, $result );
    }

    /**
     * @covers TSCraft::get_recipes
     */
    public function test_craft_get_recipes_type_exists() {
        $this->ag->c( 'db' )->execute(
            'INSERT INTO game_meta ( key_type, meta_key, meta_value ) ' .
                'VALUES ( ?, 100, \'{"type":100}\' ), ' .
                '( ?, 101, \'{"type":101}\' )',
            array( $this->ag->c( 'ts_craft' )->get_flag_game_meta(),
                   $this->ag->c( 'ts_craft' )->get_flag_game_meta() ) );

        $result = $this->ag->c( 'ts_craft' )->get_recipes( $type = 100 );

        $this->assertCount( 1, $result );
    }

    /**
     * @covers TSCraft::get_items_string
     */
    public function test_craft_get_items_string_empty() {
        $this->assertEquals( '', $this->ag->c( 'ts_craft' )->get_items_string(
            array() ) );
    }


    /**
     * @covers TSCraft::get_items_string
     */
    public function test_craft_get_items_string_single() {
        $result = $this->ag->c( 'ts_craft' )->get_items_string(
            array( array( FALSE, 'test', 1 ) ) );

        $this->assertEquals( '1x test', $result );
    }

    /**
     * @covers TSCraft::get_items_string
     */
    public function test_craft_get_items_string_double() {
        $result = $this->ag->c( 'ts_craft' )->get_items_string(
            array( array( FALSE, 'test', 1 ), array( FALSE, 'test2', 2 ) ) );

        $this->assertEquals( '1x test, 2x test2', $result );
    }

    /**
     * @covers TSCraft::craft_content
     */
    public function test_craft_craft_content_no_type() {
        $result = $this->ag->c( 'ts_craft' )->craft_content();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCraft::craft_content
     */
    public function test_craft_craft_content_type() {
        $this->ag->set_arg( 'type', TSCraft::type_crafting );

        ob_start();
        $result = $this->ag->c( 'ts_craft' )->craft_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }


    /**
     * @covers TSCraft::craft_create
     */
    public function test_craft_craft_create_no_recipe() {
        $result = $this->ag->c( 'ts_craft' )->craft_create();

        $this->assertFalse( $result );
    }




}
