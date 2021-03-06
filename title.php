<?php

class TSTitle {
    private $ag;

    public function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'do_page_content', 'title',
            array( $this, 'title_content' ) );
    }

    public function title_content() {
        if ( FALSE != $this->ag->char ) {
            //header( 'Location: game-logout.php' );

            // todo: this should force an exit using arcadia!
            // find a better way to handle the header redirect, it's a
            //  risky phpunit test
            return FALSE;
        }

        $this->ag->ts->header_output_head();
?><body>
<div class="container">

  <div class="row">
    <div class="col-md-8">
      <img src="<?php echo( GAME_CUSTOM_STYLE_URL ); ?>tslogo.png" width="90%">
    </div>
    <div class="col-md-4">

      <form class="form-horizontal" role="form" name="login_form"
            id="login_form" method="post" action="game-login.php">
        <div class="form-group">
          <label for="login_user"
                 class="col-sm-4 control-label">Username</label>
          <div class="col-sm-8">
            <input class="form-control input-sm" name="user"
                   id="login_user" value="" type="text">
          </div>
        </div>
        <div class="form-group">
          <label for="login_pass"
                 class="col-sm-4 control-label">Password</label>
          <div class="col-sm-8">
            <input class="form-control" name="pass"
                   id="login_pass" value="" type="password">
          </div>
        </div>
        <div class="text-right">
          <button type="submit" class="btn btn-sm btn-default">Log in!</button>
        </div>
        <input type="hidden" name="state" value="login">
      </form>

    </div>
  </div>

<?php
    $err_obj = array(
        1 => 'Please provide a username.',
        2 => 'Please provide a password.',
        3 => 'Please provide a valid email address.',
        4 => 'That username already exists.',
        5 => 'That email address is already in use.',
        6 => 'That username and password combination does not exist.',
        100 => 'Thanks! Please check your email for a validation link.',
        101 => 'That account is already validated!',
        102 => 'Success! You can now log in.',
    );

    if ( FALSE != $this->ag->get_arg( 'notify' ) ) {
        $notify = intval( $this->ag->get_arg( 'notify' ) );
        if ( isset( $err_obj[ $notify ] ) ) {
            echo( '<div class="row text-center"><h2>' .
                  $err_obj[ $notify ] . '</h2></div>' );
        }
    }
?>

  <div class="row">

    <div class="col-md-8">
      <h3>Title Here!</h3>
      <p>Description of game here!</p>
      <h3>Always free</h3>
      <p><?php echo( GAME_NAME ); ?> is free to play, and we are devoted
        to keeping it that way. Although we offer microtransactions,
        <?php echo( GAME_NAME ); ?> is not a "pay-to-win" game. Purchases
        are strictly optional, and there will always be ways to obtain the
        same items without payment.</p>
    </div>
    <div class="col-md-4">

    </div>

  </div>

  <div class="row">

    <div class="col-md-6">
    </div>

    <div class="col-md-6">
      <h3 class="text-right">Register for a free account</h3>

      <form class="form-horizontal" name="register_form" id="register_form"
            method="post" action="game-login.php">
        <div class="form-group">
          <label for="register_user"
                 class="col-sm-4 control-label">Username</label>
          <div class="col-sm-8">
            <input class="form-control input-sm" name="user"
                   id="register_user" value="" type="text">
          </div>
        </div>
        <div class="form-group">
          <label for="register_pass"
                 class="col-sm-4 control-label">Password</label>
          <div class="col-sm-8">
            <input class="form-control" name="pass"
                   id="register_pass" value="" type="password">
          </div>
        </div>
        <div class="form-group">
          <label for="register_email"
                 class="col-sm-4 control-label">Email</label>
          <div class="col-sm-8">
            <input class="form-control" name="email"
                   id="register_email" value="" type="text">
          </div>
        </div>
        <div class="text-right">
          <button type="submit"
                  class="btn btn-sm btn-default">Register</button>
        </div>
        <input type="hidden" name="state" value="register">
      </form>

    </div>
  </div>

</div>

<script src="<?php echo GAME_CUSTOM_STYLE_URL; ?>jquery.min.js"></script>
<script src="<?php echo GAME_CUSTOM_STYLE_URL; ?>bootstrap.min.js"></script>

</body>
</html>

<?php
        return TRUE;
    }

}

