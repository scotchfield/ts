<?php

function ts_get_zone( $zone_id ) {
    $zone = get_zone( $zone_id );

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
    global $character;

    if ( strcmp( 'zone', game_get_action() ) ) {
        return;
    }
//todo: need to do this?
}

add_action( 'action_set', 'ts_zone_set' );

function ts_zone_content() {
    global $character;

    if ( strcmp( 'zone', game_get_action() ) ) {
       return;
    }

//todo: allow converting strings to numeric (i.e. erebus to 93928340)
    $zone_id = 1; //todo: define as constant TS_STARTING_ZONE
    if ( isset( $_GET[ 'zone_id' ] ) ) {
        $zone_id = $_GET[ 'zone_id' ];
    }

    if ( ! is_numeric( $zone_id ) ) {
        $zone_id = ts_str_to_int( $zone_id );
        debug_print( 'zone_id changed to ' . $zone_id );
    }

    $zone = ts_get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return;
    }

    debug_print( $zone );
?>
<div class="row">
  <h3><?php echo( $zone[ 'name' ] ); ?></h3>
  <h4><?php echo( $zone[ 'description' ] ); ?></h4>
</div>
<?php
}

add_action( 'do_page_content', 'ts_zone_content' );

function ts_str_to_int( $st ) {
    $x = substr( md5( $st ), 0, 8 );
    return base_convert( $x, 16, 10 );
}
