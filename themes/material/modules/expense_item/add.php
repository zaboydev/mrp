<?=form_open(site_url($module['route'] .'/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
));?>

  <div class="card style-default-bright">
    <div class="card-head style-primary-dark">
      <header>Edit <?=$module['label'];?></header>

      <div class="tools">
        <div class="btn-group">
          <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
            <i class="md md-close"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-sm-12">
          <label>Account's List</label>
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <div class="pull-left">
                <button type="button" class="btn btn-sm btn-primary btn-show-report" id="selectAll">Select All </button>
                <button type="button" class="btn btn-sm btn-primary btn-show-report" id="deselectAll">Deselect All </button>
              </div>
            </div>
          </div>
          <div class="row">
            <?php foreach ($entity['accounts'] as $i => $account):?>
            <div class="col-sm-4">
              <div class="checkbox">
                <input type="checkbox" name="account[]" id="account_<?=$i;?>" value="<?=$account['id'];?>" <?=(in_array($account['id'], expense_item_without_po())) ? 'checked' : '';?> >
                <label for="account_<?=$i;?>">
                    <?=$account['account_code'];?> - <?=$account['account_name'];?>
                </label>
              </div>
            </div>
            <?php endforeach;?>
          </div>

              

        </div>
      </div>
    </div>

    <div class="card-foot">
      <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
      

      <?php if (is_granted($module, 'delete')):?>
        <a href="<?=site_url($module['route']. '/delete');?>" class="hide btn btn-floating-action btn-danger btn-xhr-delete ink-reaction" id="modal-delete-data-button" data-title="delete">
          <i class="md md-delete"></i>
        </a>
      <?php endif;?>

      <div class="pull-right">
        <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
          <i class="md md-save"></i>
        </button>
      </div>

      <input type="reset" name="reset" class="sr-only">
    </div>
  </div>

<?=form_close();?>

<script>
  $(document).ready(function () {
    $("#selectAll").click(function(){
      $('input[name="account[]"]').prop('checked', true);
    });

    $("#deselectAll").click(function(){
      $('input[name="account[]"]').prop('checked', false);
    });
  });
</script>
