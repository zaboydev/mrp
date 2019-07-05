<section class="<?=(isset($page['actions'])) ? 'has-actions' : '';?> style-default">
  <div class="section-body">

    <?php
    if (isset($page['content'])):
      $this->load->view($page['content']);
    else:
      if (in_array('datatable', $page['requirement'])):
        $this->load->view('material/__datatable');
      endif;

      if (in_array('datamodal', $page['requirement'])):
        $this->load->view('material/__datamodal');
      endif;

      if (in_array('form_create', $page['requirement'])):
        $this->load->view('material/__form_create');
      endif;

      if (in_array('form_edit', $page['requirement'])):
        $this->load->view('material/__form_edit');
      endif;
    endif;?>

  </div>

  <?php if (isset($page['actions'])):?>
    <div class="section-action style-default-bright">
      <div class="section-action-row">
        <div class="btn-toolbar">
          <div id="core-buttons" class="pull-left btn-group">
            <button class="btn btn-icon-toggle btn-lg ink-reaction btn-back" data-toggle="back">
              <i class="md md-arrow-back"></i>
            </button>

            <button class="btn btn-icon-toggle btn-lg ink-reaction btn-home" data-toggle="redirect" data-url="<?=site_url();?>">
              <i class="md md-home"></i>
            </button>
          </div>

          <!-- ACTIONS LEFT -->
          <?php if (isset($page['actions']['left'])):?>
            <div id="additional-buttons" class="btn-group">
              <?php foreach ($page['actions']['left'] as $key => $button):?>
                <a class="btn btn-icon-toggle ink-reaction <?=$button['class'];?>" id="<?=$button['id'];?>" href="<?=$button['link'];?>" target="<?=$button['target'];?>" data-provide="tooltip" data-placement="left" title="<?=$button['title'];?>"><i class="<?=$button['icon'];?>"></i></a>
              <?php endforeach;?>
            </div>
          <?php endif;?>
        </div>

        <!-- DATATABLE BUTTONS -->
        <div class="section-toolbar-action">
        </div>
      </div>

      <!-- ACTIONS RIGHT -->
      <?php if (isset($page['actions']['right'])):?>
        <div class="section-floating-action-row">
          <?php foreach ($page['actions']['right'] as $key => $button):?>
            <?php if (is_array($button['link'])):?>
              <div class="btn-group dropup">
                <button type="button" class="btn btn-floating-action ink-reaction <?=$button['class'];?>" id="<?=$button['id'];?>" data-toggle="dropdown">
                  <i class="<?=$button['icon'];?>" data-provide="tooltip" data-placement="left" title="<?=$button['title'];?>"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                  <?php foreach ($button['link'] as $label => $url):?>
                    <li><a href="<?=$url;?>"><?=$label;?></a></li>
                  <?php endforeach;?>
                </ul>
              </div>
            <?php else:?>
              <a class="btn btn-floating-action <?=$button['class'];?>" id="<?=$button['id'];?>" href="<?=$button['link'];?>" target="<?=$button['target'];?>" data-provide="tooltip" data-placement="left" title="<?=$button['title'];?>">
                <i class="<?=$button['icon'];?>"></i>
              </a>
            <?php endif;?>
          <?php endforeach;?>
        </div>
      <?php endif;?>

    </div>
  <?php endif;?>

</section>
