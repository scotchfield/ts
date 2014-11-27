<?php

require( GAME_CUSTOM_PATH . 'select.php' );
require( GAME_CUSTOM_PATH . 'title.php' );

require( GAME_CUSTOM_PATH . 'character.php' );
require( GAME_CUSTOM_PATH . 'map.php' );
require( GAME_CUSTOM_PATH . 'zone.php' );


define( 'ts_meta_type_character',            1 );
define( 'ts_meta_type_inventory',            2 );
define( 'ts_meta_type_buff',                 3 );

define( 'TS_INFO', 1 );
define( 'TS_EQUIPPED', 2 );
define( 'TS_ENCOUNTER', 3 );

define( 'TS_TIP', 10 );


//define( 'CR_TUTORIAL_STATUS',                1 );


function ts_post_load() {
    global $ag;

    $ag->set_component( 'zone', new ArcadiaZone() );
}

add_state( 'post_load', 'ts_post_load' );

function ts_default_state() {
    global $ag;

    if ( FALSE == $ag->user ) {
        $ag->set_state( 'title' );
    } else if ( FALSE == $ag->char ) {
        $ag->set_state( 'select' );
    } else {
        $ag->set_state( 'zone' );
    }
}

add_state( 'set_default_state', 'ts_default_state' );


function ts_unpack_character() {
    global $ag;

    if ( FALSE == $ag->char ) {
        return;
    }

    $info_obj = json_decode(
        character_meta( ts_meta_type_character, TS_INFO ), TRUE );

// @todo: regen health/mana/stamina

    $default_info_obj = array(
        'level' => 1,
        'str' => 10,
        'dex' => 10,
        'int' => 10,
        'con' => 10,
	'cha' => 10,
	'pow' => 10,
        'health' => 10,
        'health_max' => 10,
        'mana' => 10,
        'mana_max' => 10,
	'stamina' => 100,
	'stamina_max' => 100,
	'sanity' => 100,
	'sanity_max' => 100,
        'fatigue' => 0,
        'fatigue_reduction' => 0,
        'fatigue_rested' => 0,
        'xp' => 0,
        'gold' => 0,
        'gold_bank' => 0,
        'zone' => 1,
    );

    foreach ( $default_info_obj as $k => $v ) {
        if ( ! isset( $ag->char[ $k ] ) ) {
            $ag->char[ $k ] = $v;
        }
    }

    $ag->char[ 'equipped' ] = json_decode(
        character_meta( ts_meta_type_character, TS_EQUIPPED ), TRUE );

    $default_info = array(
      'weapon' => 0,
      'head' => 0,
      'chest' => 0,
      'legs' => 0,
      'neck' => 0,
      'trinket_1' => 0,
      'trinket_2' => 0,
      'trinket_3' => 0,
      'hands' => 0,
      'wrists' => 0,
      'belt' => 0,
      'boots' => 0,
      'ring_1' => 0,
      'ring_2' => 0,
      'mount' => 0,
    );


    foreach ( $default_info as $k => $v ) {
        if ( ! isset( $ag->char[ 'equipped' ][ $k ] ) ) {
            $ag->char[ 'equipped' ][ $k ] = $v;
        }
    }

    $ag->char[ 'encounter' ] = json_decode(
        character_meta( ts_meta_type_character, TS_ENCOUNTER ), TRUE );
}

function ts_login() {
    global $ag;

    ensure_character_meta_keygroup(
        $ag->char[ 'id' ], ts_meta_type_character, '',
        array(
            TS_INFO, TS_EQUIPPED, TS_ENCOUNTER,
        ) );
}

add_state( 'select_character', 'ts_login' );

function ts_header() {
    global $ag;

    if ( ! strcmp( 'title', $ag->get_state() ) ) {
        return;
    }

    ts_unpack_character();
//    update_buffs();

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo( GAME_NAME ); ?> (<?php echo( $ag->get_state() );
        ?>)</title>
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>elysian.css">
    <link href="http://fonts.googleapis.com/css?family=Raleway:400,500"
          rel="stylesheet" type="text/css">
    <link href='http://fonts.googleapis.com/css?family=Oswald:700'
          rel='stylesheet' type='text/css'>
  </head>
  <body>
    <div id="popup" class="invis"></div>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle"
                  data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo( GAME_URL ); ?>"><?php
              echo( GAME_NAME ); ?></a>
        </div>
<?php

    if ( FALSE != $ag->char ) {
?>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="droptown-toggle"
                 data-toggle="dropdown">Character <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?state=profile">Profile</a></li>
                <li><a href="#">Inventory</a></li>
                <li><a href="?state=achievements">Achievements</a></li>
                <li class="divider">
                <li><a href="#">Allies</a></li>
                <li><a href="#">Guild</a></li>
                <li><a href="#">Mailbox</a></li>
                <li><a href="#">Quest Log</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="droptown-toggle"
                 data-toggle="dropdown">Navigate <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Capital City</a></li>
                <li><a href="#">Auction House</a></li>
                <li><a href="#">Casino</a></li>
                <li><a href="#">Infirmary</a></li>
                <li><a href="#">Hall of Records</a></li>
                <li><a href="#">Bank of Nobility</a></li>
                <li><a href="#">Temporal Laboratory</a></li>
                <li><a href="#">Starfall Bay Auctions</a></li>
                <li><a href="#">Regional Map</a></li>
                <li><a href="#">Zones by Level</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="droptown-toggle"
                 data-toggle="dropdown">Actions <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Cast a Spell</a></li>
                <li><a href="#">Cooking</a></li>
                <li><a href="#">Crafting</a></li>
                <li><a href="#">Enchanting</a></li>
                <li><a href="#">Sell Something</a></li>
                <li><a href="#">Online Players</a></li>
                <li><a href="#">Character Search</a></li>
              </ul>
            </li>
            <li><a href="?state=about">About</a></li>
            <li><a href="?state=contact">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
                  echo( $ag->char[ 'character_name' ] );
                  ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?state=dashboard">Dashboard</a></li>
                <li class="divider"></li>
                <li><a href="game-setting.php?setting=change_character">
                    Change Character</a></li>
                <li><a href="game-logout.php">Log out</a></li>
              </ul>
            </li>
          </ul>
        </div>
<?php
    }
?>
      </div>
    </div>

    <div class="container">
<?php
    if ( FALSE != $ag->char ) {
//debug_print( $ag->char );
?>
      <div class="row">

        <div class="col-md-6">
          <a href="#"><?php echo( $ag->char[ 'character_name' ] ); ?></a>,
          Level <?php echo( $ag->char[ 'level' ] ); ?>,
          Gold: <?php echo( $ag->char[ 'gold' ] ); ?>
          (<a href="#">x new messages</a>)<br>
          Health: <?php echo( $ag->char[ 'health' ] ); ?> /
          <?php echo( $ag->char[ 'health_max' ] ); ?>,
          Mana: <?php echo( $ag->char[ 'mana' ] ); ?> /
          <?php echo( $ag->char[ 'mana_max' ] ); ?>
        </div>
        <div class="col-md-6 text-right">
          BUFFS<br>
          Twelve Sands
        </div>

      </div>
<?php
    }
?>
      <div class="row">
<?php

//debug_print( $ag->char );
//debug_print( $ag->get_state() );
}

function ts_footer() {
    global $ag;

    if ( ! strcmp( 'title', $ag->get_state() ) ) {
        return;
    }

?>
    </div>
  </div>
  <script src="<?php echo( GAME_CUSTOM_STYLE_URL );
      ?>popup.js"></script>
  <script src="<?php echo( GAME_CUSTOM_STYLE_URL );
      ?>jquery.min.js"></script>
  <script src="<?php echo( GAME_CUSTOM_STYLE_URL );
      ?>bootstrap.min.js"></script>
  </body>
</html>
<?php
}

add_state( 'game_header', 'ts_header' );
add_state( 'game_footer', 'ts_footer' );



function ts_tip_print() {
    global $ag;

    if ( FALSE == $ag->char ) {
        return;
    }

    $tip = character_meta( ts_meta_type_character, TS_TIP );

    if ( 0 < strlen( $tip ) ) {
        echo( '<p class="tip">' . $tip . '</p>' );
        update_character_meta( $ag->char[ 'id' ], ts_meta_type_character,
            TS_TIP, '' );
    }
}

add_state_priority( 'do_page_content', 'ts_tip_print' );

function ts_about() {
    global $ag;

    if ( strcmp( 'about', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">About</h1>
</div>
<?php

    echo '<h1>BOB SAGET</h1>';
}

function ts_contact() {
    global $ag;

    if ( strcmp( 'contact', $ag->get_state() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Contact</h1>
</div>
<?php

    echo '<h1>OH BOB SAGET</h1>';
}

add_state( 'do_page_content', 'ts_about' );
add_state( 'do_page_content', 'ts_contact' );


function ts_item_popup_str( $item ) {
    return '<a href="#" onmouseover="popup(\'' .
           '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
           '</span>' .
           '<hr><span>' . $item[ 'description' ] . '</span>' .
           '\')" onmouseout="popout()" class="item">' . $item[ 'name' ] .
           '</a>';
}

function ts_item_string( $item ) {
    return '<a href="#" onmouseover="popup(\'' .
           '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
           '</span><hr><span>' . $item[ 'description' ] . '</span>' .
           '\')" onmouseout="popout()" class="item">' . $item[ 'name' ] .
           '</a>';
}

function ts_validate_user( $args ) {
    if ( ! isset( $args[ 'user_id' ] ) ) {
        return;
    }

    set_user_max_characters( $args[ 'user_id' ], 1 );
}

add_state( 'validate_user', 'ts_validate_user' );
