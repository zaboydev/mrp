<div id="offcanvas-right" class="offcanvas-pane width-8">
  <div class="offcanvas-head style-primary-dark">
    <header>Options</header>
    <div class="offcanvas-tools">
      <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
        <i class="md md-close"></i>
      </a>
    </div>
  </div>
  <div class="offcanvas-body no-padding">
    <ul class="list ">
      <li class="tile">
        <a class="tile-content ink-reaction" href="#offcanvas-form-import" data-toggle="offcanvas">
          <div class="tile-icon">
            <i class="md md-import-export"></i>
          </div>
          <div class="tile-text">
            Import Data
            <small>import any data from CSV file</small>
          </div>
        </a>
      </li>
    </ul>
  </div>
</div>

<?php if ( in_array('datatable', $page['requirement']) ):?>
  <div id="offcanvas-datatable-filter" class="offcanvas-pane width-12">
    <div class="offcanvas-head style-primary-dark">
      <header>Data Filter</header>
      <div class="offcanvas-tools">
        <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
          <i class="md md-close"></i>
        </a>
      </div>
    </div>
    <div class="offcanvas-body no-padding">
      <ul class="list ">
        <li class="tile">
          <a class="tile-content ink-reaction" href="#offcanvas-column-toggle" data-toggle="offcanvas">
            <div class="tile-icon">
              <i class="fa fa-sliders"></i>
            </div>
            <div class="tile-text">
              Column Visibility
              <small>Show/hide any columns</small>
            </div>
          </a>
        </li>
      </ul>

      <?php if (isset($page['sidebar']))
        $this->load->view($page['sidebar']);?>
    </div>
  </div>

  <div id="offcanvas-column-toggle" class="offcanvas-pane width-8">
    <div class="offcanvas-head style-primary-dark">
      <header>Columns Visibility</header>
      <div class="offcanvas-tools">
        <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
          <i class="md md-close"></i>
        </a>
        <a class="btn btn-icon-toggle pull-right" href="#offcanvas-datatable-filter" data-toggle="offcanvas">
          <i class="md md-arrow-back"></i>
        </a>
      </div>
    </div>
    <div class="offcanvas-body no-padding">
      <ul class="list divider-full-bleed">
        <?php foreach ($grid['column'] as $key => $value):?>
          <?php if ($value != NULL):?>
            <li class="tile">
              <a class="tile-content ink-reaction column-toggle" href="javascript:void(0);" data-column="<?=$key;?>" data-label="<?=$value;?>">
                <div class="tile-text">
                  <?=$value;?>
                </div>
              </a>
            </li>
          <?php endif;?>
        <?php endforeach;?>
      </ul>
    </div>
  </div>
<?php endif;?>