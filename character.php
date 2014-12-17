<?php

// todo: refactor to object
global $ag;

function ts_profile_content() {
    global $ag;

    if ( strcmp( 'profile', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Profile</h1>
</div>
<?php
    ts_print_character( $ag->char );
}

$ag->add_state( 'do_page_content', FALSE, 'ts_profile_content' );

function ts_char_content() {
    global $ag;

    if ( strcmp( 'char', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Profile</h1>
</div>
<?php

    if ( ! isset( $_GET[ 'id' ] ) ) {
        return;
    }

    $char_id = intval( $_GET[ 'id' ] );
    $char = get_character_by_id( $char_id );

    if ( FALSE == $char ) {
        return;
    }

    $char[ 'meta' ] = get_character_meta( $char_id );

    $char = $ag->ts->get_unpacked_character( $char );

    ts_print_character( $char );
}

$ag->add_state( 'do_page_content', FALSE, 'ts_char_content' );


function ts_print_character( $character ) {
?>
<div class="row">
  <div class="col-md-6">
    <h2>Details</h2>

    <dl class="dl-horizontal">
      <dt>Name</dt>
      <dd><?php echo( $character[ 'character_name' ] ); ?></dd>
      <dt>Health</dt>
      <dd><?php echo( $character[ 'info' ][ 'health' ] ); ?></dd>
      <dt>Stamina</dt>
      <dd><?php echo( round(
          $character[ 'info' ][ 'stamina' ], $precision = 2 ) ); ?> /
          <?php echo( $character[ 'info' ][ 'stamina_max' ] ); ?></dd>
      <dt>Gold</dt>
      <dd><?php echo( $character[ 'info' ][ 'gold' ] ); ?></dd>
      <dt>Experience Points</dt>
      <dd><?php echo( $character[ 'info' ][ 'xp' ] ); ?></dd>
    </dl>

    <h2>Gear</h2>

    <dl class="dl-horizontal">
<?php
    $gear_obj = array(
        'weapon' => 'Weapon',
        'head' => 'Head',
        'chest' => 'Chest',
        'legs' => 'Legs',
        'neck' => 'Neck',
        'trinket' => 'Trinket',
        'hands' => 'Hands',
        'wrists' => 'Wrists',
        'belt' => 'Belt',
        'boots' => 'Boots',
        'ring' => 'Ring',
        'mount' => 'Mount',
    );

    foreach ( $gear_obj as $k => $v ) {
        echo( '<dt>' . $v . '</dt><dd>' .
              $character[ 'equipped' ][ $k ][ 'name' ] . '</dd>' );
    }
?>
    </dl>

  </div>
  <div class="col-md-6">

    <h2>Stats</h2>

    <dl class="dl-horizontal">
    </dl>

  </div>

</div>
<?php
}

function ts_achievements_content() {
    global $ag;

    if ( strcmp( 'achievements', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Achievements</h1>
</div>
<div class="row">
  <div class="col-md-6">
    <h3>Your achievements</h3>
<?php
    $achieve_obj = $ag->c( 'achievement' )->get_achievements( $ag->char[ 'id' ] );

    if ( 0 == count( $achieve_obj ) ) {
        echo( '<h4>None yet!</h4>' );
    } else {
        echo( '<dl class="dl-horizontal">' );

        foreach ( $achieve_obj as $achieve ) {
            $meta = json_decode( $achieve[ 'meta_value' ], TRUE );
            echo( '<dt>' . $meta[ 'name' ] . '</dt><dd>' .
                  $meta[ 'text' ] . '</dd><dd>' .
                  date( 'F j, Y, g:ia', $achieve[ 'timestamp' ] ) .
                  '</dd>' );
        }
        echo( '</dl>' );
    }
?>
  </div>
  <div class="col-md-6">
    <h3>Achievements Remaining</h3>
    <dl class="dl-horizontal">
<?php
    $all_achieve_obj = $ag->c( 'achievement' )->get_all_achievements();

    foreach ( $all_achieve_obj as $k => $achieve ) {
        if ( isset( $achieve_obj[ $k ] ) ) {
            continue;
        }
        $meta = json_decode( $achieve[ 'meta_value' ], TRUE );
        echo( '<dt>' . $meta[ 'name' ] . '</dt><dd>' .
              $meta[ 'text' ] . '</dd>' );
    }
?>
  </dl>
  </div>
</div>
<?php
}

$ag->add_state( 'do_page_content', FALSE, 'ts_achievements_content' );

function ts_inventory_content() {
    global $ag;

    if ( strcmp( 'inventory', $ag->get_state() ) ) {
       return;
    }

    $inventory_obj = $ag->ts->get_inventory();

?>
<div class="row text-right">
  <h1 class="page_section">Inventory</h1>
</div>
<div class="row">
  <h2>Currently Holding:</h2>
  <ul>
  <?php

    echo( '<div class="row">' );
    $counter = 0;
    foreach ( $inventory_obj as $item ) {
        $meta = $item[ 'meta' ];

        echo( '<div class="col-sm-4">' . $ag->ts->item_div( $meta ) );
        if ( isset( $meta[ 'sell' ] ) ) {
            echo ( '<br>' .
                '<a href="game-setting.php?state=store_sell&sell=' .
                $item[ 'meta_key' ] . '&nonce=' .
                $ag->c( 'common' )->nonce_create(
                    'sell' . $item[ 'meta_key' ] ) .
                '">Sell for ' . $meta[ 'sell' ][ 0 ] .
                ' gold</a>' );
        }
        if ( isset( $meta[ 'slot' ] ) ) {
            echo( '<br>' .
                '<a href="game-setting.php?state=equip&equip=' .
                $item[ 'meta_key' ] . '&nonce=' .
                $ag->c( 'common' )->nonce_create(
                    'equip' . $item[ 'meta_key' ] ) .
                '">Equip</a>' );
        }
        echo( '</div>' );

        $counter += 1;
        if ( $counter >= 3 ) {
            echo( '</div><div class="row">' );
            $counter = 0;
        }
    }
    echo( '</div>' );


?>
</div>
<?php
}

$ag->add_state( 'do_page_content', FALSE, 'ts_inventory_content' );


function ts_equip_item() {
    global $ag;

    $inv_id = $ag->get_arg( 'equip' );

    if ( ! $ag->c( 'common' )->nonce_verify(
           $ag->get_arg( 'nonce' ), $state = 'equip' . $inv_id ) ) {
        return FALSE;
    }

    $inventory_obj = $ag->ts->get_inventory();

    if ( ! isset( $inventory_obj[ $inv_id ] ) ) {
        return FALSE;
    }

    $item = $inventory_obj[ $inv_id ];

    if ( ! isset( $item[ 'meta' ][ 'slot' ] ) ) {
        return FALSE;
    }

    $slot = $item[ 'meta' ][ 'slot' ];

    if ( 0 != $ag->char[ 'equipped' ][ $slot ][ 'id' ] ) {
        $equipped_item = json_encode( $ag->char[ 'equipped' ][ $slot ] );
        $ag->c( 'inventory' )->award_item( $ag->char[ 'id' ], $equipped_item );
    }

    $ag->c( 'inventory' )->remove_item( $ag->char[ 'id' ], $inv_id );

    $ag->char[ 'equipped' ][ $slot ] = $item[ 'meta' ];

    update_character_meta( $ag->char[ 'id' ], ts_meta_type_character,
        TS_TIP, $item[ 'meta' ][ 'name' ] . ' equipped.' );

    $ag->set_redirect_header( GAME_URL . '?state=profile' );
}

$ag->add_state( 'do_setting', 'equip', 'ts_equip_item' );
