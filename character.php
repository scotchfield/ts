<?php

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

add_state( 'do_page_content', 'ts_profile_content' );

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

    $char = ts_get_unpacked_character( $char );

    ts_print_character( $char );
}

add_state( 'do_page_content', 'ts_char_content' );


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
        'trinket_1' => 'Trinket',
        'trinket_2' => 'Trinket',
        'trinket_3' => 'Trinket',
        'hands' => 'Hands',
        'wrists' => 'Wrists',
        'belt' => 'Belt',
        'boots' => 'Boots',
        'ring_1' => 'Ring',
        'ring_2' => 'Ring',
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

add_state( 'do_page_content', 'ts_achievements_content' );

function ts_inventory_content() {
    global $ag;

    if ( strcmp( 'inventory', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Inventory</h1>
</div>
<div class="row">
  <h2>Currently Holding:</h2>
  <ul>
  <?php

    foreach( $ag->char[ 'meta' ][ ts_meta_type_inventory ] as $item ) {
        echo( '<li>' . ts_item_popup( $item ) . '</li>' );
//        echo( '<li>' . $item[ 'name' ] . '<br><i>(' . $item[ 'text' ] . ')</i></li>' );
    }

?>
</div>
<?php
}

add_state( 'do_page_content', 'ts_inventory_content' );
