<?php

class TestTSDashboard extends PHPUnit_Framework_TestCase {

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
     * @covers TSDashboard::__construct
     */
    public function test_dashboard_new() {
        $this->assertNotFalse( $this->ag->c( 'dashboard' ) );
    }

    /**
     * @covers TSDashboard::is_dev
     */
    public function test_dashboard_is_dev_no_user() {
        $result = $this->ag->c( 'dashboard' )->is_dev();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSDashboard::is_dev
     */
    public function test_dashboard_is_dev_true() {
        $this->ag->char = array( 'info' => array( 'dev' => 1 ) );

        $result = $this->ag->c( 'dashboard' )->is_dev();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSDashboard::content_dashboard
     */
    public function test_dashboard_content_dashboard_no_user() {
        $result = $this->ag->c( 'dashboard' )->content_dashboard();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSDashboard::content_dashboard
     */
    public function test_dashboard_content_dashboard_dev_user() {
        $this->ag->char = array( 'info' => array( 'dev' => 1 ) );

        ob_start();
        $result = $this->ag->c( 'dashboard' )->content_dashboard();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSDashboard::content_zone
     */
    public function test_dashboard_content_zone_no_user() {
        $result = $this->ag->c( 'dashboard' )->content_zone();

        $this->assertFalse( $result );
    }

    /**
     * @covers TSDashboard::content_zone
     */
    public function test_dashboard_content_zone_dev_user() {
        $this->ag->char = array( 'info' => array( 'dev' => 1 ) );

        ob_start();
        $result = $this->ag->c( 'dashboard' )->content_zone();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSDashboard::content_zone
     */
    public function test_dashboard_content_zone_id_exists_no_zone() {
        $this->ag->char = array( 'info' => array( 'dev' => 1 ) );

        $this->ag->set_arg( 'id', -1 );

        ob_start();
        $result = $this->ag->c( 'dashboard' )->content_zone();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSDashboard::content_zone
     */
    public function test_dashboard_content_zone_exists() {
        $this->ag->char = array( 'info' => array( 'dev' => 1 ) );

        $zone_id = $this->ag->c( 'ts_zone' )->str_to_int( 1 );

        $this->ag->c( 'zone' )->add_zone( $zone_id, 'test' );

        $this->ag->set_arg( 'id', 1 );

        ob_start();
        $result = $this->ag->c( 'dashboard' )->content_zone();
        ob_end_clean();

        $this->assertTrue( $result );
    }

    /**
     * @covers TSDashboard::content_zone
     */
    public function test_dashboard_content_zone_list_zones() {
        $this->ag->char = array( 'info' => array( 'dev' => 1 ) );

        $this->ag->c( 'zone' )->add_zone( 1, 'test' );

        ob_start();
        $result = $this->ag->c( 'dashboard' )->content_zone();
        ob_end_clean();

        $this->assertTrue( $result );
    }





}
