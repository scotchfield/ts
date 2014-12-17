<?php

class TSDashboard extends ArcadiaComponent {
    private $ag;

    function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'do_page_content', FALSE,
                   array( $this, 'content_dashboard' ) );

        $ag->add_state( 'do_page_content', FALSE,
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
<?php

        $id = $ag->get_arg( 'id' );
        $zone = FALSE;

        if ( $id ) {
            $key = $ag->ts->zone->str_to_int( $id );
            $zone = $ag->c( 'zone' )->get_zone( $key );
        }

        if ( $zone ) {
            $meta = json_decode( $zone[ 'meta_value' ], TRUE );

            $meta_keys = array( 'id', 'name', 'description', 'places',
                'actions', 'combat_id' );

            foreach ( $meta_keys as $k ) {
                if ( ! isset( $meta[ $k ] ) ) {
                    $meta[ $k ] = '';
                }
            }

            if ( isset( $_POST[ 'inputId' ] ) ) {
                $post_keys = array( 'inputId', 'inputName', 'inputDesc',
                    'inputPlaces', 'inputActions', 'inputCombat' );

                foreach( $post_keys as $k ) {
                    if ( ! isset( $_POST[ $k ] ) ) {
                        $_POST[ $k ] = '';
                    }
                }

                $meta_new = array(
                    'id' => $_POST[ 'inputId' ],
                    'name' => $_POST[ 'inputName' ],
                    'description' => $_POST[ 'inputDesc' ],
                    'places' => json_decode( $_POST[ 'inputPlaces' ], TRUE ),
                    'actions' => json_decode( $_POST[ 'inputActions' ], TRUE ),
                    'combat_id' => json_decode( $_POST[ 'inputCombat' ], TRUE )
                );

                $ag->c( 'zone' )->update_zone(
                    $key, json_encode( $meta_new ) );

                $new_key = $ag->ts->zone->str_to_int( $meta_new[ 'id' ] );

                if ( $new_key != $key ) {
                    $ag->c( 'zone' )->modify_zone_key( $key, $new_key );
                }

                $zone = $ag->c( 'zone' )->get_zone( $new_key );

                $meta = json_decode( $zone[ 'meta_value' ], TRUE );

                $meta_keys = array( 'id', 'name', 'description', 'places',
                    'actions', 'combat_id' );

                foreach ( $meta_keys as $k ) {
                    if ( ! isset( $meta[ $k ] ) ) {
                        $meta[ $k ] = '';
                    }
                }
            }

?>
<div class="row">
  <form class="form-horizontal" role="form" method="post">
    <input type="hidden" name="state" value="dashboard_zone">
    <div class="form-group">
      <label for="inputId" class="col-sm-3 control-label">Text ID</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="inputId" value="<?php
            echo( $meta[ 'id' ] ); ?>" name="inputId">
      </div>
    </div>
    <div class="form-group">
      <label for="inputName" class="col-sm-3 control-label">Name</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="inputName" value="<?php
            echo( $meta[ 'name' ] ); ?>" name="inputName">
      </div>
    </div>
    <div class="form-group">
      <label for="inputDesc" class="col-sm-3 control-label">Description</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="inputDesc" value="<?php
            echo( $meta[ 'description' ] ); ?>" name="inputDesc">
      </div>
    </div>
    <div class="form-group">
      <label for="inputPlaces" class="col-sm-3 control-label">Places</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="inputPlaces" value="<?php
            echo( htmlspecialchars(
                      json_encode( $meta[ 'places' ] ) ) );
            ?>" name="inputPlaces">
      </div>
    </div>
    <div class="form-group">
      <label for="inputActions" class="col-sm-3 control-label">Actions</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="inputActions" value="<?php
            echo( htmlspecialchars(
                      json_encode( $meta[ 'actions' ] ) ) );
            ?>" name="inputActions">
      </div>
    </div>
    <div class="form-group">
      <label for="inputCombat" class="col-sm-3 control-label">Combat</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="inputCombat" value="<?php
            echo( htmlspecialchars(
                      json_encode( $meta[ 'combat_id' ] ) ) );
            ?>" name="inputCombat">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-3 col-sm-9">
        <button type="submit" class="btn btn-default">Submit</button>
      </div>
    </div>
  </form>
</div>
<?php

            echo( '<h2><a href="?state=dashboard_zone">' .
                  'Back to zones</a></h2>' );

        } else {

            $zone_obj = $ag->c( 'zone' )->get_all_zones();

            foreach ( $zone_obj as $zone ) {
                $meta = json_decode( $zone[ 'meta_value' ], TRUE );

                echo( '<h4><a href="?state=dashboard_zone&id=' .
                      $meta[ 'id' ] . '">' . $meta[ 'name' ] .
                      '</a></h4>' );
            }

            echo( '<h2><a href="?state=dashboard">' .
                  'Back to Dashboard</a></h2>' );

        }
    }
    
}
