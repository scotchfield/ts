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
    }

    public function tearDown() {
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

}