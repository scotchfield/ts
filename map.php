<?php

class TSMap {
    private $ag;

    public function __construct( $ag ) {
        $this->ag = $ag;

        $ag->add_state( 'do_page_content', 'map',
            array( $this, 'map_content' ) );
    }

    public function map_content() {
?>
<h3>Twelve Sands Navigation</h3>
<div class="row">
  <div class="col-md-4">
    <h4>Primary Locations</h4>
    <ul>
      <li><a href="?state=zone&zone_id=erebus">Erebus City</a></li>
    </ul>
    <h4>Goods and Services</h4>
    <ul>
      <li><a href="?state=zone&zone_id=turagon">Turagon's General
          Goods</a></li>
    </ul>
    <h4>Recreational Activities</h4>
    <ul>
      <li><a href="?state=zone&zone_id=casino">City Casino</a></li>
    </ul>
  </div>
  <div class="col-md-4">
    <h4>Combat Hubs</h4>
    <ul>
      <li><a href="?state=zone&zone_id=abc">abc</a></li>
      <li><a href="?state=zone&zone_id=def">def</a></li>
    </ul>
  </div>
  <div class="col-md-4">
    <h4>Helpful Links</h4>
    <ul>
      <li><a href="?state=allquests">Find all available quests</a></li>
    </ul>
  </div>
</div>
<?php
        return TRUE;
    }

}
