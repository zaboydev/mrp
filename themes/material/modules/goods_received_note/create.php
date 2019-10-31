<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">
    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document')); ?>
    <div class="card">
      <div class="card-head style-primary-dark">
        <header><?= PAGE_TITLE; ?></header>
      </div>
      <div class="card-body no-padding">
        <?php
        if ($this->session->flashdata('alert'))
          render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
        ?>

        <div class="document-header force-padding">
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">

                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['receipt']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                    <label for="document_number">Document No.</label>
                  </div>
                  <span class="input-group-addon"><?= receipt_format_number(); ?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="received_date" id="received_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['receipt']['received_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_date'); ?>" required>
                <input type="hidden" name="opname_start_date" id="opname_start_date" data-date-format="yyyy-mm-dd" class="form-control" value="<?= last_publish_date(); ?>" readonly>
                <label for="received_date">Received Date</label>
              </div>

              <div class="form-group">
                <select name="warehouse" id="warehouse" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_warehouse'); ?>" required>
                  <?php foreach (available_warehouses() as $w => $warehouse) : ?>
                    <option value="<?= $warehouse; ?>" <?= ($_SESSION['receipt']['warehouse'] == $warehouse) ? 'selected' : ''; ?>>
                      <?= $warehouse; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="warehouse">Warehouse</label>
              </div>

              <div class="form-group">
                <input type="text" name="received_by" id="received_by" class="form-control" value="<?= $_SESSION['receipt']['received_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_by'); ?>" required>
                <label for="received_by">Received By</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-4">
              <div class="form-group">
                <select name="received_from" id="received_from" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_from'); ?>" required>
                  <option value="">-- Not from Vendor</option>
                  <?php foreach (available_vendors() as $key => $vendor) : ?>
                    <option value="<?= $vendor; ?>" <?= ($vendor == $_SESSION['receipt']['received_from']) ? 'selected' : NULL; ?>>
                      <?= $vendor; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="received_from">Received From</label>
              </div>

              <div class="form-group">
                <input type="text" name="known_by" id="known_by" class="form-control" value="<?= $_SESSION['receipt']['known_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_known_by'); ?>" required>
                <label for="known_by">Known By</label>
              </div>

              <div class="form-group">
                <input type="text" name="approved_by" id="approved_by" class="form-control" value="<?= $_SESSION['receipt']['approved_by']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_approved_by'); ?>">
                <label for="approved_by">Approved By</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['receipt']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['receipt']['items'])) : ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th></th>
                  <th>id</th>
                  <th>Group</th>
                  <th>Description</th>
                  <th>P/N</th>
                  <th>Alt. P/N</th>
                  <th>S/N</th>
                  <th>Qty</th>
                  <th>Unit</th>
                  <th>Condition</th>
                  <th>Stores</th>
                  <th>Order Number</th>
                  <th>Ref./Invoice</th>
                  <th>AWB Number</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['receipt']['items'] as $i => $items) : ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="1">

                      <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                        <i class="fa fa-trash"></i>
                      </a>
                      <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'>
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                    <td>
                      <?= $items['id']; ?>
                    </td>
                    <td>
                      <?= $items['group']; ?>
                    </td>
                    <td>
                      <?= $items['description']; ?>
                    </td>
                    <td class="no-space">
                      <?= $items['part_number']; ?>
                    </td>
                    <td class="no-space">
                      <?= $items['alternate_part_number']; ?>
                    </td>
                    <td>
                      <?= $items['serial_number']; ?>
                    </td>
                    <td>
                      <?= number_format($items['received_quantity'], 2); ?>
                    </td>
                    <td>
                      <?= $items['unit_pakai']; ?>
                    </td>
                    <td>
                      <?= $items['condition']; ?>
                    </td>
                    <td>
                      <?= $items['stores']; ?>
                    </td>
                    <td>
                      <?= $items['purchase_order_number']; ?>
                    </td>
                    <td>
                      <?= $items['reference_number']; ?>
                    </td>
                    <td>
                      <?= $items['awb_number']; ?>
                    </td>
                    <td>
                      <?= $items['remarks']; ?>
                    </td>
                    <td>
                      <?= $items['received_unit_value']; ?>
                      <?= $items['kurs_dollar']; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <?php //if (empty($_SESSION['receipt']['received_from']) === FALSE):
          ?>
          <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
            Add Item
          </a>
          <?php //endif;
          ?>

          <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
            Discard
          </a>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
  </div>

  <div id="modal-add-item" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-add-item-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header style-primary-dark">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="modal-add-item-label">Add Item</h4>
        </div>

        <?= form_open(site_url($module['route'] . '/add_item'), array(
          'autocomplete' => 'off',
          'id'    => 'ajax-form-create-document',
          'class' => 'form form-validate ui-front',
          'role'  => 'form'
        )); ?>

        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" id="search_purchase_order" data-search-for="purchase_order" class="form-control" data-source="<?= site_url($module['route'] . '/search_purchase_order'); ?>">
                    <label for="search_item">Search item from Purchase Order</label>
                  </div>
                  <span class="input-group-addon">
                    <i class="md md-search"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-12 col-lg-8">
              <div class="row">
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                      <input type="text" name="serial_number" id="serial_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_serial/'); ?>">
                      <label for="serial_number">Serial Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="part_number" id="part_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>" required>
                      <label for="part_number">Part Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="description" id="description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" data-source="<?= site_url($modules['ajax']['route'] . '/json_item_description/' . $_SESSION['receipt']['category']); ?>" required>
                      <label for="description">Description</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="alternate_part_number" id="alternate_part_number" data-tag-name="alternate_part_number" data-source="<?= site_url($modules['ajax']['route'] . '/json_alternate_part_number/' . $_SESSION['receipt']['category']); ?>" class="form-control input-sm">
                      <label for="alternate_part_number">Alt. Part Number</label>
                    </div>

                    <div class="form-group">
                      <select name="group" id="group" data-tag-name="group" class="form-control input-sm" required>
                        <option>-- Select One --</option>
                        <?php foreach (available_item_groups_2($_SESSION['receipt']['category']) as $group) : ?>
                          <option value="<?= $group['group']; ?>">
                            <?= $group['group']; ?> - <?= $group['coa']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label for="group">Item Group</label>
                    </div>
                  </fieldset>
                </div>
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>Storage</legend>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                          <input type="text" name="quantity_order" id="quantity_order" data-tag-name="quantity_order" class="form-control input-sm" value="1" required>

                        </div>
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                          <input type="text" name="unit" id="unit" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" required>
                        </div>
                      </div>
                      <label for="received_quantity">Qty & Unit Terima</label>
                    </div>


                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                          <input type="text" name="unit_pakai" id="unit_pakai" data-tag-name="unit_pakai" data-search-for="unit_pakai" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm">
                        </div>
                        <div class="col-lg-6 col-sm-6 col-xs-6">

                          <input type="text" id="received_quantity" class="form-control input-sm" name="received_quantity" value="1" readonly="readonly">
                        </div>
                      </div>
                      <label for="kurs">Unit Pakai</label>
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-3 col-sm-3 col-xs-3">
                          <input type="text" name="satuan" id="satuan" data-tag-name="received_quantity" class="form-control input-sm" value="1" readonly="readonly">
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-3">

                          <input type="text" name="received_unit" id="received_unit" data-tag-name="received_unit" data-search-for="received_unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" readonly>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-3">
                          <input type="text" id="isi" class="form-control input-sm" name="isi" value="1" required>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-3">
                          <input type="text" name="unit_used" id="unit_used" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" readonly>
                        </div>
                      </div>
                      <label for="received_quantity">Unit Konversi</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="minimum_quantity" id="minimum_quantity" data-tag-name="minimum_quantity" class="form-control input-sm" value="0" required>
                      <label for="minimum_quantity">Minimum Quantity</label>
                    </div>

                    <?php if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN') : ?>
                      <div class="form-group">
                        <div class="row">
                          <div class="col-lg-6 col-sm-6">
                            <select class="form-control input-sm" id="kurs" name="kurs" required>
                              <!-- <option>-Pilih Mata Uang-</option> -->
                              <option value="rupiah">Rupiah</option>
                              <option value="dollar">USD Dollar</option>
                            </select>

                          </div>
                          <div class="col-lg-6 col-sm-6">
                            <input type="text" id="received_unit_value" class="form-control input-sm" name="received_unit_value" value="0" step=".02">
                            <input type="hidden" id="value_order" class="form-control input-sm" name="value_order" value="0" step=".02">
                          </div>
                        </div>
                        <label for="kurs">Price per Unit</label>
                      </div>
                    <?php else : ?>
                      <div class="form-group hide">
                        <div class="row">
                          <div class="col-lg-6 col-sm-6">
                            <select class="form-control input-sm" id="kurs" name="kurs" required>
                              <!-- <option>-Pilih Mata Uang-</option> -->
                              <option value="rupiah">Rupiah</option>
                              <option value="dollar">USD Dollar</option>
                            </select>

                          </div>
                          <div class="col-lg-6 col-sm-6">
                            <input type="text" id="received_unit_value" class="form-control input-sm" name="received_unit_value" value="0" step=".02">
                            <input type="hidden" id="value_order" class="form-control input-sm" name="value_order" value="0" step=".02">
                          </div>
                        </div>
                        <label for="kurs">Price per Unit</label>
                      </div>
                    <?php endif; ?>

                    <div class="form-group">
                      <select name="condition" id="condition" class="form-control input-sm">
                        <?php foreach (available_conditions() as $key => $condition) : ?>
                          <option value="<?= $condition; ?>"><?= $condition; ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label for="condition">Item Condition</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="stores" id="stores" data-tag-name="stores" data-search-for="stores" data-source="<?= site_url($modules['ajax']['route'] . '/json_stores/' . $_SESSION['receipt']['category']); ?>" class="form-control input-sm" required>
                      <label for="stores">Stores</label>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>

            <div class="col-sm-12 col-lg-4">
              <fieldset>
                <legend>Optional</legend>

                <div class="form-group">
                  <div class="row">
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                      <div class="radio">
                        <input type="checkbox" name="no_expired_date" id="no_expired_date" value="no" required="required">
                        <label for="no_expired_date">No Expired Date</label>
                      </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                      <input type="text" name="expired_date" id="expired_date" data-tag-name="expired_date" class="form-control input-sm" required="required">
                    </div>
                  </div>

                  <label for="expired_date">Expired Date</label>
                </div>

                <div class="form-group">
                  <input type="text" name="purchase_order_number" id="purchase_order_number" data-tag-name="purchase_order_number" class="form-control input-sm">
                  <label for="purchase_order_number">Order Number</label>
                  <input type="hidden" name="purchase_order_item_id" id="purchase_order_item_id" />
                </div>

                <div class="form-group">
                  <input type="text" name="reference_number" id="reference_number" data-tag-name="reference_number" class="form-control input-sm">
                  <label for="reference_number">Ref./Invoice</label>
                </div>

                <div class="form-group">
                  <input type="text" name="awb_number" id="awb_number" data-tag-name="awb_number" class="form-control input-sm" required="required">
                  <label for="awb_number">AWB Number</label>
                </div>

                <div class="form-group">
                  <textarea name="remarks" id="remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                  <label for="remarks">Remarks</label>
                </div>

                <div class="form-group">
                  <input type="text" name="kode_stok" id="kode_stok" data-tag-name="kode_stok" class="form-control input-sm" readonly="readonly">
                  <label for="kode_stok">Kode Stok</label>
                </div>

                <!-- <div class="form-group">
                    <textarea name="kode_akunting" id="kode_akunting" data-tag-name="kode_akunting" class="form-control input-sm"></textarea>
                    <label for="remarks">Kode Akunting</label>
                  </div> -->
              </fieldset>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>
          <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
            Add Item
          </button>

          <input type="hidden" name="consignor" id="consignor">
          <input type="reset" name="reset" class="sr-only">
        </div>

        <?= form_close(); ?>
      </div>
    </div>
  </div>

  <div id="modal-edit-item" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-add-item-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header style-primary-dark">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="modal-add-item-label"></h4>
        </div>

        <?= form_open(site_url($module['route'] . '/edit_item'), array(
          'autocomplete' => 'off',
          'id'    => 'ajax-form-edit-document',
          'class' => 'form form-validate ui-front',
          'role'  => 'form'
        )); ?>

        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" id="edit_search_purchase_order" data-search-for="purchase_order" class="form-control" data-source="<?= site_url($module['route'] . '/search_purchase_order'); ?>">
                    <label for="search_item">Search item from Purchase Order</label>
                  </div>
                  <span class="input-group-addon">
                    <i class="md md-search"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-12 col-lg-8">
              <div class="row">
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                      <input type="text" name="serial_number" id="edit_serial_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_serial/'); ?>">
                      <label for="serial_number">Serial Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="part_number" id="edit_part_number" class="form-control input-sm input-autocomplete" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>" required>
                      <label for="part_number">Part Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="description" id="edit_description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" data-source="<?= site_url($modules['ajax']['route'] . '/json_item_description/' . $_SESSION['receipt']['category']); ?>" required>
                      <label for="description">Description</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="alternate_part_number" id="edit_alternate_part_number" data-tag-name="alternate_part_number" data-source="<?= site_url($modules['ajax']['route'] . '/json_alternate_part_number/' . $_SESSION['receipt']['category']); ?>" class="form-control input-sm">
                      <label for="alternate_part_number">Alt. Part Number</label>
                    </div>

                    <div class="form-group">
                      <select name="group" id="edit_group" data-tag-name="group" class="form-control input-sm" required>
                        <option>-- Select One --</option>
                        <?php foreach (available_item_groups_2($_SESSION['receipt']['category']) as $group) : ?>
                          <option value="<?= $group['group']; ?>">
                            <?= $group['group']; ?> - <?= $group['coa']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label for="group">Item Group</label>
                    </div>
                  </fieldset>
                </div>
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>Storage</legend>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                          <input type="text" name="quantity_order" id="edit_quantity_order" data-tag-name="edit_quantity_order" class="form-control input-sm" value="1" required>

                        </div>
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                          <input type="text" name="unit" id="edit_unit" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" required>
                        </div>
                      </div>
                      <label for="received_quantity">Qty & Unit Terima</label>
                    </div>


                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-6 col-sm-6 col-xs-6">
                          <input type="text" name="unit_pakai" id="edit_unit_pakai" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm">
                        </div>
                        <div class="col-lg-6 col-sm-6 col-xs-6">

                          <input type="text" id="edit_received_quantity" class="form-control input-sm" name="received_quantity" value="1" readonly="readonly">
                        </div>
                      </div>
                      <label for="kurs">Unit Pakai</label>
                    </div>

                    <div class="form-group">
                      <div class="row">
                        <div class="col-lg-3 col-sm-3 col-xs-3">
                          <input type="text" name="satuan" id="edit_satuan" data-tag-name="edit_satuan" class="form-control input-sm" value="1" readonly="readonly">
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-3">

                          <input type="text" name="received_unit" id="edit_received_unit" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" readonly>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-3">
                          <input type="text" id="edit_isi" class="form-control input-sm" name="isi" value="0" required>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-3">
                          <input type="text" name="unit_used" id="edit_unit_used" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" readonly>
                        </div>
                      </div>
                      <label for="received_quantity">Unit Konversi</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="minimum_quantity" id="edit_minimum_quantity" data-tag-name="minimum_quantity" class="form-control input-sm" value="0" required>
                      <label for="minimum_quantity">Minimum Quantity</label>
                    </div>

                    <?php if (config_item('auth_role') == 'SUPERVISOR' || config_item('auth_role') == 'SUPER ADMIN') : ?>
                      <div class="form-group">
                        <div class="row">
                          <div class="col-lg-5 col-sm-5">
                            <select class="form-control input-sm" id="edit_kurs" name="edit_kurs">
                              <option value="rupiah">Rupiah</option>
                              <option value="dollar">USD Dollar</option>
                            </select>

                          </div>
                          <div class="col-lg-7 col-sm-7">
                            <input type="text" id="edit_received_unit_value" class="form-control input-sm" name="received_unit_value" value="0" step=".02">
                            <input type="hidden" id="edit_value_order" class="form-control input-sm" name="value_order" value="0" step=".02">
                          </div>
                        </div>
                        <label for="kurs">Price per Unit</label>
                      </div>
                    <?php else : ?>
                      <div class="form-group hide">
                        <div class="row">
                          <div class="col-lg-5 col-sm-5">
                            <select class="form-control input-sm" id="edit_kurs" name="edit_kurs">
                              <option value="rupiah">Rupiah</option>
                              <option value="dollar">USD Dollar</option>
                            </select>

                          </div>
                          <div class="col-lg-7 col-sm-7">
                            <input type="text" id="edit_received_unit_value" class="form-control input-sm" name="received_unit_value" value="0" step=".02">
                            <input type="hidden" id="edit_value_order" class="form-control input-sm" name="value_order" value="0" step=".02">
                          </div>
                        </div>
                        <label for="kurs">Price per Unit</label>
                      </div>
                    <?php endif; ?>

                    <div class="form-group">
                      <select name="condition" id="edit_condition" class="form-control input-sm">
                        <?php foreach (available_conditions() as $key => $condition) : ?>
                          <option value="<?= $condition; ?>"><?= $condition; ?></option>
                        <?php endforeach; ?>
                      </select>
                      <label for="condition">Item Condition</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="stores" id="edit_stores" data-tag-name="stores" data-search-for="stores" data-source="<?= site_url($modules['ajax']['route'] . '/json_stores/' . $_SESSION['receipt']['category']); ?>" class="form-control input-sm" required>
                      <label for="stores">Stores</label>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>

            <div class="col-sm-12 col-lg-4">
              <fieldset>
                <legend>Optional</legend>

                <!-- <div class="form-group">
                    <input type="text" name="expired_date" id="edit_expired_date" data-tag-name="expired_date" class="form-control input-sm">
                    <label for="expired_date">Expired Date</label>
                  </div> -->

                <div class="form-group">
                  <div class="row">
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                      <div class="radio">
                        <input type="checkbox" name="edit_no_expired_date" id="edit_no_expired_date" value="no">
                        <label for="no_expired_date">No Expired Date</label>
                      </div>
                    </div>
                    <div class="col-lg-6 col-sm-6 col-xs-6">
                      <input type="text" name="edit_expired_date" id="edit_expired_date" data-tag-name="edit_expired_date" class="form-control input-sm">
                    </div>
                  </div>

                  <label for="expired_date">Expired Date</label>
                </div>

                <div class="form-group">
                  <input type="text" name="purchase_order_number" id="edit_purchase_order_number" data-tag-name="purchase_order_number" class="form-control input-sm">
                  <label for="purchase_order_number">Order Number</label>
                  <input type="hidden" name="purchase_order_item_id" id="edit_purchase_order_item_id" />
                </div>

                <div class="form-group">
                  <input type="text" name="reference_number" id="edit_reference_number" data-tag-name="reference_number" class="form-control input-sm">
                  <label for="reference_number">Ref./Invoice</label>
                </div>

                <div class="form-group">
                  <input type="text" name="awb_number" id="edit_awb_number" data-tag-name="awb_number" class="form-control input-sm" required="required">
                  <label for="awb_number">AWB Number</label>
                </div>

                <div class="form-group">
                  <textarea name="remarks" id="edit_remarks" data-tag-name="remarks" class="form-control input-sm"></textarea>
                  <label for="remarks">Remarks</label>
                  <input type="hidden" name="item_id" id="item_id">
                  <input type="hidden" name="stock_in_store_id" id="stock_in_store_id">
                  <input type="hidden" name="receipt_items_id" id="receipt_items_id">
                  <!-- <input type="hidden" name="document_number_receipts_items" id="document_number_receipts_items" >
                    <input type="hidden" name="category_receipts_items" id="category_receipts_items" >
                    <input type="hidden" name="warehouse_receipts_items" id="warehouse_receipts_items" >
                    <input type="hidden" name="master_items_id" id="master_items_id" > -->
                </div>

                <div class="form-group">
                  <input type="text" name="edit_kode_stok" id="edit_kode_stok" data-tag-name="edit_kode_stok" class="form-control input-sm" readonly="readonly">
                  <label for="edit_kode_stok">Kode Stok</label>
                </div>
              </fieldset>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>

          <button type="submit" id="modal-edit-item-submit" class="btn btn-primary btn-edit ink-reaction">
            Edit Item
          </button>


          <input type="hidden" name="consignor" id="edit_consignor">
          <input type="reset" name="reset" class="sr-only">
        </div>

        <?= form_close(); ?>
      </div>
    </div>
  </div>

  <div class="section-action style-default-bright">
    <div class="section-floating-action-row">
      <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>">
        <i class="md md-save"></i>
        <small class="top right">Save Document</small>
      </a>
    </div>
  </div>
</section>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/pace/pace.min.js') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?= html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
<?= html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/moment.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js') ?>
<script>
  Pace.on('start', function() {
    $('.progress-overlay').show();
  });

  Pace.on('done', function() {
    $('.progress-overlay').hide();
  });

  (function($) {
    $.fn.reset = function() {
      this.find('input:text, input[type="email"], input:password, select, textarea').val('');
      this.find('input:radio, input:checkbox').prop('checked', false);
      return this;
    }

    $.fn.redirect = function(target) {
      var url = $(this).data('href');

      if (target == '_blank') {
        window.open(url, target);
      } else {
        window.document.location = url;
      }
    }

    $.fn.popup = function() {
      var popup = $(this).data('target');
      var source = $(this).data('source');

      $.get(source, function(data) {
        var obj = $.parseJSON(data);

        if (obj.type == 'denied') {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error(obj.info, 'ACCESS DENIED!');
        } else {
          $(popup)
            .find('.modal-body')
            .empty()
            .append(obj.info);

          $(popup).modal('show');

          $(popup).on('click', '.modal-header:not(a)', function() {
            $(popup).modal('hide');
          });

          $(popup).on('click', '.modal-footer:not(a)', function() {
            $(popup).modal('hide');
          });
        }
      })
    }
  }(jQuery));

  function submit_post_via_hidden_form(url, params) {
    var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

    $.each(params, function(key, value) {
      var hidden = $('<input type="hidden" />').attr({
        name: key,
        value: JSON.stringify(value)
      });

      hidden.appendTo(f);
    });

    f.submit();
    f.remove();
  }

  function numberFormat(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
      x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
  }

  $(document).on('keydown', function(event) {
    if ((event.metaKey || event.ctrlKey) && (
        String.fromCharCode(event.which).toLowerCase() === '0' ||
        String.fromCharCode(event.which).toLowerCase() === 'a' ||
        String.fromCharCode(event.which).toLowerCase() === 'd' ||
        String.fromCharCode(event.which).toLowerCase() === 'e' ||
        String.fromCharCode(event.which).toLowerCase() === 'i' ||
        String.fromCharCode(event.which).toLowerCase() === 'o' ||
        String.fromCharCode(event.which).toLowerCase() === 's' ||
        String.fromCharCode(event.which).toLowerCase() === 'x')) {
      event.preventDefault();
    }
  });

  $(function() {
    // GENERAL ELEMENTS
    var formDocument = $('#form-document');
    var buttonSubmitDocument = $('#btn-submit-document');
    var buttonDeleteDocumentItem = $('.btn_delete_document_item');
    var buttonEditDocumentItem = $('.btn_edit_document_item');
    var autosetInputData = $('[data-input-type="autoset"]');

    toastr.options.closeButton = true;

    $('[data-toggle="redirect"]').on('click', function(e) {
      e.preventDefault;

      var url = $(this).data('url');

      window.document.location = url;
    });

    $('[data-toggle="back"]').on('click', function(e) {
      e.preventDefault;

      history.back();
    });

    var startDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?> - 1, 1);
    var lastDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?>, 0);
    var last_publish = $('[name="opname_start_date"]').val();
    var today = new Date();
    today.setDate(today.getDate() - 2);
    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
      startDate: today,
      // endDate: last_opname
    });

    $('#expired_date').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd'
      //startDate: '0d'
    });

    $('#edit_expired_date').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd'
      //startDate: '0d'
    });

    $(document).on('click', '.btn-xhr-submit', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('.form-xhr');
      var action = form.attr('action');

      button.attr('disabled', true);

      if (form.valid()) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);

          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            form.reset();

            $('[data-dismiss="modal"]').trigger('click');

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        });
      }

      button.attr('disabled', false);
    });

    $(buttonSubmitDocument).on('click', function(e) {
      e.preventDefault();
      $(buttonSubmitDocument).attr('disabled', true);

      var url = $(this).attr('href');

      $.post(url, formDocument.serialize(), function(data) {
        var obj = $.parseJSON(data);

        if (obj.success == false) {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error(obj.message);
        } else {
          toastr.options.timeOut = 4500;
          toastr.options.closeButton = false;
          toastr.options.progressBar = true;
          toastr.options.positionClass = 'toast-top-right';
          toastr.success(obj.message);

          window.setTimeout(function() {
            window.location.href = '<?= site_url($module['route']); ?>';
          }, 5000);
        }

        $(buttonSubmitDocument).attr('disabled', false);
      });
    });

    $(buttonDeleteDocumentItem).on('click', function(e) {
      e.preventDefault();

      var url = $(this).attr('href');
      var tr = $(this).closest('tr');

      $.get(url);

      $(tr).remove();

      if ($("#table-document > tbody > tr").length == 0) {
        $(buttonSubmit).attr('disabled', true);
      }
    });

    $(autosetInputData).on('change', function() {
      var val = $(this).val();
      var url = $(this).data('source');

      $.get(url, {
        data: val
      });
    });

    $('#isi').data('rule-min', parseInt(1)).data('msg-min', 'min val 1');
    $('#edit_isi').data('rule-min', parseInt(1)).data('msg-min', 'min val 1');

    $('#search_purchase_order').on('click focus', function() {
      $.ajax({
        url: $('#search_purchase_order').data('source'),
        dataType: "json",
        success: function(resource) {
          $('#search_purchase_order').autocomplete({
              autoFocus: true,
              minLength: 3,

              source: function(request, response) {
                var results = $.ui.autocomplete.filter(resource, request.term);
                response(results.slice(0, 5));
              },

              focus: function(event, ui) {
                return false;
              },

              select: function(event, ui) {
                if (ui.item.default_currency == 'USD') {
                  var unit_value = parseInt(ui.item.unit_price) * parseInt(ui.item.exchange_rate);
                } else {
                  var unit_value = parseInt(ui.item.unit_price);
                }

                $('#consignor').val(ui.item.vendor);
                $('#serial_number').val(ui.item.serial_number);
                $('#part_number').val(ui.item.part_number);
                $('#description').val(ui.item.description);
                $('#alternate_part_number').val(ui.item.alternate_part_number);
                $('#group').val(ui.item.group);
                $('#received_quantity').val(parseFloat(ui.item.left_received_quantity));
                $('#quantity_order').val(parseFloat(ui.item.left_received_quantity));
                $('#unit').val(ui.item.unit);
                $('#received_unit').val(ui.item.unit);
                $('#unit_pakai').val(ui.item.unit_pakai);
                $('#unit_used').val(ui.item.unit_pakai);
                $('#received_unit_value').val(parseFloat(unit_value));
                $('#value_order').val(parseFloat(unit_value));
                $('#purchase_order_item_id').val(ui.item.id);
                $('#purchase_order_number').val(ui.item.document_number);
                $('#kode_stok').val(ui.item.kode_stok);
                if (ui.item.default_currency == 'USD') {
                  $('[name="kurs"]').val('dollar');

                } else {
                  $('[name="kurs"]').val('rupiah');

                }

                $('#quantity_order').data('rule-max', parseInt(ui.item.left_received_quantity)).data('msg-max', 'max available ' + ui.item.left_received_quantity);

                // if (ui.item.serial_number != null){
                //   $( inputIssuedQuantity ).val(1).attr('readonly', true);
                // }

                $('#search_purchase_order').val('');

                return false;
              }
            })
            .data("ui-autocomplete")._renderItem = function(ul, item) {
              $(ul).addClass('list divider-full-bleed');

              return $("<li class='tile'>")
                .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
                .appendTo(ul);
            };
        }
      });
    });

    $.ajax({
      url: $('input[id="serial_number"]').data('source'),
      dataType: "json",
      success: function(resource) {
        $('input[id="serial_number"]').autocomplete({
            autoFocus: true,
            minLength: 2,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $('input[id="serial_number"]').val(ui.item.serial_number);
              $('input[id="part_number"]').val(ui.item.part_number);
              $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
              $('input[id="description"]').val(ui.item.description);
              $('select[id="group"]').val(ui.item.group);
              $('input[id="unit"]').val(ui.item.unit);
              $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
              $('#kode_stok').val(ui.item.kode_stok);
              $('#received_unit').val(ui.item.unit);
              $('#unit_pakai').val(ui.item.unit);
              $('#unit_used').val(ui.item.unit);

              $('input[id="received_quantity"]').val(1).prop('readonly', true);

              $('input[id="stores"]').focus();

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    $.ajax({
      url: $('input[id="part_number"]').data('source'),
      dataType: "json",
      success: function(resource) {
        $('input[id="part_number"]').autocomplete({
            autoFocus: true,
            minLength: 2,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              $('input[id="part_number"]').val(ui.item.part_number);
              $('input[id="alternate_part_number"]').val(ui.item.alternate_part_number);
              $('input[id="description"]').val(ui.item.description);
              $('select[id="group"]').val(ui.item.group);
              $('input[id="unit"]').val(ui.item.unit);
              $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
              $('#kode_stok').val(ui.item.kode_stok);
              $('#received_unit').val(ui.item.unit);
              $('#unit_pakai').val(ui.item.unit);
              $('#unit_used').val(ui.item.unit);

              $('input[id="received_quantity"]').focus();

              return false;
            }
          })
          .data("ui-autocomplete")._renderItem = function(ul, item) {
            $(ul).addClass('list divider-full-bleed');

            return $("<li class='tile'>")
              .append('<a class="tile-content ink-reaction"><div class="tile-text">' + item.label + '</div></a>')
              .appendTo(ul);
          };
      }
    });

    $('input[id="received_quantity"]').attr('data-rule-min', parseInt(1)).attr('data-msg-min', 'min available ' + parseInt(1));
    $('input[id="edit_received_quantity"]').attr('data-rule-min', parseInt(1)).attr('data-msg-min', 'min available ' + parseInt(1));

    // $('#issued_quantity').attr('max', parseInt(ui.item.qty_konvers)).focus();
    // $('#received_quantity').attr('max', parseInt(1)).focus();

    // input item description autocomplete
    $.ajax({
      url: $('input[id="item_description"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="item_description"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    // input alt part number autocomplete
    $.ajax({
      url: $('input[id="alternate_part_number"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="alternate_part_number"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    // input unit autocomplete
    $.ajax({
      url: $('input[id="unit"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="unit"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });



    $('input[id="unit"]').keyup(function() {
      var unit_terima = $('input[id="unit"]').val();
      $('input[id="unit_terima"]').val(unit_terima);
    });

    $('input[id="no_expired_date"]').change(function() {
      if ($('[id="no_expired_date"]').is(':checked')) {
        $('input[id="expired_date"]').prop('readonly', true);
        $('input[id="expired_date"]').prop('required', false);
      } else {
        $('input[id="expired_date"]').prop('readonly', false);
        $('input[id="expired_date"]').prop('required', true);
      }

    });

    $('input[id="expired_date"]').change(function() {
      if ($('input[id="expired_date"]').val() != '') {
        $('input[id="no_expired_date"]').prop('disabled', true);
        $('input[id="no_expired_date"]').prop('required', false);
      } else {
        $('input[id="no_expired_date"]').prop('disabled', false);
        $('input[id="no_expired_date"]').prop('required', true);
      }

    });

    $('input[id="edit_no_expired_date"]').change(function() {
      if ($('[id="edit_no_expired_date"]').is(':checked')) {
        $('input[id="edit_expired_date"]').prop('readonly', true);
        $('input[id="edit_expired_date"]').prop('required', false);
      } else {
        $('input[id="edit_expired_date"]').prop('readonly', false);
        $('input[id="edit_expired_date"]').prop('required', true);
      }

    });

    $('input[id="edit_expired_date"]').change(function() {
      if ($('input[id="edit_expired_date"]').val() != '') {
        $('input[id="edit_no_expired_date"]').prop('disabled', true);
        $('input[id="edit_no_expired_date"]').prop('required', false);
      } else {
        $('input[id="edit_no_expired_date"]').prop('disabled', false);
        $('input[id="edit_no_expired_date"]').prop('required', true);
      }

    });

    $.ajax({
      url: $('input[id="edit_unit"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="edit_unit"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('input[id="unit_pakai"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="unit_pakai"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $('input[id="unit_pakai"]').keyup(function() {
      var unit_used = $('input[id="unit_pakai"]').val();
      $('input[id="unit_used"]').val(unit_used);
    });

    $('input[id="unit"]').keyup(function() {
      var unit = $('input[id="unit"]').val();
      $('input[id="received_unit"]').val(unit);
    });

    $.ajax({
      url: $('input[id="edit_unit_pakai"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="edit_unit_pakai"]').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    // input stores autocomplete
    $('input[id="stores"]').on('focus', function() {
      $.ajax({
        url: $('input[id="stores"]').data('source'),
        dataType: "json",
        success: function(data) {
          $('input[id="stores"]').autocomplete({
            source: function(request, response) {
              var results = $.ui.autocomplete.filter(data, request.term);
              response(results.slice(0, 10));
            }
          });
        }
      });
    });

    $('input[id="edit_stores"]').on('focus', function() {
      $.ajax({
        url: $('input[id="edit_stores"]').data('source'),
        dataType: "json",
        success: function(data) {
          $('input[id="stores"]').autocomplete({
            source: function(request, response) {
              var results = $.ui.autocomplete.filter(data, request.term);
              response(results.slice(0, 10));
            }
          });
        }
      });
    });

    // input serial number
    $('input[id="serial_number"]').on('change', function() {
      if ($(this).val() != '') {
        $('input[id="received_quantity"]').val('1').attr('readonly', false);
      } else {
        $('input[id="received_quantity"]').attr('readonly', false);
      }
    });

    //hitung qty konversi
    $('input[name="isi"]').keyup(function() {
      var isi = $(this).val();

      if (isi !== '' || isi > 0) {
        var qty = $('[name="quantity_order"]').val();
        var value = $('[name="value_order"]').val();
        var qty_konversi = parseFloat(qty) * parseFloat(isi);
        $('[name="received_quantity"]').val(qty_konversi);

        var count_received_value = parseFloat(value) / parseFloat(isi);
        var received_value = Number.parseFloat(count_received_value).toFixed(2);
        $('[name="received_unit_value"]').val(received_value);
      }
    });

    $('input[name="quantity_order"]').keyup(function() {
      var qty = $(this).val();

      if (qty !== '' || qty > 0) {
        var isi = $('[name="isi"]').val();
        var value = $('[name="value_order"]').val();
        var qty_konversi = parseFloat(qty) * parseFloat(isi);
        $('[name="received_quantity"]').val(qty_konversi);

        var count_received_value = parseFloat(value) / parseFloat(isi);
        var received_value = Number.parseFloat(count_received_value).toFixed(2);
        $('[name="received_unit_value"]').val(received_value);
      }
    });

    $('input[name="received_unit_value"]').keyup(function() {
      var received_value = $(this).val();

      if (received_value !== '' || received_value > 0) {
        var isi = $('[name="isi"]').val();
        // var value = $('[name="value_order"]').val();
        // var qty_konversi = parseFloat(qty) * parseFloat(isi);
        // $('[name="received_quantity"]').val(qty_konversi);

        var count_value_order = parseFloat(received_value) * parseFloat(isi);
        var value_order = Number.parseFloat(count_value_order).toFixed(2);
        $('[name="value_order"]').val(value_order);
      }
    });

    $('input[id="edit_isi"]').keyup(function() {
      var isi = $(this).val();

      if (isi !== '' || isi > 0) {
        var qty = $('[name="quantity_order"]').val();
        var value = $('[id="edit_value_order"]').val();
        var qty_konversi = parseFloat(qty) * parseFloat(isi);
        $('[name="received_quantity"]').val(qty_konversi);

        var count_received_value = parseFloat(value) / parseFloat(isi);
        var received_value = Number.parseFloat(count_received_value).toFixed(2);
        $('[id="edit_received_unit_value"]').val(received_value);
        console.log(value);
      }
    });

    $('input[id="edit_quantity_order"]').keyup(function() {
      var qty = $(this).val();

      if (qty !== '' || qty > 0) {
        var isi = $('[id="edit_isi"]').val();
        var value = $('[id="edit_value_order"]').val();
        var qty_konversi = parseFloat(qty) * parseFloat(isi);
        $('[name="received_quantity"]').val(qty_konversi);

        var count_received_value = parseFloat(value) / parseFloat(isi);
        var received_value = Number.parseFloat(count_received_value).toFixed(2);
        $('[name="received_unit_value"]').val(received_value);
      }
    });

    $(buttonEditDocumentItem).on('click', function(e) {
      e.preventDefault();

      //var id = $(this).data('todo').id;
      var id = $(this).data('todo').todo;
      var data_send = {
        id: id
        //i: i
      };
      var save_method;

      save_method = 'update';
      /*$('#ajax-form-create-document')[0].reset(); // reset form on modals*/


      $.ajax({
        url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
        type: "GET",
        data: data_send,
        dataType: "JSON",
        success: function(response) {
          console.log(JSON.stringify(response));
          $('[name="serial_number"]').val(response.serial_number);
          $('[name="part_number"]').val(response.part_number);
          $('[name="description"]').val(response.description);
          $('[name="alternate_part_number"]').val(response.alternate_part_number);
          $('[name="group"]').val(response.group);
          $('[name="received_quantity"]').val(response.received_quantity);
          $('[name="quantity_order"]').val(response.quantity_order);
          $('[name="minimum_quantity"]').val(response.minimum_quantity);
          $('[name="unit"]').val(response.received_unit);
          // $('[name="received_unit_value"]').val(response.received_unit_value);
          $('[name="condition"]').val(response.condition);
          $('[name="stores"]').val(response.stores);
          $('[name="expired_date"]').val(response.expired_date);
          if (response.no_expired_date == "no") {
            $('[id="edit_no_expired_date"]').attr('checked', true);
          }
          $('input[id="edit_expired_date"]').val(response.expired_date);
          $('[name="purchase_order_number"]').val(response.purchase_order_number);
          $('[name="reference_number"]').val(response.reference_number);
          $('[name="awb_number"]').val(response.awb_number);
          $('[name="remarks"]').val(response.remarks);
          $('[name="item_id"]').val(id);
          $('[name="edit_kode_akunting"]').val(response.kode_akunting);
          // $('[name="edit_kurs"]').val('rupiah');
          $('[id="edit_unit_pakai"]').val(response.unit_pakai);
          $('[id="edit_qty_konversi"]').val(response.hasil_konversi);
          // $('[id="edit_unit_terima"]').val(response.unit_pakai);
          $('[id="edit_received_unit"]').val(response.received_unit);
          $('[id="edit_isi"]').val(response.hasil_konversi);
          $('[id="edit_unit_used"]').val(response.unit_pakai);
          $('[name="edit_kode_stok"]').val(response.kode_stok);
          // if (response.isi) {
          //   $('[name="edit_isi"]').val(response.isi);
          // } else {
          //   $('[name="edit_isi"]').val(response.qty_konversi);
          // }
          $('[id="edit_isi"]').val(response.isi);
          $('[id="edit_value_order"]').val(response.value_order);
          // $('[name="edit_isi"]').val(response.isi);
          $('[name="qty_konversi"]').val(response.hasil_konversi);
          if (response.kurs_dollar > 1) {
            $('[name="edit_kurs"]').val('dollar');
            $('[name="received_unit_value"]').val(response.unit_value_dollar);

          } else {
            $('[name="edit_kurs"]').val('rupiah');
            $('[name="received_unit_value"]').val(response.received_unit_value);

          }
          if (response.kurs) {
            $('[name="edit_kurs"]').val(response.kurs);
          }
          $('[id="edit_kode_stok"]').val(response.kode_stok);
          $('[id="stock_in_store_id"]').val(response.stock_in_stores_id);
          $('[id="receipt_items_id"]').val(response.receipt_items_id);

          $('#edit_purchase_order_item_id').val(response.purchase_order_item_id);




          $('#modal-edit-item').modal('show'); // show bootstrap modal when complete loaded
          $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title

        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
      });
    });

  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>