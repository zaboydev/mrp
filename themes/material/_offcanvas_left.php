<?php if (isset($page['offcanvas'])):
  $this->load->view($page['offcanvas']);
endif;?>

<?php if (is_granted($module, 'import')):?>
  <div id="offcanvas-form-import" class="offcanvas-pane width-8">
    <div class="offcanvas-head style-primary-dark">
      <header>Import <?=$module['label'];?></header>
      <div class="offcanvas-tools">
        <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
          <i class="md md-close"></i>
        </a>
      </div>
    </div>
    
    <div class="offcanvas-body">
      <?php $this->load->view($module['view'] .'/import');?>
    </div>
  </div>
<?php endif;?>
