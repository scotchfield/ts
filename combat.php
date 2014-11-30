<?php

function ts_combat_content() {
    global $ag;

    if ( strcmp( 'combat', $ag->get_state() ) ) {
        return;
    }

    $zone_id = $ag->get_state_arg( 'zone_id' );

    if ( ! $zone_id ) {
        return;
    }

    if ( ! is_numeric( $zone_id ) ) {
        $zone_id = ts_str_to_int( $zone_id );
    }

    $zone = ts_get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return;
    }
?>
<div class="row text-right">
  <h1 class="page_section">Combat</h1>
</div>
<?
    $combat_id = $ag->get_state_arg( 'combat_id' );

    if ( ! isset( $zone[ 'combat_id' ][ $combat_id ] ) ) {
        return;
    }

    $c_obj = $zone[ 'combat_id' ][ $combat_id ];
    $npc_id = $c_obj[ array_rand( $c_obj ) ];

    $npc = $ag->c( 'npc' )->get_npc( $npc_id );

    debug_print( $npc );


}

add_state( 'do_page_content', 'ts_combat_content' );



