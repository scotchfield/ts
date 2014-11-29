<?php

function ts_get_zone( $zone_id ) {
    global $ag;

    $zone = $ag->c( 'zone' )->get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return;
    }

    $meta = json_decode( $zone[ 'meta_value' ], $assoc = TRUE );
    foreach ( $meta as $k => $v ) {
        $zone[ $k ] = $v;
    }
    unset( $zone[ 'meta_value' ] );

    return $zone;
}

function ts_zone_set() {
    global $ag;

    if ( strcmp( 'zone', $ag->get_state() ) ) {
        return;
    }
//todo: need to do this?
}

add_state( 'state_set', 'ts_zone_set' );

function ts_zone_content() {
    global $ag;

    if ( strcmp( 'zone', $ag->get_state() ) ) {
       return;
    }

//todo: allow converting strings to numeric (i.e. erebus to 93928340)
    $zone_id = 'erebus'; //todo: define as constant TS_STARTING_ZONE
    if ( FALSE != $ag->get_state_arg( 'zone_id' ) ) {
        $zone_id = $ag->get_state_arg( 'zone_id' );
    }

    if ( ! is_numeric( $zone_id ) ) {
        $zone_id = ts_str_to_int( $zone_id );
//        debug_print( 'zone_id changed to ' . $zone_id );
    }

    $zone = ts_get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return;
    }

//    debug_print( $zone );
?>
<div class="row text-center">
  <h2><?php echo( $zone[ 'name' ] ); ?></h2>
  <p class="lead"><?php echo( $zone[ 'description' ] ); ?></p>
</div>

<div class="row">
  <div class="col-sm-6 text-center">
    <h3>Actions</h3>
<?php
    if ( isset( $zone[ 'actions' ] ) ) {
        echo( '<div class="list-group">' );
        foreach ( $zone[ 'actions' ] as $k => $v ) {
?>
<a href="<?php echo( GAME_URL ); ?>?state=zone&zone_id=<?php echo( $k ); ?>"
   class="list-group-item">
  <h4 class="list-group-item-heading"><?php echo( $v ); ?></h4>
  <p class="list-group-item-text">Herp de derp.</p>
</a>
<?php
        }
        echo( '</div>' );
    }
?>

  </div>

  <div class="col-sm-6 text-center">
    <h3>Places to go</h3>
<?php
    if ( isset( $zone[ 'places' ] ) ) {
        echo( '<div class="list-group">' );
        foreach ( $zone[ 'places' ] as $k => $v ) {
?>
<a href="<?php echo( GAME_URL ); ?>?state=zone&zone_id=<?php echo( $k ); ?>"
   class="list-group-item">
  <h4 class="list-group-item-heading"><?php echo( $v ); ?></h4>
  <p class="list-group-item-text">Herp de derp.</p>
</a>
<?php
        }
        echo( '</div>' );
    }
?>
  </div>
</div>
<?php
}

add_state( 'do_page_content', 'ts_zone_content' );

function ts_str_to_int( $st ) {
    $x = substr( md5( $st ), 0, 8 );
    return base_convert( $x, 16, 10 );
}
