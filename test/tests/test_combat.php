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
        $this->ag->c( 'db' )->execute( 'DELETE FROM game_meta' );

        unset( $this->ts );
        unset( $this->ag );
    }

    private function getTestCharacter() {
        return array(
            'id' => 1,
            'name' => 'test_name',
            'character_name' => 'test_character_name',
            'health' => 10,
            'health_max' => 10,
            'stamina' => 10,
            'stamina_max' => 10,
            'text' => 'test_text',
            'info' => array(
                'stamina' => 10, 'health' => 10, 'health_max' => 10, ),
        );
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

    /**
     * @covers TSCombat::combat_content
     */
    public function test_combat_content_no_id() {
        $result = $this->ag->c( 'ts_combat' )->combat_content();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCombat::combat_content
     */
    public function test_combat_content_no_zone() {
        $this->ag->set_arg( 'zone_id', 'test' );

        $result = $this->ag->c( 'ts_combat' )->combat_content();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCombat::combat_content
     */
    public function test_combat_content_no_combat_id() {
        $this->ag->c( 'zone' )->add_zone( 1, '{"name":"test"}' );
        $this->ag->set_arg( 'zone_id', 1 );

        ob_start();
        $result = $this->ag->c( 'ts_combat' )->combat_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCombat::combat_content
     */
    public function test_combat_content_no_npc_for_combat_id() {
        $this->ag->c( 'zone' )->add_zone( 1, '{"combat_id":{"1":[-1]}}' );
        $this->ag->set_arg( 'zone_id', 1 );
        $this->ag->set_arg( 'combat_id', 1 );

        ob_start();
        $result = $this->ag->c( 'ts_combat' )->combat_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCombat::combat_content
     * @covers TSCombat::get_combat
     */
    public function test_combat_content_invalid_npc_meta() {
        $this->ag->c( 'zone' )->add_zone( 1, '{"combat_id":{"1":[1]}}' );
        $this->ag->set_arg( 'zone_id', 1 );
        $this->ag->set_arg( 'combat_id', 1 );

        ob_start();
        $result = $this->ag->c( 'ts_combat' )->combat_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSCombat
     */
    public function test_combat_content_sample_characters() {
        $this->ag->c( 'zone' )->add_zone( 1,
            '{"name":"test","combat_id":{"1":[2]}}' );
        $this->ag->set_arg( 'zone_id', 1 );
        $this->ag->set_arg( 'combat_id', 1 );

        $this->ag->char = $this->getTestCharacter();

        $npc = $this->getTestCharacter();
        $npc[ 'id' ] = 2;
        $npc[ 'health' ] = 1;
        $npc[ 'info' ][ 'health' ] = 1;
        $this->ag->c( 'db' )->execute(
            'INSERT INTO game_meta ( key_type, meta_key, meta_value ) ' .
                'VALUES ( ?, 2, ? )',
            array( $this->ag->c( 'npc' )->get_flag_game_meta(),
                   json_encode( $npc ) ) );

        ob_start();
        $result = $this->ag->c( 'ts_combat' )->combat_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSCombat
     */
    public function test_combat_content_sample_characters_npc_win() {
        $this->ag->c( 'zone' )->add_zone( 1,
            '{"name":"test","combat_id":{"1":[2]}}' );
        $this->ag->set_arg( 'zone_id', 1 );
        $this->ag->set_arg( 'combat_id', 1 );

        $this->ag->char = $this->getTestCharacter();
        $this->ag->char[ 'info' ][ 'health' ] = 1;
        $this->ag->char[ 'info' ][ 'health_max' ] = 1;

        $npc = $this->getTestCharacter();
        $npc[ 'id' ] = 2;
        $this->ag->c( 'db' )->execute(
            'INSERT INTO game_meta ( key_type, meta_key, meta_value ) ' .
                'VALUES ( ?, 2, ? )',
            array( $this->ag->c( 'npc' )->get_flag_game_meta(),
                   json_encode( $npc ) ) );

        ob_start();
        $result = $this->ag->c( 'ts_combat' )->combat_content();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSCombat
     */
    public function test_combat_content_too_tired() {
        $this->ag->c( 'zone' )->add_zone( 1,
            '{"name":"test","combat_id":{"1":[2]}}' );
        $this->ag->set_arg( 'zone_id', 1 );
        $this->ag->set_arg( 'combat_id', 1 );

        $this->ag->char = $this->getTestCharacter();
        $this->ag->char[ 'info' ][ 'stamina' ] = 0;

        $npc = $this->getTestCharacter();
        $npc[ 'id' ] = 2;
        $this->ag->c( 'db' )->execute(
            'INSERT INTO game_meta ( key_type, meta_key, meta_value ) ' .
                'VALUES ( ?, 2, ? )',
            array( $this->ag->c( 'npc' )->get_flag_game_meta(),
                   json_encode( $npc ) ) );

        ob_start();
        $result = $this->ag->c( 'ts_combat' )->combat_content();
        ob_end_clean();

        $this->assertFalse( $result );
    }





}
