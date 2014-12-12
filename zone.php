<?php

function ts_get_zone( $zone_id ) {
    global $ag;

    $zone = $ag->c( 'zone' )->get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return;
    }

    $meta = json_decode( $zone[ 'meta_value' ], $assoc = TRUE );
//todo: handle case where meta is false or json is erroneous
    foreach ( $meta as $k => $v ) {
        $zone[ $k ] = $v;
    }
    unset( $zone[ 'meta_value' ] );

    return $zone;
}

function ts_zone_content() {
    global $ag;

    $zone_id = 'erebus'; //todo: define as constant TS_STARTING_ZONE
    if ( FALSE != $ag->get_arg( 'zone_id' ) ) {
        $zone_id = $ag->get_arg( 'zone_id' );
    }

    if ( ! is_numeric( $zone_id ) ) {
        $zone_id = ts_str_to_int( $zone_id );
        //$ag->debug_print( 'zone_id changed to ' . $zone_id );
    }

    $zone = ts_get_zone( $zone_id );

    if ( FALSE == $zone ) {
        return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Zone</h1>
</div>
<div class="row">
  <div class="col-md-8">
    <h1><?php echo( $zone[ 'name' ] ); ?></h1>
    <p class="lead"><?php echo( $zone[ 'description' ] ); ?></p>
  </div>
  <div class="col-md-4">
<?php
    if ( isset( $zone[ 'actions' ] ) ) {
        echo( '<h3>Actions</h3>' );
        echo( '<div class="list-group">' );
        foreach ( $zone[ 'actions' ] as $k => $v ) {
?>
<a href="<?php echo( GAME_URL ); ?>?<?php echo( $k ); ?>"
   class="list-group-item">
  <h4 class="list-group-item-heading"><?php echo( $v ); ?></h4>
  <p class="list-group-item-text">Herp de derp.</p>
</a>
<?php
        }
        echo( '</div>' );
    }

    if ( isset( $zone[ 'places' ] ) ) {
        echo( '<h3>Places to go</h3>' );
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

add_state( 'do_page_content', 'zone', 'ts_zone_content' );

function ts_str_to_int( $st ) {
    $x = substr( md5( $st ), 0, 8 );
    return base_convert( $x, 16, 10 );
}

function ts_store_content() {
    global $ag;

    $zone_id = $ag->get_arg( 'zone_id' );

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

    if ( ! isset( $zone[ 'store_id' ] ) ) {
        return FALSE;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Store</h1>
</div>
<?

    $item_obj = $ag->c( 'item' )->get_item_list( $zone[ 'store_id' ] );

    echo( '<div class="row">' );
    foreach ( $item_obj as $item ) {
        $item_meta = json_decode( $item[ 'meta_value' ], TRUE );

        if ( ! isset( $item_meta[ 'buy' ] ) ) {
            continue;
        }

        echo( '<div class="col-sm-4">' .
              '<div class="text-center">' .
              '<a href="game-setting.php?setting=store_buy&zone_id=' .
              $ag->get_arg( 'zone_id' ) . '&buy=' . $item[ 'meta_key' ] .
              '&nonce=' . $ag->c( 'common' )->nonce_create(
                  'buy' . $item[ 'meta_key' ] ) .
              '">Buy for ' . $item_meta[ 'buy' ][ 0 ] . ' gold</a></div>' );
        //todo buy should be an array of item/quantities

        echo( ts_item_div( $item_meta ) . '</div>' );
    }
    echo( '</div>' );

}

add_state( 'do_page_content', 'store', 'ts_store_content' );

function ts_store_buy() {
    global $ag;

    $zone_id = $ag->get_arg( 'zone_id' );

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

    if ( ! isset( $zone[ 'store_id' ] ) ) {
        return FALSE;
    }

    $item_id = $ag->get_arg( 'buy' );

    if ( ! $ag->c( 'common' )->nonce_verify(
               $ag->get_arg( 'nonce' ), $state = 'buy' . $item_id ) ) {
        return FALSE;
    }

    if ( ! in_array( $item_id, $zone[ 'store_id' ] ) ) {
        return FALSE;
    }

    $item = $ag->c( 'item' )->get_item( $item_id );

    if ( ! $item ) {
        return FALSE;
    }

    $item_meta = json_decode( $item[ 'meta_value' ], TRUE );

    if ( ! isset( $item_meta[ 'buy' ] ) ) {
        return FALSE;
    }

    if ( $ag->char[ 'info' ][ 'gold' ] < $item_meta[ 'buy' ][ 0 ] ) {
        // todo: set tip to say you don't have enough gold
        return FALSE;
    }

    $ag->char[ 'info' ][ 'gold' ] -= $item_meta[ 'buy' ][ 0 ];
    $ag->c( 'inventory' )->award_item(
        $ag->char[ 'id' ], $item[ 'meta_value' ] );

    update_character_meta( $ag->char[ 'id' ], ts_meta_type_character,
        TS_TIP, $item_meta[ 'name' ] . ' purchased for ' .
        $item_meta[ 'buy' ][ 0 ] . ' gold.' );

    $ag->set_redirect_header( GAME_URL .
        '?state=store&zone_id=' . $ag->get_arg( 'zone_id' ) );
}

// todo: this global has to go..
$GLOBALS[ 'setting_map' ][ 'store_buy' ] = 'ts_store_buy';




function ts_store_sell() {
    global $ag;

    $inv_id = $ag->get_arg( 'sell' );

    if ( ! $ag->c( 'common' )->nonce_verify(
           $ag->get_arg( 'nonce' ), $state = 'sell' . $inv_id ) ) {
        return FALSE;
    }

    $inventory_obj = $ag->c( 'inventory' )->get_inventory( $ag->char[ 'id' ] );

    if ( ! isset( $inventory_obj[ $inv_id ] ) ) {
        return FALSE;
    }

    $item = $inventory_obj[ $inv_id ];
    $item_meta = json_decode( $item[ 'meta_value' ], TRUE );

    if ( ! isset( $item_meta[ 'sell' ] ) ) {
        return FALSE;
    }

    $ag->char[ 'info' ][ 'gold' ] += $item_meta[ 'sell' ][ 0 ];
    $ag->c( 'inventory' )->remove_item( $ag->char[ 'id' ], $inv_id );

    update_character_meta( $ag->char[ 'id' ], ts_meta_type_character,
        TS_TIP, $item_meta[ 'name' ] . ' sold for ' .
        $item_meta[ 'sell' ][ 0 ] . ' gold.' );

    $ag->set_redirect_header( GAME_URL . '?state=inventory' );
}

// todo: this global has to go..
$GLOBALS[ 'setting_map' ][ 'store_sell' ] = 'ts_store_sell';
