<?=form_open(current_url(), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
));?>

  <div class="card style-default-bright">
    <div class="card-head style-primary-dark">
      <header>Change Account</header>

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
        <div class="col-sm-12 col-lg-8">
          <div class="row">
            <div class="col-sm-6 col-lg-8">
                <div class="form-group">
                    <input type="text" name="warehouse" id="warehouse" class="form-control" value="<?=$entity['base'];?>" readonly>
                    <label for="warehouse">Base</label>
                </div>
                <div class="form-group">
                    <input type="hidden" name="id" value="<?=$entity['id']?>">
                    <input type="text" name="pr_number" id="pr_number" class="form-control" required value="<?=($entity['document_number']);?>" readonly>
                    <label for="pr_number">Document No.</label>
                </div>

                <div class="form-group">
                    <input type="text" name="status" id="status" class="form-control" required value="<?=$entity['status'];?>" readonly>
                    <label for="status">Status</label>
                </div>
            </div>
            <div class="col-sm-6 col-lg-8">
                <div class="form-group">
                    <input type="text" name="amount_total" id="amount_total" class="form-control" value="<?=$entity['total_amount'];?>" readonly>
                    <label for="amount_total">Amount Total</label>
                </div>

                <div class="form-group">
                    <input type="text" name="currency" id="currency" class="form-control" value="<?=$entity['currency'];?>" readonly>
                    <label for="currency">Currency</label>
                </div>

                <div class="form-group">
                    <select name="account" id="account" class="form-control">
                    <option value="">-- SELECT Account</option>
                    <?php foreach (getAccount($entity['type']) as $key => $account) : ?>
                        <option value="<?= $account['coa']; ?>" <?= ($account['coa'] == $entity['coa_kredit']) ? 'selected' : ''; ?>>
                        <?= $account['coa']; ?> <?= $account['group']; ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="vendor">Account</label>
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>   


    <div class="card-foot">

      <div class="pull-right">
        <a href="<?=site_url($module['route'] .'/save_change_account');?>" type="button" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
          <i class="md md-save"></i>
        </a>
        
      </div>
    </div>
  </div>


<?=form_close();?>
