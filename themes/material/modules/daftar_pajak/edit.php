<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-edit-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
  <div class="card-head style-primary-dark">
    <header>Edit <?= $module['label']; ?></header>

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
      <div class="col-sm-4 col-lg-3">
        
      </div>

      <div class="col-sm-8 col-lg-9">
        <div class="row">
          <div class="col-sm-12 col-lg-6">
            <div class="form-group">
              <input type="text" name="description" id="description" class="form-control" data-validation-exception="" required value="<?= $entity['description']; ?>">
              <label for="description">Name</label>
            </div>
            
            <div class="form-group">
              <div class="input-group">
                <div class="input-group-content">

                  <input type="number" name="percentase" id="percentase" class="form-control" data-validation-exception="" required value="<?= $entity['percentase']; ?>">
                  <label for="percentase">Percentage</label>
                </div>
                <span class="input-group-addon"> % </span>
              </div>
            </div>

            <div class="form-group">
              <div class="radio">
                <input type="checkbox" name="pemotongan" id="pemotongan" value="yes" <?= ("yes" === $entity['pemotongan']) ? 'checked' : ''; ?>>
                <label for="pemotongan">Pemotongan</label>
              </div>
              <label for="pemotongan">Pemotongan</label>

            </div>

            <div class="form-group">
              <select name="akun_pajak_penjualan" class="form-control" style="width: 100%">
                <option value="">-- SELECT Account --</option>
                <?php foreach (getAccountsMrp() as $key => $account) : ?>
                <option value="<?= $account['coa']; ?>" <?= ($account['coa'] === $entity['akun_pajak_penjualan']) ? 'selected' : ''; ?>>
                <?= $account['coa']; ?> <?= $account['group']; ?>
                </option>
                <?php endforeach; ?>
              <select>
              <label for="akun_pajak_penjualan" id="akun_pajak_penjualan"><?= ("yes" === $entity['pemotongan']) ? 'Akun Pajak Pembelian' : 'Akun Pajak Penjualan'; ?></label>

            </div>

            <div class="form-group">
              <select name="akun_pajak_pembelian" class="form-control" style="width: 100%">
                <option value="">-- SELECT Account --</option>
                <?php foreach (getAccountsMrp() as $key2 => $account2) : ?>
                <option value="<?= $account2['coa']; ?>" <?= ($account['coa'] === $entity['akun_pajak_pembelian']) ? 'selected' : ''; ?>>
                <?= $account2['coa']; ?> <?= $account2['group']; ?>
                </option>
                <?php endforeach; ?>
              <select>
              <label for="akun_pajak_pembelian" id="akun_pajak_pembelian"><?= ("yes" === $entity['pemotongan']) ? 'Akun Pajak Penjualan' : 'Akun Pajak Pembelian'; ?></label>
              
            </div>

            <div class="form-group">
              <textarea name="notes" id="notes" class="form-control"><?= $entity['notes']; ?></textarea>
              <label for="notes">Notes</label>
            </div>
          </div>

          <div class="col-sm-12 col-lg-6">
            
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card-foot">
    <input type="hidden" name="id" id="id" value="<?= $entity['id']; ?>">
    <input type="hidden" name="description_exception" id="description_exception" value="<?= $entity['description']; ?>">

    <?php if (is_granted($module, 'delete')) : ?>
      <a href="<?= site_url($module['route'] . '/delete'); ?>" class="btn btn-floating-action btn-danger btn-xhr-delete ink-reaction" id="modal-delete-data-button" data-title="delete">
        <i class="md md-delete"></i>
      </a>
    <?php endif; ?>

    <div class="pull-right">
      <button type="submit" id="modal-edit-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction" data-title="save and update">
        <i class="md md-save"></i>
      </button>
    </div>

    <input type="reset" name="reset" class="sr-only">
  </div>
</div>

<?= form_close(); ?>