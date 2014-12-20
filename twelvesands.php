<?php

define( 'ts_meta_type_character',            1 );
define( 'ts_meta_type_buff',                 3 );

define( 'TS_INFO', 1 );
define( 'TS_EQUIPPED', 2 );
define( 'TS_ENCOUNTER', 3 );

define( 'TS_TIP', 10 );

define( 'TRACK_NPC', 2000 );

//define( 'CR_TUTORIAL_STATUS',                1 );

class TwelveSands {

    public $ag;

    public function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'do_page_content', 'about', array( $this, 'about' ) );
        $ag->add_state( 'do_page_content', 'contact',
            array( $this, 'contact' ) );
        $ag->add_state_priority( 'do_page_content', FALSE,
            array( $this, 'tip_print' ) );

        $ag->add_state( 'arcadia_end', FALSE,
            array( $this, 'pack_character' ) );
        $ag->add_state( 'award_achievement', FALSE,
            array( $this, 'achievement_print' ) );
        $ag->add_state_priority( 'character_load', FALSE,
            array( $this, 'unpack_character' ) );
        $ag->add_state( 'character_load', FALSE,
            array( $this, 'regen_stamina' ) );
        $ag->add_state( 'game_header', FALSE, array( $this, 'header' ) );
        $ag->add_state( 'game_footer', FALSE, array( $this, 'footer' ) );
        $ag->add_state( 'select_character', FALSE, array( $this, 'login' ) );
        $ag->add_state( 'set_default_state', FALSE,
            array( $this, 'default_state' ) );
        $ag->add_state( 'validate_user', FALSE,
            array( $this, 'validate_user' ) );

        $this->craft = new TSCraft( $ag );
        $this->zone = new TSZone( $ag );

        $this->ag->set_component( 'achievement', new ArcadiaAchievement() );
        $this->ag->set_component( 'inventory', new ArcadiaInventory() );
        $this->ag->set_component( 'item', new ArcadiaItem() );
        $this->ag->set_component( 'npc', new ArcadiaNpc() );
        $this->ag->set_component( 'track_npc',
            new ArcadiaTracking( $key_type = TRACK_NPC ) );
        $this->ag->set_component( 'zone', new ArcadiaZone() );

        $this->ag->set_component( 'dashboard', new TSDashboard( $this->ag ) );
    }

    public function default_state() {
        if ( FALSE == $this->ag->user ) {
            $this->ag->set_state( 'title' );
        } else if ( FALSE == $this->ag->char ) {
            $this->ag->set_state( 'select' );
        } else {
            $this->ag->set_state( 'zone' );
        }
    }

    public function login() {
        if ( ! $this->ag->char ) {
            return FALSE;
        }

        if ( ! $this->ag->c( 'user' ) ) {
            return FALSE;
        }

        $this->ag->c( 'user' )->ensure_character_meta_keygroup(
            $this->ag->char[ 'id' ], ts_meta_type_character, '',
            array(
                TS_INFO, TS_EQUIPPED, TS_ENCOUNTER,
                TS_TIP,
                ) );

        return TRUE;
    }

    public function tip_print() {
        if ( FALSE == $this->ag->char ) {
            return FALSE;
        }

        $tip = $this->ag->c( 'user' )->character_meta(
            ts_meta_type_character, TS_TIP );

        if ( 0 < strlen( $tip ) ) {
            echo( '<p class="tip">' . $tip . '</p>' );
            $this->ag->c( 'user' )->update_character_meta(
                $this->ag->char[ 'id' ],
                ts_meta_type_character, TS_TIP, '' );
        }

        return TRUE;
    }

    public function unpack_character() {
        if ( FALSE == $this->ag->char ) {
            return FALSE;
        }

        $this->ag->char[ 'info' ] = json_decode(
            $this->ag->c( 'user' )->character_meta(
                ts_meta_type_character, TS_INFO ), TRUE );

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
            'stamina_timestamp' => 0,
            'sanity' => 100,
            'sanity_max' => 100,
            'fatigue' => 0,
            'fatigue_reduction' => 0,
            'fatigue_rested' => 0,
            'xp' => 0,
            'gold' => 0,
            'gold_bank' => 0,
            'zone' => 1,
            'burden' => 0,
            'burden_max' => 100,
        );

        foreach ( $default_info_obj as $k => $v ) {
            if ( ! isset( $this->ag->char[ 'info' ][ $k ] ) ) {
                $this->ag->char[ 'info' ][ $k ] = $v;
            }
        }

        $this->ag->char[ 'equipped' ] = json_decode(
            $this->ag->c( 'user' )->character_meta(
                ts_meta_type_character, TS_EQUIPPED ), TRUE );

        $default_info = array(
          'weapon' => 0,
          'head' => 0,
          'chest' => 0,
          'legs' => 0,
          'neck' => 0,
          'trinket' => 0,
          'hands' => 0,
          'wrists' => 0,
          'belt' => 0,
          'boots' => 0,
          'ring' => 0,
          'mount' => 0,
        );

        $default_cache = array();

        foreach ( $default_info as $k => $v ) {
            if ( ! isset( $this->ag->char[ 'equipped' ][ $k ] ) ) {
                if ( ! isset( $default_cache[ $v ] ) ) {
                    $default_cache[ $v ] = $this->ag->c( 'item' )->get_item(
                        $v );
                }

                $this->ag->char[ 'equipped' ][ $k ] = json_decode(
                    $default_cache[ $v ][ 'meta_value' ], TRUE );
            }
        }

        $this->ag->char[ 'encounter' ] = json_decode(
            $this->ag->c( 'user' )->character_meta(
                ts_meta_type_character, TS_ENCOUNTER ), TRUE );

        return TRUE;
    }

    public function pack_character() {
        if ( FALSE == $this->ag->char ) {
            return FALSE;
        }

        // todo: check if we need to update, don't just update every time

        if ( isset( $this->ag->char[ 'info' ] ) ) {
            $this->ag->c( 'user' )->update_character_meta(
                $this->ag->char[ 'id' ],
                ts_meta_type_character, TS_INFO,
                json_encode( $this->ag->char[ 'info' ] ) );
        }

        if ( isset( $this->ag->char[ 'equipped' ] ) ) {
            $this->ag->c( 'user' )->update_character_meta(
                $this->ag->char[ 'id' ],
                ts_meta_type_character, TS_EQUIPPED,
                json_encode( $this->ag->char[ 'equipped' ] ) );
        }

        if ( isset( $this->ag->char[ 'encounter' ] ) ) {
            $this->ag->c( 'user' )->update_character_meta(
                $this->ag->char[ 'id' ],
                ts_meta_type_character, TS_ENCOUNTER,
                json_encode( $this->ag->char[ 'encounter' ] ) );
        }

        return TRUE;
    }

    public function regen_stamina() {
        if ( FALSE == $this->ag->char ) {
            return FALSE;
        }

        if ( $this->ag->char[ 'info' ][ 'stamina' ] < 100 ) {
            $stamina_boost = 1.0;

            $stamina_seconds = time() -
                $this->ag->char[ 'info' ][ 'stamina_timestamp' ];
            $stamina_gain = $stamina_boost * ( $stamina_seconds / 120.0 );

            $new_stamina = min(
                100, $this->ag->char[ 'info' ][ 'stamina' ] + $stamina_gain );

            $this->ag->char[ 'info' ][ 'stamina' ] = $new_stamina;
            $this->ag->char[ 'info' ][ 'stamina_timestamp' ] = time();
        }

        return TRUE;
    }

    public function header() {
        if ( ! strcmp( 'title', $this->ag->get_state() ) ) {
            return FALSE;
        }

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo( GAME_NAME ); ?> (<?php echo( $this->ag->get_state() );
        ?>)</title>
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo( GAME_CUSTOM_STYLE_URL );
        ?>ts.css">
<!--    <link href="http://fonts.googleapis.com/css?family=Raleway:400,500"
          rel="stylesheet" type="text/css">-->
      <link href='http://fonts.googleapis.com/css?family=Lusitana:400,700'
        rel='stylesheet' type='text/css'>
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
        if ( FALSE != $this->ag->char ) {
?>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="droptown-toggle"
                 data-toggle="dropdown">Character <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?state=profile">Profile</a></li>
                <li><a href="?state=inventory">Inventory</a></li>
                <li><a href="?state=achievements">Achievements</a></li>
                <li class="divider">
                <li><a href="?state=allies">Allies</a></li>
                <li><a href="?state=guild">Guild</a></li>
                <li><a href="?state=mail">Mailbox</a></li>
                <li><a href="?state=quests">Quest Log</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="droptown-toggle"
                 data-toggle="dropdown">Navigate <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?state=zone&zone_id=erebus">Erebus City</a></li>
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
                <li><a href="?state=craft&type=1">Cooking</a></li>
                <li><a href="?state=craft&type=2">Crafting</a></li>
                <li><a href="#">Enchanting</a></li>
                <li><a href="?state=sell">Sell Something</a></li>
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
                  echo( $this->ag->char[ 'character_name' ] );
                  ?> <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="?state=dashboard">Dashboard</a></li>
                <li class="divider"></li>
                <li><a href="game-setting.php?state=change_character">
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
        return TRUE;
    }

    public function footer() {
        if ( ! strcmp( 'title', $this->ag->get_state() ) ) {
            return FALSE;
        }
?>
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
        return TRUE;
    }

    public function about() {
?>
<div class="row text-right">
  <h1 class="page_section">About</h1>
</div>
<div class="row text-left">
  <h2>What is Twelve Sands?</h2>
  <p><a href="https://github.com/scotchfield/ts">Twelve Sands</a> is an
    open-source web-based role playing game.</p>
  <h2>What is Arcadia?</h2>
  <p><a href="https://github.com/scotchfield/arcadia">Arcadia</a> is an
    open-source web-based role playing engine currently under development.
    Check out the project, and feel free to follow along as it grows!</p>
</div>
<?php
    }

    public function contact() {
?>
<div class="row text-right">
  <h1 class="page_section">Contact</h1>
</div>
<?php

        echo '<h1>Contact</h1>';
    }

    public function validate_user( $args ) {
        if ( ! isset( $args[ 'user_id' ] ) ) {
            return FALSE;
        }

        set_user_max_characters( $args[ 'user_id' ], 1 );

        return TRUE;
    }

    public function achievement_print( $args ) {
        if ( ! isset( $args[ 'achievement_id' ] ) ) {
            return FALSE;
        }

        $achievement = $this->ag->c( 'achievement' )->get_achievement(
            $args[ 'achievement_id' ] );
        $meta = json_decode( $achievement[ 'meta_value' ], TRUE );
?>
<div class="row text-center alert">
  <h2>You have completed a new achievement!</h2>
  <h3><?php echo( $meta[ 'name' ] ); ?></h3>
  <h4><?php echo( $meta[ 'text' ] ); ?></h4>
</div>
<?php
        return TRUE;
    }

    public function item_string( $item ) {
        return '<a href="#" onmouseover="popup(\'' .
               '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
               '</span><hr><span>' . $item[ 'description' ] . '</span>' .
               '\')" onmouseout="popout()" class="item">' . $item[ 'name' ] .
               '</a>';
    }

    public function item_popup( $item ) {
        if ( ! isset( $item[ 'rarity' ] ) ) {
            $item[ 'rarity' ] = 1;
        }
        $rarity_obj = array(
            5 => 'legendary', 4 => 'epic', 3 => 'rare',
            2 => 'uncommon', 1 => 'common',
        );

        $st = '<a class="' . $rarity_obj[ $item[ 'rarity' ] ] .
              '" href="#" onmouseover="popup(\'' .
              '<span class=&quot;item_name&quot;>' . $item[ 'name' ] .
              '</span><hr>';

        if ( 5 == $item[ 'rarity' ] ) {
            $st = $st . '<span class=&quot;legendary&quot;>' .
                  'Legendary Quality</span><br><span>';
        } else if ( 4 == $item[ 'rarity' ] ) {
            $st = $st . '<span class=&quot;epic&quot;>' .
                  'Epic Quality</span><br><span>';
        } else if ( 3 == $item[ 'rarity' ] ) {
            $st = $st . '<span class=&quot;rare&quot;>' .
                  'Rare Quality</span><br><span>';
        } else if ( 2 == $item[ 'rarity' ] ) {
            $st = $st . '<span class=&quot;uncommon&quot;>' .
                  'Uncommon Quality</span><br><span>';
        } else {
            $st = $st . '<span class=&quot;common&quot;>' .
                  'Common Quality</span><br><span>';
        }

        $st = $st . '</span><hr>' . 'HERP DE DERP' .
              '\')" onmouseout="popout()" class="item">' .
              $item[ 'name' ] . '</a>';
        return $st;
    }

    public function item_div( $item ) {
        $st = '<div class="item"><div class="img">' .
              '<img src="/game/ts/style/grunge.png" width="64" height="64">' .
              '</div><div class="text">' .
              '<h4 style="margin: 0">' . $item[ 'name' ] . '</h4></div></div>';

        return $st;
    }

    public function get_inventory() {
        $inventory_obj = $this->ag->c( 'inventory' )->get_inventory(
            $this->ag->char[ 'id' ] );

        return $this->expand_id_obj( $inventory_obj );
    }

    public function expand_id_obj( $item_obj ) {
        $item_id = array();

        // todo: rewrite as first step collects inventory_obj keys and second
        //  setup replaces them, instead of cycling through the whole thing a
        //  second time
        foreach ( $item_obj as $k => $v ) {
            $meta = json_decode( $v[ 'meta_value' ], TRUE );
            if ( isset( $meta[ 'id' ] ) ) {
                $item_id[] = $meta[ 'id' ];
            }
            $item_obj[ $k ][ 'meta' ] = $meta;
        }

        if ( count( $item_id ) > 0 ) {
            $template_obj = $this->ag->c( 'item' )->get_item_list( $item_id );

            foreach ( $item_obj as $inv_k => $inv_v ) {
                if ( isset( $inv_v[ 'meta' ][ 'id' ] ) ) {
                    $i = $inv_v[ 'meta' ][ 'id' ];
                    $item_meta = json_decode(
                        $template_obj[ $i ][ 'meta_value' ], TRUE );
                    foreach ( $item_meta as $k => $v ) {
                        $item_obj[ $inv_k ][ 'meta' ][ $k ] = $v;
                    }
                }
            }
        }

        return $item_obj;
    }

}
