<?php

function ts_profile_content() {
    global $character, $ag;

    if ( strcmp( 'profile', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Profile</h1>
</div>
<?php
    debug_print( $character );
    ts_print_character( $character );
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
      <dd><?php echo( $character[ 'health' ] ); ?></dd>
      <dt>Stamina</dt>
      <dd><?php echo( round( $character[ 'stamina' ], $precision = 2 ) ); ?> /
          <?php echo( $character[ 'stamina_max' ] ); ?></dd>
      <dt>Gold</dt>
      <dd><?php echo( $character[ 'gold' ] ); ?></dd>
      <dt>Experience Points</dt>
      <dd><?php echo( $character[ 'xp' ] ); ?></dd>
    </dl>

    <h2>Gear</h2>

    <dl class="dl-horizontal">
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
    global $character, $ag;

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
    if ( ( ! isset( $character[ 'meta' ][ game_meta_type_achievement ] ) ) ||
         ( 0 == count(
                    $character[ 'meta' ][ game_meta_type_achievement ] ) ) ) {
        echo( '<h4>None yet!</h4>' );
    } else {
        echo( '<dl class="dl-horizontal">' );
        $achieve_obj = get_achievements( $character[ 'id' ] );

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
    $achieve_obj = get_all_achievements();

    foreach ( $achieve_obj as $k => $achieve ) {
        if ( isset( $character[ 'meta' ][
                        game_meta_type_achievement ][ $k ] ) ) {
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