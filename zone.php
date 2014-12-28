<?php

class TSZone {
    private $ag;

    public function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'do_page_content', 'store',
            array( $this, 'store_content' ) );
        $ag->add_state( 'do_page_content', 'zone',
            array( $this, 'zone_content' ) );

        $ag->add_state( 'do_setting', 'store_buy',
            array( $this, 'store_buy' ) );
        $ag->add_state( 'do_setting', 'store_sell',
            array( $this, 'store_sell' ) );
    }

    public function get_zone( $zone_id ) {
        $zone = $this->ag->c( 'zone' )->get_zone( $zone_id );

        if ( FALSE == $zone ) {
            return FALSE;
        }

        $meta = json_decode( $zone[ 'meta_value' ], $assoc = TRUE );

        if ( ! $meta ) {
            return FALSE;
        }

        foreach ( $meta as $k => $v ) {
            $zone[ $k ] = $v;
        }
        unset( $zone[ 'meta_value' ] );

        return $zone;
    }

    public function zone_content() {
        $zone_id = 'erebus'; //todo: define as constant TS_STARTING_ZONE

        if ( FALSE != $this->ag->get_arg( 'zone_id' ) ) {
            $zone_id = $this->ag->get_arg( 'zone_id' );
        }

        if ( ! is_numeric( $zone_id ) ) {
            $zone_id = $this->str_to_int( $zone_id );
            //$this->ag->debug_print( 'zone_id changed to ' . $zone_id );
        }

        $zone = $this->get_zone( $zone_id );

        if ( FALSE == $zone ) {
            return FALSE;
        }

?>
<div class="row text-right">
  <h1 class="page_section">Zone</h1>
</div>
<div class="row">
  <div class="col-md-8">
    <h1><?php echo( $zone[ 'name' ] ); ?></h1>
    <p class="lead zone_description"><?php
        if ( isset( $zone[ 'description' ] ) ) {
            echo( $zone[ 'description' ] );
        } ?></p>
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

        if ( isset( $zone[ 'people' ] ) && count( $zone[ 'people' ] ) > 0 ) {
            echo( '<h3>People here</h3>' );
            echo( '<div class="list-group">' );
            foreach ( $zone[ 'people' ] as $k => $v ) {
?>
<a href="<?php echo( GAME_URL ); ?>?state=npc&id=<?php echo( $k ); ?>"
   class="list-group-item">
  <h4 class="list-group-item-heading"><?php echo( $v ); ?></h4>
  <p class="list-group-item-text">Herp de derp.</p>
</a>
<?php
            }
            echo( '</div>' );
        }

        if ( isset( $zone[ 'places' ] ) && count( $zone[ 'places' ] ) > 0 ) {
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
        return TRUE;
    }

    function str_to_int( $st ) {
        $x = substr( md5( $st ), 0, 8 );
        return base_convert( $x, 16, 10 );
    }

    function store_content() {
        $zone_id = $this->ag->get_arg( 'zone_id' );

        if ( ! $zone_id ) {
            return FALSE;
        }

        if ( ! is_numeric( $zone_id ) ) {
            $zone_id = $this->str_to_int( $zone_id );
        }

        $zone = $this->get_zone( $zone_id );

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
<?php

        $item_obj = $this->ag->c( 'item' )->get_item_list(
            $zone[ 'store_id' ] );

        echo( '<div class="row">' );
        foreach ( $item_obj as $item ) {
            $item_meta = json_decode( $item[ 'meta_value' ], TRUE );

            if ( ! isset( $item_meta[ 'buy' ] ) ) {
                continue;
            }

            echo( '<div class="col-sm-4">' .
                  '<div class="text-center">' .
                  '<a href="game-setting.php?state=store_buy&zone_id=' .
                  $this->ag->get_arg( 'zone_id' ) . '&buy=' .
                  $item[ 'meta_key' ] .
                  '&nonce=' . $this->ag->c( 'common' )->nonce_create(
                      'buy' . $item[ 'meta_key' ] ) .
                  '">Buy for ' . $item_meta[ 'buy' ][ 0 ] .
                  ' gold</a></div>' );
            //todo buy should be an array of item/quantities

            echo( $this->ag->ts->item_div( $item_meta ) . '</div>' );
        }
        echo( '</div>' );
    }

    function store_buy() {
        $zone_id = $this->ag->get_arg( 'zone_id' );

        if ( ! $zone_id ) {
            return FALSE;
        }

        if ( ! is_numeric( $zone_id ) ) {
            $zone_id = $this->str_to_int( $zone_id );
        }

        $zone = $this->get_zone( $zone_id );

        if ( FALSE == $zone ) {
            return FALSE;
        }

        if ( ! isset( $zone[ 'store_id' ] ) ) {
            return FALSE;
        }

        $item_id = $this->ag->get_arg( 'buy' );

        if ( ! $this->ag->c( 'common' )->nonce_verify(
                   $this->ag->get_arg( 'nonce' ),
                   $state = 'buy' . $item_id ) ) {
            return FALSE;
        }

        if ( ! in_array( $item_id, $zone[ 'store_id' ] ) ) {
            return FALSE;
        }

        $item = $this->ag->c( 'item' )->get_item( $item_id );

        if ( ! $item ) {
            return FALSE;
        }

        $item_meta = json_decode( $item[ 'meta_value' ], TRUE );

        if ( ! isset( $item_meta[ 'buy' ] ) ) {
            return FALSE;
        }

        if ( $this->ag->char[ 'info' ][ 'gold' ] < $item_meta[ 'buy' ][ 0 ] ) {
            // todo: set tip to say you don't have enough gold
            return FALSE;
        }

        $this->ag->char[ 'info' ][ 'gold' ] -= $item_meta[ 'buy' ][ 0 ];
        $this->ag->c( 'inventory' )->award_item(
            $this->ag->char[ 'id' ], '{"id":' . $item_id . '}' );

        $this->ag->c( 'user' )->update_character_meta(
            $this->ag->char[ 'id' ], ts_meta_type_character,
            TS_TIP, $item_meta[ 'name' ] . ' purchased for ' .
            $item_meta[ 'buy' ][ 0 ] . ' gold.' );

        $this->ag->set_redirect_header( GAME_URL .
            '?state=store&zone_id=' . $this->ag->get_arg( 'zone_id' ) );
    }

    function store_sell() {
        $inv_id = $this->ag->get_arg( 'sell' );

        if ( ! $this->ag->c( 'common' )->nonce_verify(
               $this->ag->get_arg( 'nonce' ), $state = 'sell' . $inv_id ) ) {
            return FALSE;
        }

        $inventory_obj = $this->ag->ts->get_inventory();

        if ( ! isset( $inventory_obj[ $inv_id ] ) ) {
            return FALSE;
        }

        $item = $inventory_obj[ $inv_id ];

        if ( ! isset( $item[ 'meta' ][ 'sell' ] ) ) {
            return FALSE;
        }

        $this->ag->char[ 'info' ][ 'gold' ] += $item[ 'meta' ][ 'sell' ][ 0 ];
        $this->ag->c( 'inventory' )->remove_item(
            $this->ag->char[ 'id' ], $inv_id );

        $this->ag->c( 'user' )->update_character_meta(
            $this->ag->char[ 'id' ], ts_meta_type_character,
            TS_TIP, $item[ 'meta' ][ 'name' ] . ' sold for ' .
            $item[ 'meta' ][ 'sell' ][ 0 ] . ' gold.' );

        $this->ag->set_redirect_header( GAME_URL . '?state=inventory' );
    }

}
