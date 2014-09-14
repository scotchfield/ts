<?php

function ts_zone_set() {
    global $character;

    if ( strcmp( 'zone', game_get_action() ) ) {
        return;
    }

// todo: think about how to handle zone assignment in twelve sands

/*    if ( ! isset( $_GET[ 'zone_id' ] ) || ! isset( $_GET[ 'zone_id' ] ) ) {
        return;
    }

    $zone = get_zone_by_tag( $_GET[ 'zone_tag' ] );
    if ( FALSE != $zone ) {
        return;
    }*/

/*    if ( '' != character_meta( ts_meta_type_character, TS_CURRENT_ZONE ) ) {
        update_character_meta( $character[ 'id' ], ts_meta_type_character,
            TS_CURRENT_ZONE, $zone_id );
    } else {
        add_character_meta( $character[ 'id' ], ts_meta_type_character,
            TS_CURRENT_ZONE, $zone_id );
    }

    $character[ 'meta' ][ ts_meta_type_character ][ TS_CURRENT_ZONE ] =
        $zone_id;*/
}

add_action( 'action_set', 'ts_zone_set' );

function ts_zone_content() {
    global $character;

    if ( strcmp( 'zone', game_get_action() ) ) {
       return;
    }

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
