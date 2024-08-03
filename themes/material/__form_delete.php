<?=form_open(current_url(), array(
  'class' => 'form',
  'id'    => 'form-delete-data',
));?>
  <div class="card">
    <div class="card-head style-danger">
      <header>
        <i class="fa fa-exclamation-circle fa-tag"></i>
        Delete <?=$module['label'];?>
      </header>
    </div>

    <div class="card-body">
      <?php $this->load->view($module['view'] . PAGE_ROLE);?>
    </div>
  </div>
<?=form_close();?>
