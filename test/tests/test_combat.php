<?php

class TestTSCombat extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $ag = new ArcadiaGame();

        $ag->set_component( 'common', new ArcadiaCommon() );
        $ag->set_component( 'db',
            new ArcadiaDb( DB_ADDRESS, DB_NAME, DB_USER, DB_PASSWORD ) );
        $ag->set_component( 'user', new ArcadiaUser( $ag ) );

        $this->ag = $ag;
        $this->ts = new TwelveSands( $ag );

        $ag->c( 'db' )->execute(
            'INSERT INTO game_meta ( key_type, meta_key, meta_value ) ' .
                'VALUES ( ?, 1, \'{"name":"test","health":1}\' )',
            array( $ag->c( 'npc' )->get_flag_game_meta() ) );
    }

    public function tearDown() {
        $this->ag->c( 'db' )->execute(
            'DELETE FROM game_meta WHERE key_type=?',
            array( $this->ag->c( 'npc' )->get_flag_game_meta() ) );

        unset( $this->ts );
        unset( $this->ag );
    }

    /**
     * @covers TSCombat::__construct
     */
    public function test_combat_new() {
        $this->assertNotFalse( $this->ag->c( 'ts_combat' ) );
    }

    /**
     * @covers TSCombat::get_npc
     */
    public function test_combat_get_npc_does_not_exist() {
        $result = $this->ag->c( 'ts_combat' )->get_npc( -1 );

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCombat::get_npc
     */
    public function test_combat_get_npc_exists() {
        $result = $this->ag->c( 'ts_combat' )->get_npc( 1 );

        $this->assertEquals( $result,
            array( 'key_type' => '208', 'meta_key' => '1',
                   'name' => 'test', 'id' => '1',
                   'health' => 1, 'health_max' => 1 ) );
    }

}