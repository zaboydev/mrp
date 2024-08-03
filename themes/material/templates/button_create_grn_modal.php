<div class="section-floating-action-row">
  <div class="btn-group dropup">
    <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-create-document" data-toggle="dropdown">
      <i class="md md-add"></i>
      <small class="top right">Create Goods Received Notes</small>
    </button>

    <ul class="dropdown-menu dropdown-menu-right" role="menu">
      <?php foreach (config_item('auth_inventory') as $category):?>
        <li>
          <a href="<?=site_url($modules['doc_receipt']['route'] .'/create/'. $category);?>"><?=$category;?></a>
        </li>
      <?php endforeach;?>
    </ul>
  </div>
</div>
