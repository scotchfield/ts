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

/*    $zone_id = GAME_STARTING_ZONE;
    if ( '' != character_meta( ts_meta_type_character, TS_CURRENT_ZONE ) ) {
        $zone_id = character_meta( ts_meta_type_character, TS_CURRENT_ZONE );
    }

    $zone = get_zone( $zone_id );
    $zone_transitions = get_zone_transitions( $zone_id );
    $zone[ 'meta' ] = explode_meta( $zone[ 'zone_meta' ] );

    $npc_obj = array();
    if ( isset( $zone[ 'meta' ][ 'npcs' ] ) ) {
        $npc_id_obj = explode( ',', $zone[ 'meta' ][ 'npcs' ] );
        foreach ( $npc_id_obj as $npc_id ) {
            $npc_obj[ $npc_id ] = get_npc_by_id( $npc_id );
        }
    }

    echo( '<div class="row"><div class="col-xs-8">' .
          '<h3>' . $zone[ 'zone_title' ] . '</h3>' .
          '<p class="lead">' . $zone[ 'zone_description' ] . '</p>' .
          '</div><div class="col-xs-4">' );

    if ( count( $npc_obj ) > 0 ) {
        echo( '<h4 class="text-right">Others here</h4><ul>' );
        foreach ( $npc_obj as $zn ) {
            echo( '<li class="text-right"><a href="' . GAME_URL .
                  '?action=npc&amp;id=' .
                  $zn[ 'id' ] . '">' . $zn[ 'npc_name' ] .
                  '</a></li>' );
        }
        echo( '</ul>' );
    }

    echo '<h4 class="text-right">Go somewhere else</h4><ul>';
    foreach ( $zone_transitions as $zt ) {
        echo '<li class="text-right"><a href="' . GAME_URL .
             '?action=zone&amp;zone_tag=' .
             $zt[ 'zone_tag' ] . '">' . $zt[ 'zone_title' ] .
             '</a></li>';
    }
    echo '</ul>';

    echo( '</div></div>' );

    if ( ! strcmp( 'combat', $zone[ 'zone_type' ] ) ) {


        echo '<h3 class="text-center">' .
             '<a href="game-setting.php?setting=start_combat">' .
             'Start combat!</a></h3>';
    } else if ( ! strcmp( 'store', $zone[ 'zone_type' ] ) ) {


    }
/**/
}

add_action( 'do_page_content', 'ts_zone_content' );

function ts_str_to_int( $st ) {
    $x = md5( $st );
    return base_convert( $x, 16, 10 ) & 0xffffffff;
}
