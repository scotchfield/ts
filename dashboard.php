<?php

class TSDashboard extends ArcadiaComponent {
    function __construct() {
        add_state( 'do_page_content', FALSE,
                   array( $this, 'content_dashboard' ) );

        add_state( 'do_page_content', FALSE,
                   array( $this, 'content_zone' ) );
    }

    public function is_dev() {
        global $ag;

        if ( ( isset( $ag->char[ 'info' ][ 'dev' ] ) ) &&
             ( $ag->char[ 'info' ][ 'dev' ] ) ) {
            return TRUE;
        }

        return FALSE;
    }

    public function content_dashboard( $args ) {
        global $ag;

        if ( strcmp( 'dashboard', $ag->get_state() ) ) {
            return;
        }

        if ( ! $this->is_dev() ) {
            return;
        }

?>
<div class="row text-right">
  <h1 class="page_section">Dashboard</h1>
</div>
<div class="row text-center">
  <h2><a href="?state=dashboard_zone">Edit zones</a></h2>
</div>
<?php
    }

    public function content_zone( $args ) {
        global $ag;

        if ( strcmp( 'dashboard_zone', $ag->get_state() ) ) {
            return;
        }

        if ( ! $this->is_dev() ) {
            return;
        }

?>
<div class="row text-right">
  <h1 class="page_section">Dashboard</h1>
</div>
<div class="row text-center">
<?php

        $key = $ag->get_arg( 'key' );
        $zone = FALSE;

        if ( $key ) {
            $zone = $ag->c( 'zone' )->get_zone( $key );
        }

        if ( $zone ) {

            debug_print( $zone );

            echo( '<h2><a href="?state=dashboard_zone">' .
                  'Back to zones</a></h2>' );

        } else {

            $zone_obj = $ag->c( 'zone' )->get_all_zones();

            foreach ( $zone_obj as $zone ) {
                $meta = json_decode( $zone[ 'meta_value' ], TRUE );

                echo( '<h4><a href="?state=dashboard_zone&key=' .
                      $zone[ 'meta_key' ] . '">' . $meta[ 'name' ] .
                      '</a></h4>' );
            }

            echo( '<h2><a href="?state=dashboard">' .
                  'Back to Dashboard</a></h2>' );

        }
?>
</div>
<?php
    }
    
}
