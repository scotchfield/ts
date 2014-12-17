<?php

class TSCraft extends ArcadiaComponent {
    const type_cooking = 1;
    const type_crafting = 2;

    private $ag;

    public function __construct( $ag ) {
        $this->flag_game_meta = 500;
        $this->flag_character_meta = 500;

        $this->ag = $ag;

        $ag->add_state( 'do_page_content', 'craft',
            array( $this, 'craft_content' ) );

        $ag->add_state( 'do_setting', 'craft_create',
            array( $this, 'craft_create' ) );
    }

    public function get_recipe( $id ) {
        return $this->ag->c( 'db' )->db_fetch(
            'SELECT * FROM game_meta WHERE key_type=? AND meta_key=?',
            array( $this->flag_game_meta, $id ) );
    }

    public function get_recipes( $type = FALSE ) {
        $obj = $this->ag->c( 'db' )->db_fetch_all(
            'SELECT * FROM game_meta WHERE key_type=? ORDER BY meta_key',
            array( $this->flag_game_meta ),
            'meta_key' );

        foreach ( $obj as $k => $v ) {
            $obj[ $k ][ 'meta' ] = json_decode( $v[ 'meta_value' ], TRUE );
            if ( $type ) {
                if ( $type != $obj[ $k ][ 'meta' ][ 'type' ] ) {
                    unset( $obj[ $k ] );
                }
            }
        }

        return $obj;
    }

    public function get_items_string( $item_obj ) {
        $ret_obj = array();
        foreach ( $item_obj as $item ) {
            $ret_obj[] = $item[ 2 ] . 'x ' . $item[ 1 ];
        }
        return implode( ', ', $ret_obj );
    }

    public function craft_content() {
        $craft_types = array(
            SELF::type_crafting => 'Crafting',
            SELF::type_cooking => 'Cooking',
        );

        if ( ! isset( $craft_types[ $this->ag->get_arg( 'type' ) ] ) ) {
            return;
        }

        $craft_type = $this->ag->get_arg( 'type' );
?>
<div class="row text-right">
  <h1 class="page_section"><?php echo( $craft_types[ $craft_type ] ); ?></h1>
</div>
<?php

        $r_obj = $this->get_recipes( $type = $craft_type);

        echo( '<div class="row"><ul>' );
        foreach ( $r_obj as $r ) {
            echo( '<li><b>' .
                  $this->get_items_string( $r[ 'meta' ][ 'post' ] ) .
                  ' (' . $this->get_items_string( $r[ 'meta' ][ 'pre' ] ) .
                  ') (<a href="game-setting.php?state=craft_create&type=' .
                  $craft_type . '&id=' . $r[ 'meta_key' ] .
                  '">craft</a>)</li>' );
        }
        echo( '</ul></div>' );
    }

    public function craft_create() {
        $this->ag->set_redirect_header( GAME_URL . '?state=craft&type=2' );

        $recipe = $this->get_recipe( $this->ag->get_arg( 'id' ) );

        if ( ! $recipe ) {
            return FALSE;
        }

        $inventory_obj = $this->ag->ts->get_inventory();
        $inv_count = array();
        foreach ( $inventory_obj as $x ) {
            if ( ! isset( $inv_count[ 'id' ] ) ) {
                $inv_count[ $x[ 'meta' ][ 'id' ] ] = array();
            }
            $inv_count[ $x[ 'meta' ][ 'id' ] ][] = ( $x[ 'meta_key' ] );
        }

        $meta = json_decode( $recipe[ 'meta_value' ], TRUE );

/*$this->ag->debug_print( $meta );
$this->ag->debug_print( $inv_count );*/

        $can_craft = TRUE;
        foreach ( $meta[ 'pre' ] as $x ) {
            $this->ag->debug_print( $x );
            if ( count( $inv_count[ $x[ 0 ] ] ) < $x[ 2 ] ) {
                $can_craft = FALSE;
            }
        }

        if ( $can_craft ) {
            $this->ag->debug_print( 'can craft!' );
        }

        $this->ag->debug_print( $recipe );

        exit;
    }

}