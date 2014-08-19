<?php

require( GAME_CUSTOM_PATH . 'select.php' );
require( GAME_CUSTOM_PATH . 'title.php' );


define( 'ts_meta_type_character',            1 );
define( 'ts_meta_type_inventory',            2 );
define( 'ts_meta_type_buff',                 3 );

define( 'TS_NAME', 1 );
define( 'TS_TITLED_NAME', 2 );
define( 'TS_STATS', 3 );
define( 'TS_EQUIPPED', 4 );
define( 'TS_ENCOUNTER', 5 );

/*define( 'TS_LEVEL', 3 );
define( 'TS_STR', 10 );
define( 'TS_DEX', 11 );
define( 'TS_INT', 12 );
define( 'TS_CHA', 13 );
define( 'TS_CON', 14 );
define( 'TS_BASE_HP', 15 );
define( 'TS_CURRENT_HP', 16 );
define( 'TS_MANA', 17 );
define( 'TS_MANA_MAX', 18 );
define( 'TS_FATIGUE', 19 );
define( 'TS_FATIGUE_REDUCTION', 20 );
define( 'TS_FATIGUE_RESTED', 21 );
define( 'TS_XP', 22 );
define( 'TS_GOLD', 23 );
define( 'TS_GOLD_BANK', 24 );*/

/*define( 'TS_WEAPON', 100 );
define( 'TS_ARMOUR_HEAD', 101 );
define( 'TS_ARMOUR_CHEST', 102 );
define( 'TS_ARMOUR_LEGS', 103 );
define( 'TS_ARMOUR_NECK', 104 );
define( 'TS_ARMOUR_TRINKET_1', 105 );
define( 'TS_ARMOUR_TRINKET_2', 106 );
define( 'TS_ARMOUR_TRINKET_3', 107 );
define( 'TS_ARMOUR_HANDS', 108 );
define( 'TS_ARMOUR_WRISTS', 109 );
define( 'TS_ARMOUR_BELT', 110 );
define( 'TS_ARMOUR_BOOTS', 111 );
define( 'TS_ARMOUR_RING_1', 112 );
define( 'TS_ARMOUR_RING_2', 113 );
define( 'TS_MOUNT', 114 );*/



/*define( 'CR_TUTORIAL_STATUS',                1 );
define( 'CR_CHARACTER_NAME',                 2 );
define( 'CR_CHARACTER_MONEY',                3 );
define( 'CR_CHARACTER_TIP',                  4 );
define( 'CR_CURRENT_ZONE',                   5 );

define( 'CR_CURRENT_CITYAREA',              10 );

define( 'CR_CHARACTER_HEALTH',              50 );
define( 'CR_CHARACTER_HEALTH_MAX',          51 );

define( 'CR_CHARACTER_STAMINA',             60 );
define( 'CR_CHARACTER_STAMINA_TIMESTAMP',   61 );
define( 'CR_CHARACTER_STAMINA_MAX',         62 );

define( 'CR_CHARACTER_STR',                100 );
define( 'CR_CHARACTER_DEX',                101 );
define( 'CR_CHARACTER_INT',                102 );
define( 'CR_CHARACTER_CON',                103 );
define( 'CR_CHARACTER_APP',                104 );
define( 'CR_CHARACTER_POW',                105 );
define( 'CR_CHARACTER_EDU',                106 );
define( 'CR_CHARACTER_XP',                 107 );

define( 'CR_CHARACTER_JOB_ID',             150 );
define( 'CR_CHARACTER_JOB_HIRED',          151 );
define( 'CR_CHARACTER_JOB_LASTPAID',       152 );

define( 'CR_CHARACTER_GYM_ID',             200 );

define( 'CR_CHARACTER_JAIL_TIME',          250 );*/

/*define( 'cr_game_meta_employers',            1 );
define( 'cr_game_meta_jobs',                 2 );
define( 'cr_game_meta_crimes',               3 );
define( 'cr_game_meta_degrees',              4 );
define( 'cr_game_meta_courses',              5 );
define( 'cr_game_meta_gyms',                 6 );
define( 'cr_game_meta_state',                7 );*/

/*define( 'CR_GAME_FOES_MALL',                 1 );*/


function ts_default_action() {
    global $user, $character;

    if ( FALSE == $user ) {
        game_set_action( 'title' );
    } else if ( FALSE == $character ) {
        game_set_action( 'select' );
    } else {
        game_set_action( 'map' );
    }
}

add_action( 'set_default_action', 'ts_default_action' );


function ts_login() {
    global $character;

/*    ensure_character_meta_keygroup(
        $character[ 'id' ], cr_meta_type_character, '',
        array(
            CR_CHARACTER_NAME, CR_CHARACTER_TIP, CR_CURRENT_ZONE
        ) );

    ensure_character_meta_keygroup(
        $character[ 'id' ], cr_meta_type_character, 0,
        array(
            CR_TUTORIAL_STATUS, CR_CHARACTER_MONEY,
            CR_CHARACTER_STAMINA_TIMESTAMP,
            CR_CHARACTER_XP,
            CR_CHARACTER_JOB_ID, CR_CHARACTER_JOB_HIRED,
            CR_CHARACTER_JOB_LASTPAID,
            CR_CHARACTER_JAIL_TIME
        ) );

    ensure_character_meta_keygroup(
        $character[ 'id' ], cr_meta_type_character, 1,
        array(
            CR_CHARACTER_GYM_ID, CR_CURRENT_CITYAREA
        ) );

    ensure_character_meta_keygroup(
        $character[ 'id' ], cr_meta_type_character, 10,
        array(
            CR_CHARACTER_STR, CR_CHARACTER_DEX, CR_CHARACTER_INT,
            CR_CHARACTER_CON, CR_CHARACTER_APP, CR_CHARACTER_POW,
            CR_CHARACTER_EDU
        ) );

    ensure_character_meta_keygroup(
        $character[ 'id' ], cr_meta_type_character, 100,
        array(
            CR_CHARACTER_HEALTH, CR_CHARACTER_HEALTH_MAX,
            CR_CHARACTER_STAMINA, CR_CHARACTER_STAMINA_MAX
        ) );

    cr_award_salary();*/
}

add_action( 'select_character', 'ts_login' );

function ts_header() {
    global $user, $character;

    if ( ! strcmp( 'title', game_get_action() ) ) {
        return;
    }

//    update_buffs();

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo( GAME_NAME ); ?> (<?php echo( game_get_action() );
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

    if ( FALSE != $character ) {
?>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="droptown-toggle"
                 data-toggle="dropdown">Character <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Profile</a></li>
                <li><a href="#">Inventory</a></li>
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
            <li><a href="?action=about">About</a></li>
            <li><a href="?action=contact">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
                  echo( $character[ 'character_name' ] );
                  ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?action=dashboard">Dashboard</a></li>
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
      <div class="row">

<div class="col-md-6">
  <a href="#"><?php echo( 'TS_TITLED_NAME' ); ?></a>,
  Level <?php echo( 'TS_LEVEL' ); ?>,
  Gold: <?php echo( 'TS_GOLD' ); ?>
  (<a href="#">x new messages</a>)<br>
  HEALTH | MANA
</div>
<div class="col-md-6 text-right">
  BUFFS<br>
  Twelve Sands
</div>

      </div>
      <div class="row">
<?php
}

function ts_footer() {
    global $character;

    if ( ! strcmp( 'title', game_get_action() ) ) {
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

add_action( 'game_header', 'ts_header' );
add_action( 'game_footer', 'ts_footer' );



function ts_tip_print() {
    global $character;
/*
    if ( FALSE == $character ) {
        return;
    }

    $tip = character_meta( ts_meta_type_character, TS_CHARACTER_TIP );

    if ( 0 < strlen( $tip ) ) {
        echo( '<p class="tip">' . $tip . '</p>' );
        update_character_meta( $character[ 'id' ], ts_meta_type_character,
            TS_CHARACTER_TIP, '' );
    }*/
}

add_action_priority( 'do_page_content', 'ts_tip_print' );

function ts_about() {
    if ( strcmp( 'about', game_get_action() ) ) {
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
    if ( strcmp( 'contact', game_get_action() ) ) {
       return;
    }

?>
<div class="row text-right">
  <h1 class="page_section">Contact</h1>
</div>
<?php

    echo '<h1>OH BOB SAGET</h1>';
}

add_action( 'do_page_content', 'ts_about' );
add_action( 'do_page_content', 'ts_contact' );


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

add_action( 'validate_user', 'ts_validate_user' );
