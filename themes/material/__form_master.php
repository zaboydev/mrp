<?=form_open(current_url(), array(
  'autocomplete'  => 'off',
  'class'         => 'form form-validate floating-label',
  'id'            => 'ajax-form-data'
));?>
  <div class="card">
    <div class="card-head style-primary-dark">
      <header><?=PAGE_TITLE;?></header>
    </div>

    <div class="card-body">
      <?php
      if ( $this->session->flashdata('alert') )
        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);

      $this->load->view($module['view'] . PAGE_ROLE);
      ?>
    </div>
  </div>
<?=form_close();?>
