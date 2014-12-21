<?php

class TSCombat {
    private $ag;

    public function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'do_page_content', 'combat',
            array( $this, 'combat_content' ) );
    }

    public function get_npc( $npc_id ) {
        $npc = $this->ag->c( 'npc' )->get_npc( $npc_id );

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

    public function combat_content() {
        if ( strcmp( 'combat', $this->ag->get_state() ) ) {
            return FALSE;
        }

        $zone_id = $this->ag->get_arg( 'zone_id' );

        if ( ! $zone_id ) {
            return FALSE;
        }

        if ( ! is_numeric( $zone_id ) ) {
            $zone_id = $this->ag->c( 'ts_zone' )->str_to_int( $zone_id );
        }

        $zone = $this->ag->c( 'ts_zone' )->get_zone( $zone_id );

        if ( FALSE == $zone ) {
            return FALSE;
        }
?>
<div class="row text-right">
  <h1 class="page_section">Combat</h1>
</div>
<?
        $combat_id = $this->ag->get_arg( 'combat_id' );
        if ( ! $combat_id ) {
            $combat_id = 1;
        }

        if ( ! isset( $zone[ 'combat_id' ][ $combat_id ] ) ) {
            return FALSE;
        }

        $c_obj = $zone[ 'combat_id' ][ $combat_id ];
        $npc_id = $c_obj[ array_rand( $c_obj ) ];

        $npc = $this->get_npc( $npc_id );

        if ( ! $npc ) {
            return FALSE;
        }

        echo( '<div class="row text-center">' );
        echo( '  <div class="col-md-8 col-md-offset-2">' );
        echo( '    <h1>' . $npc[ 'name' ] . '</h1>' );
        if ( isset( $npc[ 'text' ] ) ) {
            echo( '      <p class="lead">' . $npc[ 'text' ] . '</p>' );
        }
        echo( '  </div>' );
        echo( '</div>' );

        $combat_obj = $this->get_combat( $npc );

        if ( ! $combat_obj[ 'round' ][ 0 ][ 'initiative' ] ) {
            $this->echo_health( $combat_obj[ 'round' ][ 0 ] );
        }

        foreach ( $combat_obj[ 'round' ] as $combat ) {
            if ( $combat[ 'initiative' ] ) {
                $this->echo_health( $combat );
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

            $this->ag->char[ 'info' ][ 'stamina' ] = max(
                0, $this->ag->char[ 'info' ][ 'stamina' ] - 1.0 );

            $this->ag->c( 'track_npc' )->set_tracking( $this->ag->char[ 'id' ],
                $npc[ 'meta_key' ],
                $this->ag->char_meta( TRACK_NPC, $npc[ 'meta_key' ], 0 ) + 1 );

            echo( '<p>You have defeated your ' .
                  $this->ag->c( 'common' )->number_with_suffix(
                      $this->ag->char_meta(
                          TRACK_NPC, $npc[ 'meta_key' ], 0 ) + 1 ) .
                      ' ' . $npc[ 'name' ] . '.</p>' );

            $this->check_combat_achievements( $npc );

            if ( isset( $npc[ 'gold' ] ) ) {
                $gold = $npc[ 'gold' ];
                if ( isset( $npc[ 'gold_var' ] ) ) {
                    $v = $gold * floatval( $npc[ 'gold_var' ] );
                    $gold_min = max( 0, round( $gold - $v ) );
                    $gold_max = round( $gold + $v );
                    $gold = mt_rand( $gold_min, $gold_max );
                }

                echo( '<p>You receive ' . $gold . ' gold!</p>' );

                $this->ag->char[ 'info' ][ 'gold' ] += $gold;
            }

            if ( isset( $npc[ 'drops' ] ) ) {
                foreach ( $npc[ 'drops' ] as $k => $drop_rate ) {
                    $roll = mt_rand( 0, 100 );
                    if ( $roll < $drop_rate ) {
                        $item = $this->ag->c( 'item' )->get_item( $k );
                        $item_obj = json_decode( $item[ 'meta_value' ], TRUE );

                        echo( '<p>Your foe drops some loot: ' .
                              $this->ag->ts->item_popup( $item_obj ) .
                              '</p>' );

                        $this->ag->c( 'inventory' )->award_item(
                            $this->ag->char[ 'id' ], '{"id":' . $k . '}' );
                    }
                }
            }

        } else {
            echo( '<div class="row text-center">' .
                  '<h2>You are defeated!</h2>' );
            echo( '<h2>' . $npc[ 'name' ] . ' wins!</h2>' );
            echo( '<p class="lead">You take a huge stamina hit as you lurch ' .
                  'back to a safe spot and heal.</p>' );

            $this->ag->char[ 'info' ][ 'stamina' ] = max(
                0, $this->ag->char[ 'info' ][ 'stamina' ] - 10.0 );
        }

        echo( '<p><a href="?state=combat&zone_id=' .
              $this->ag->get_arg( 'zone_id' ) .
              '">Adventure again</a></p>' );
        echo( '<p><a href="?state=zone&zone_id=' .
              $this->ag->get_arg( 'zone_id' ) .
              '">Back to ' . $zone[ 'name' ] . '</a></p>' );

        echo( '</div>' );
    }

    public function get_combat( $npc ) {
        $obj = array();

        if ( FALSE == $this->ag->char ) {
            return $obj;
        }

        if ( $this->ag->char[ 'info' ][ 'stamina' ] < 1 ) {
            // return too tired;
            return $obj;
        }

        $this->ag->char[ 'info' ][ 'health' ] = $this->ag->char[ 'info' ][ 'health_max' ];

        $initiative = $this->get_initiative( $this->ag->char, $npc );

        $obj[ 'round' ] = array();

        $done = FALSE;

        while ( ! $done ) {
            $combat = array(
                'name_a' => $this->ag->char[ 'character_name' ],
                'name_b' => $npc[ 'name' ],
                'health_a' => $this->ag->char[ 'info' ][ 'health' ],
                'health_b' => $npc[ 'health' ],
                'health_max_a' => $this->ag->char[ 'info' ][ 'health_max' ],
                'health_max_b' => $npc[ 'health_max' ],
                'initiative' => $initiative,
            );

            if ( $initiative ) {
                $attack = $this->get_attack( $this->ag->char );
                $combat[ 'attack' ] = $attack;
                $npc[ 'health' ] -= $attack[ 'dmg' ];
            } else {
                $attack = $this->get_attack( $npc );
                $combat[ 'attack' ] = $attack;
                $this->ag->char[ 'info' ][ 'health' ] -= $attack[ 'dmg' ];
            }

            $obj[ 'round' ][] = $combat;

            $initiative = ! $initiative;

            if ( $this->ag->char[ 'info' ][ 'health' ] <= 0 ) {
                $obj[ 'win' ] = FALSE;
                $done = TRUE;
            } else if ( $npc[ 'health' ] <= 0 ) {
                $obj[ 'win' ] = TRUE;
                $done = TRUE;
            }
        }

        $this->ag->char[ 'info' ][ 'health' ] = $this->ag->char[ 'info' ][ 'health_max' ];

        return $obj;
    }

    public function get_initiative( $char_a, $char_b ) {
        // todo come on
        if ( mt_rand( 0, 1 ) == 0 ) {
            return TRUE;
        }

        return FALSE;
    }

    public function get_attack( $char ) {
        $attack = array( 'text' => 'The weapon hits for 1 damage!',
            'dmg' => 1 );

        if ( isset( $char[ 'attacks' ] ) ) {
            $attack_key = array_rand( $char[ 'attacks' ] );
            $attack = $char[ 'attacks' ][ $attack_key ];

            if ( isset( $attack[ 'min' ] ) && isset( $attack[ 'max' ] ) ) {
                $attack[ 'dmg' ] = mt_rand(
                    intval( $attack[ 'min' ] ), intval( $attack[ 'max' ] ) );
            } else {
                $attack[ 'dmg' ] = 1;
            }

            $attack[ 'text' ] = str_replace( '|dmg|', $attack[ 'dmg' ],
                $attack[ 'text' ] );
        }

        return $attack;
    }

    public function echo_health( $combat ) {
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

    public function check_combat_achievements( $npc ) {
        if ( ( 1 == $npc[ 'id' ] ) &&
             ( $this->ag->char_meta( TRACK_NPC, $npc[ 'id' ], 0 ) > 10 ) ) {
            $this->ag->c( 'achievement' )->award_achievement( 1 );
        }
    }

}
