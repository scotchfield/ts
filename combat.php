<?php

function ts_get_npc( $npc_id ) {
    global $ag;

    $npc = $ag->c( 'npc' )->get_npc( $npc_id );

    if ( FALSE == $npc ) {
        return;
    }

    $meta = json_decode( $npc[ 'meta_value' ], $assoc = TRUE );
//todo: handle case where meta is false or json is erroneous
    foreach ( $meta as $k => $v ) {
        $npc[ $k ] = $v;
    }
    unset( $npc[ 'meta_value' ] );

    $npc[ 'id' ] = $npc[ 'meta_key' ];
    if ( isset( $npc[ 'health' ] ) ) {
        $npc[ 'health_max' ] = $npc[ 'health' ];
    }

    return $npc;
}

function ts_combat_content() {
    global $ag;

    if ( strcmp( 'combat', $ag->get_state() ) ) {
        return FALSE;
    }

    $zone_id = $ag->get_state_arg( 'zone_id' );

    if ( ! $zone_id ) {
        return FALSE;
    }

    if ( ! is_numeric( $zone_id ) ) {
        $zone_id = ts_str_to_int( $zone_id );
    }

    $zone = ts_get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return FALSE;
    }
?>
<div class="row text-right">
  <h1 class="page_section">Combat</h1>
</div>
<?
    $combat_id = $ag->get_state_arg( 'combat_id' );
    if ( ! $combat_id ) {
        $combat_id = 1;
    }

    if ( ! isset( $zone[ 'combat_id' ][ $combat_id ] ) ) {
        return FALSE;
    }

    $c_obj = $zone[ 'combat_id' ][ $combat_id ];
    $npc_id = $c_obj[ array_rand( $c_obj ) ];

    $npc = ts_get_npc( $npc_id );

    if ( ! $npc ) {
        return FALSE;
    }

    $combat_obj = ts_get_combat( $npc );

    if ( ! $combat_obj[ 'round' ][ 0 ][ 'initiative' ] ) {
        ts_echo_health( $combat_obj[ 'round' ][ 0 ] );
    }

    foreach ( $combat_obj[ 'round' ] as $combat ) {
        if ( $combat[ 'initiative' ] ) {
            ts_echo_health( $combat );
            echo( '<p class="attack">(<b>' .
                  $combat[ 'name_a' ] . '</b>) ' .
                  $combat[ 'attack' ][ 'text' ] . '</p>' );
        } else {
            echo( '<p class="attack">(<b>' .
                  $combat[ 'name_b' ] . '</b>) ' .
                  $combat[ 'attack' ][ 'text' ] . '</p>' );
            echo( '</div></div>' );
        }
    }

    if ( $combat_obj[ 'win' ] ) {
        echo( '</div></div><div class="row text-center">' .
              '<h2>You win!</h2>' );

        $ag->char[ 'info' ][ 'stamina' ] = max(
            0, $ag->char[ 'info' ][ 'stamina' ] - 1.0 );

        $ag->c( 'track_npc' )->set_tracking( $ag->char[ 'id' ],
            $npc[ 'meta_key' ],
            $ag->char_meta( TRACK_NPC, $npc[ 'meta_key' ], 0 ) + 1 );

        echo( '<p>You have defeated your ' .
              number_with_suffix( $ag->char_meta(
                  TRACK_NPC, $npc[ 'meta_key' ], 0 ) + 1 ) .
              ' ' . $npc[ 'name' ] . '.</p>' );

        ts_check_combat_achievements( $npc );
        
        if ( isset( $npc[ 'drops' ] ) ) {
            foreach ( $npc[ 'drops' ] as $k => $drop_rate ) {
                $roll = mt_rand( 0, 100 );
                if ( $roll < $drop_rate ) {
                    $item = $ag->c( 'item' )->get_item( $k );
                    $item = json_decode( $item[ 'meta_value' ], TRUE );

                    echo( '<p>Your foe drops some loot: ' . ts_item_popup( $item ) . '</p>' );

                    // todo: award the item; handle indices in db rows for inventory
                }
            }
        }

    } else {
        echo( '<div class="row text-center">' .
              '<h2>You are defeated!</h2>' );
        echo( '<h2>' . $npc[ 'name' ] . ' wins!</h2>' );
        echo( '<p class="lead">You take a huge stamina hit as you lurch ' .
              'back to a safe spot and heal.</p>' );

        $ag->char[ 'info' ][ 'stamina' ] = max(
            0, $ag->char[ 'info' ][ 'stamina' ] - 10.0 );
    }

    echo( '</div>' );
}

add_state( 'do_page_content', 'ts_combat_content' );

function ts_get_combat( $npc ) {
    global $ag;

    $obj = array();

    if ( FALSE == $ag->char ) {
        return $obj;
    }

    if ( $ag->char[ 'info' ][ 'stamina' ] < 1 ) {
        // return too tired;
        return $obj;
    }

    $ag->char[ 'info' ][ 'health' ] = $ag->char[ 'info' ][ 'health_max' ];

    $initiative = ts_get_initiative( $ag->char, $npc );

    $obj[ 'round' ] = array();

    $done = FALSE;

    while ( ! $done ) {
        $combat = array(
            'name_a' => $ag->char[ 'character_name' ],
            'name_b' => $npc[ 'name' ],
            'health_a' => $ag->char[ 'info' ][ 'health' ],
            'health_b' => $npc[ 'health' ],
            'health_max_a' => $ag->char[ 'info' ][ 'health_max' ],
            'health_max_b' => $npc[ 'health_max' ],
            'initiative' => $initiative,
        );

        if ( $initiative ) {
            $attack = ts_get_attack( $npc );
            $combat[ 'attack' ] = $attack;
            $npc[ 'health' ] -= $attack[ 'dmg' ];
        } else {
            $attack = ts_get_attack( $ag->char );
            $combat[ 'attack' ] = $attack;
            $ag->char[ 'info' ][ 'health' ] -= $attack[ 'dmg' ];
        }

        $obj[ 'round' ][] = $combat;

        $initiative = ! $initiative;

        if ( $ag->char[ 'info' ][ 'health' ] <= 0 ) {
            $obj[ 'win' ] = FALSE;
            $done = TRUE;
        } else if ( $npc[ 'health' ] <= 0 ) {
            $obj[ 'win' ] = TRUE;
            $done = TRUE;
        }
    }

    $ag->char[ 'info' ][ 'health' ] = $ag->char[ 'info' ][ 'health_max' ];

    return $obj;
}

function ts_get_initiative( $char_a, $char_b ) {
// todo come on
    if ( mt_rand( 0, 1 ) == 0 ) {
        return TRUE;
    }

    return FALSE;
}

function ts_get_attack( $char ) {
// todo come on
    return array( 'text' => 'The weapon hits for 1 damage!',
                  'dmg' => 1 );
}

function ts_echo_health( $combat ) {
?>
  <div class="row attack">
    <div class="col-xs-3 text-center">
      <?php echo( $combat[ 'name_a' ] ); ?><br>
      Health: <?php echo( $combat[ 'health_a' ] ); ?> /
              <?php echo( $combat[ 'health_max_a' ] ); ?>
    </div>
    <div class="col-xs-3 text-center">
      <?php echo( $combat[ 'name_b' ] ); ?><br>
      Health: <?php echo( $combat[ 'health_b' ] ); ?> /
              <?php echo( $combat[ 'health_max_b' ] ); ?>
    </div>
    <div class="col-xs-6 text-center">
<?php
}

function ts_check_combat_achievements( $npc ) {
    global $ag;

    if ( ( 1 == $npc[ 'id' ] ) &&
         ( $ag->char_meta( TRACK_NPC, $npc[ 'id' ], 0 ) > 10 ) ) {
        $ag->c( 'achievement' )->award_achievement( 1 );
    }
}
