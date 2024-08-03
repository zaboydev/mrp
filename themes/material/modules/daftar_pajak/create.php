<?= form_open(site_url($module['route'] . '/save'), array(
  'autocomplete'  => 'off',
  'id'            => 'form-create-data',
  'class'         => 'form form-validate form-xhr ui-front',
  'role'          => 'form'
)); ?>

<div class="card style-default-bright">
  <div class="card-head style-primary-dark">
    <header>Create New <?= $module['label']; ?></header>

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
              <input type="text" name="description" id="description" class="form-control" data-validation-exception="" required>
              <label for="description">Name</label>
            </div>

            <div class="form-group">
              <div class="input-group">
                <div class="input-group-content">

                  <input type="number" name="percentase" id="percentase" class="form-control" data-validation-exception="" required>
                  <label for="percentase">Percentage</label>
                </div>
                <span class="input-group-addon"> % </span>
              </div>
            </div>

            <div class="form-group">
              <div class="radio">
                <input type="checkbox" name="pemotongan" id="pemotongan" value="yes">
                <label for="pemotongan">Pemotongan</label>
              </div>
              <label for="pemotongan">Pemotongan</label>

            </div>

            <div class="form-group">
              <select name="akun_pajak_penjualan" class="form-control" style="width: 100%">
                <option value="">-- SELECT Account --</option>
                <?php foreach (getAccountsMrp() as $key => $account) : ?>
                <option value="<?= $account['coa']; ?>">
                <?= $account['coa']; ?> <?= $account['group']; ?>
                </option>
                <?php endforeach; ?>
              <select>
              <label for="akun_pajak_penjualan" id="akun_pajak_penjualan">Akun Pajak Penjualan</label>

            </div>

            <div class="form-group">
              <select name="akun_pajak_pembelian" class="form-control" style="width: 100%">
                <option value="">-- SELECT Account --</option>
                <?php foreach (getAccountsMrp() as $key2 => $account2) : ?>
                <option value="<?= $account2['coa']; ?>">
                <?= $account2['coa']; ?> <?= $account2['group']; ?>
                </option>
                <?php endforeach; ?>
              <select>
              <label for="akun_pajak_pembelian" id="akun_pajak_pembelian">Akun Pajak Pembelian</label>
              
            </div>

            <div class="form-group">
              <textarea name="notes" id="notes" class="form-control"></textarea>
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
    <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
      <i class="md md-save"></i>
    </button>
  </div>
</div>

<?= form_close(); ?>
<script>
  $('[id="pemotongan"]').change(function () {
    if ($('[id="pemotongan"]').is(':checked')) {
      $('#akun_pajak_pembelian').html('Akun Pajak Penjualan');
      $('#akun_pajak_penjualan').html('Akun Pajak Pembelian');
    } else {
      $('#akun_pajak_penjualan').html('Akun Pajak Penjualan');
      $('#akun_pajak_pembelian').html('Akun Pajak Pembelian');
    }
  });
</script>

