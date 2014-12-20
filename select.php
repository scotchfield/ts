<?php

class TSSelect {
    private $ag;

    public function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'state_set', FALSE, array( $this, 'select_check' ) );
        $ag->add_state( 'do_page_content', 'select',
            array( $this, 'select_content' ) );
    }

    public function select_check() {
        if ( FALSE == $this->ag->user ) {
            return FALSE;
        }

        if ( FALSE == $this->ag->char ) {
            $this->ag->set_state( 'select' );
        }

        return TRUE;
    }

    public function select_content() {
        $char_obj = $this->ag->c( 'user' )->get_characters_for_user(
            $this->ag->user[ 'id' ] );
?>
<div class="row">
  <div class="col-md-2">
    &nbsp;
  </div>
  <div class="col-md-8">

<h1 class="text-center">Welcome back,
<?php echo( $this->ag->user[ 'user_name' ] ); ?>.</h1>

  <h1 class="page_section">SELECT A CHARACTER</h1>

<?php
        if ( count( $char_obj ) == 0 ) {
            echo( '<h3 class="text-center">None found!</h3>' );
        } else {
            foreach ( $char_obj as $char ) {
                echo( '<h3 class="text-center">' .
                      '<a href="game-setting.php?state=select_character' .
                      '&amp;id=' . $char[ 'id' ] . '">' .
                      $char[ 'character_name' ] . '</a></h3>' );
            }
        }

        if ( count( $char_obj ) < $this->ag->user[ 'max_characters' ] ) {
?>
<h1 class="text-center">Create a character</h1>
<form name="char_form" id="char_form" method="get" action="game-setting.php">
<div class="form-group">
<label>Character Name</label>
<input class="form-control" name="char_name" id="char_name" value="" type="text">
</div>
<button type="submit" class="btn btn-default">Let's go!</button>
<input type="hidden" name="state" value="new_character">
</form>
<?php
        }
?>
  </div>
  <div class="col-md-2">

  </div>
</div>
<?php
        return TRUE;
    }

}