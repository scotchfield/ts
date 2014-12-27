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
        $result  = $this->ag->c( 'ts_craft' )->get_recipes();

        $this->assertCount( 0, $result );
    }

}