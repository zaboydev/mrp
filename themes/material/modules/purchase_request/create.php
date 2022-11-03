<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
  <div class="section-body">
    <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document')); ?>
    <div class="card">
      <div class="card-head style-primary-dark">
        <header><?= PAGE_TITLE . " " . strtoupper($_SESSION['request']['request_to'] == 0 ? "budget control" : "mrp"); ?></header>
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
                <select name="annual_cost_center_id" id="annual_cost_center_id" class="form-control" data-source="<?= site_url($module['route'] . '/set_annual_cost_center_id'); ?>" required>
                  <option value="">--Select Department--</option>
                  <?php foreach (config_item('auth_annual_cost_centers') as $annual_cost_center) : ?>
                    <option value="<?= $annual_cost_center['id']; ?>" <?= ($_SESSION['request']['annual_cost_center_id'] == $annual_cost_center['id']) ? 'selected' : ''; ?>>
                      <?= $annual_cost_center['cost_center_name']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="source">Department</label>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <select name="head_dept_select" id="head_dept_select" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                  <option>--Select Head Dept--</option>
                  
                </select>
                <label for="notes">Head Dept.</label>
                <input type="text" name="head_dept" id="head_dept" class="form-control" value="<?= $_SESSION['request']['head_dept']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-6 col-lg-3">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="text" name="pr_number" id="pr_number" class="form-control" value="<?= $_SESSION['request']['pr_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>">
                    <label for="pr_number">Document No.</label>
                  </div>
                </div>
              </div>
              <!-- <div class="form-group">
                    <input type="text" name="pr_date" id="pr_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-end-date="0d" class="form-control" value="<?= $_SESSION['request']['pr_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_pr_date'); ?>" required>
                    <label for="required_date">Date</label>
                  </div> -->

              <div class="form-group">
                <input type="text" name="required_date" id="required_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['request']['required_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_required_date'); ?>" required>
                <label for="required_date">Target Date</label>
              </div>
            </div>

            <div class="col-sm-6 col-lg-4">
              <div class="form-group">
                <input type="text" name="suggested_supplier" id="suggested_supplier" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_suggested_supplier'); ?>" data-autocomplete="<?= site_url($module['route'] . '/get_available_vendors'); ?>" value="<?= $_SESSION['request']['suggested_supplier']; ?>" required>
                <label for="suggested_supplier">Suggested Supplier</label>
              </div>

              <div class="form-group">
                <input type="text" name="deliver_to" id="deliver_to" class="form-control" value="<?= $_SESSION['request']['deliver_to']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_deliver_to'); ?>" required>
                <label for="deliver_to">Deliver To</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="3" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['request']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['request']['items'])) : ?>
          <?php $grand_total = array(); ?>
          <?php $total_quantity = array(); ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th></th>
                  <th>Description</th>
                  <th>P/N</th>
                  <th>S/N</th>
                  <th>Additional Info</th>
                  <th class="text-right">Qty</th>
                  <th>Unit</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['request']['items'] as $i => $items) : ?>
                  <?php $grand_total[] = $items['total']; ?>
                  <?php $total_quantity[] = $items['quantity']; ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="100">
                      <a href="<?= site_url($module['route'] . '/del_item/' . $i); ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                        <i class="fa fa-trash"></i>
                      </a>
                      <a class="btn btn-icon-toggle btn-primary btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'>
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                    <td>
                      <?= print_string($items['product_name']); ?>
                    </td>
                    <td class="no-space">
                      <?= print_string($items['part_number']); ?>
                    </td>
                    <td class="no-space">
                      <?= print_string($items['serial_number']); ?>
                    </td>
                    <td class="no-space">
                      <?= print_string($items['additional_info']); ?>
                    </td>
                    <td>
                      <?= print_number($items['quantity'], 2); ?>
                    </td>
                    <td>
                      <?= print_string($items['unit']); ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <th></th>
                <th>Total</th>
                <th></th>
                <th></th>
                <th></th>
                <th><?= print_number(array_sum($total_quantity), 2); ?></th>
                <th></th>
              </tfoot>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <div class="card-actionbar">
        <div class="card-actionbar-row">
          <div class="pull-left">
            <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
              Add Item
            </a>

            <a style="margin-left: 10px;" href="<?= site_url($module['route'] . '/attachment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
              Attachment
            </a>
          </div>

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
              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-content">
                        <input type="text" id="search_budget" class="form-control" data-target="<?= site_url($module['route'] . '/search_budget/'); ?>">
                        <label for="search_budget">Search item Budgeted</label>
                      </div>
                      <span class="input-group-addon">
                        <i class="md md-search"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-6 hide">
              <div class="row">
                <div class="col-xs-12">
                  <div class="form-group">
                    <div class="input-group">
                      <div class="input-group-content">
                        <input type="text" id="search_item_unbudgeted" class="form-control" data-target="<?= site_url($module['route'] . '/search_item_unbudgeted/'); ?>">
                        <label for="search_item_unbudgeted">Search item Unbudgeted</label>
                      </div>
                      <span class="input-group-addon">
                        <i class="md md-search"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="row">
            <div class="col-sm-12 col-lg-7">
              <div class="row">
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                      <input type="text" name="part_number" id="part_number" class="form-control input-sm" data-source="<?= site_url($module['route'] . '/search_items_by_part_number/'); ?>">
                      <label for="part_number">Part Number For Unbudgeted</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="serial_number" id="serial_number" class="form-control input-sm" data-source="<?= site_url($module['route'] . '/search_items_by_serial_number/'); ?>">
                      <label for="serial_number">Serial Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="product_name" id="product_name" class="form-control input-sm" data-source="<?= site_url($module['route'] . '/search_items_by_product_name/'); ?>">
                      <label for="product_name">Description</label>
                    </div>

                    <div class="form-group">
                      <select name="group_name" id="group_name" data-tag-name="group_name" class="form-control input-sm" required>
                        <option>-- Select One --</option>
                        <?php foreach (available_item_groups_2($_SESSION['request']['category']) as $group) : ?>
                          <option value="<?= $group['group']; ?>">
                            <?= $group['coa']; ?> - <?= $group['group']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label for="group_name">Group</label>
                    </div>

                    <div class="form-group">
                      <!-- <input type="text" name="unit" id="unit" class="form-control input-sm"> -->
                      <input type="text" name="unit" id="unit" data-tag-name="unit" data-search-for="unit" data-source="<?= site_url($modules['ajax']['route'] . '/search_item_units/'); ?>" class="form-control input-sm" placeholder="Unit" required>
                      <label for="unit">Unit of Measurement</label>
                    </div>
                  </fieldset>
                </div>
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>Required</legend>

                    <div class="form-group">
                      <input type="text" name="quantity" id="quantity" class="form-control input-sm" value="1" required>
                      <label for="quantity">Quantity</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="maximum_quantity" id="maximum_quantity" class="form-control input-sm" value="0" readonly>
                      <label for="maximum_quantity">Max. Quantity</label>

                      <input type="hidden" name="price" id="price" value="1">
                      <input type="hidden" name="total" id="total" value="0">

                      <input type="hidden" name="ytd_quantity" id="ytd_quantity" value="0">
                      <input type="hidden" name="ytd_used_quantity" id="total" value="0">
                      <input type="hidden" name="ytd_budget" id="price" value="0">
                      <input type="hidden" name="ytd_used_budget" id="total" value="0">

                      <input type="hidden" name="mtd_quantity" id="mtd_quantity" value="0">
                      <input type="hidden" name="mtd_used_quantity" id="mtd_used_quantity" value="0">
                      <input type="hidden" name="mtd_budget" id="mtd_budget" value="0">
                      <input type="hidden" name="mtd_used_budget" id="mtd_used_budget" value="0">
                    </div>

                    <div class="form-group hide">
                      <input type="number" name="maximum_price" id="maximum_price" value="0" class="form-control input-sm" readonly="readonly">
                      <label for="max_value">Max. Value</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="on_hand_quantity" id="on_hand_quantity" class="form-control input-sm" value="0" readonly>
                      <label for="on_hand_quantity">On Hand Quantity</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="minimum_quantity" id="minimum_quantity" class="form-control input-sm" value="1">
                      <label for="minimum_quantity">Min. Quantity</label>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <fieldset>
                <legend>Optional</legend>

                <div class="form-group">
                  <textarea name="additional_info" id="additional_info" data-tag-name="additional_info" class="form-control input-sm"></textarea>
                  <label for="additional_info">Additional Info/Remarks</label>
                </div>
                <div class="form-group">
                  <input type="text" name="reference_ipc" id="reference_ipc" class="form-control input-sm">
                  <label for="reference_ipc">Reference IPC</label>
                </div>
              </fieldset>
            </div>
            <div class="col-sm-12 col-lg-5 hide form-unbudgeted">
              <fieldset>
                <legend>Unbudgeted</legend>

                <div class="form-group">
                  <input name="xx" id="xx" data-tag-name="xx" class="form-control input-sm" value="Unbudgeted" readonly="readonly"></input>
                  <label for="additional_info">Unbudgeted</label>
                </div>
              </fieldset>
            </div>
            <div class="col-sm-12 col-lg-5 hide form-relokasi">
              <fieldset>
                <legend>Relocation Form</legend>

                <div class="form-group">
                  <input name="origin_budget" id="origin_budget" data-tag-name="origin_budget" class="form-control input-sm"></input>
                  <label for="additional_info">Origin Budget</label>
                </div>
                <div class="form-group">
                  <input name="budget_value" id="budget_value" data-tag-name="budget_value" class="form-control input-sm" readonly="readonly"></input>
                  <label for="budget_value">Budget Value</label>
                </div>
                <div class="form-group">
                  <input name="relocation_budget" id="relocation_budget" data-tag-name="relocation_budget" class="form-control input-sm" readonly="readonly"></input>
                  <input type="hidden" name="need_budget" id="need_budget" data-tag-name="need_budget" class="form-control input-sm"></input>
                  <label for="additional_info">Relocation Value</label>
                </div>
              </fieldset>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <input type="hidden" id="inventory_monthly_budget_id" name="inventory_monthly_budget_id">
          <input type="hidden" id="unbudgeted_item" name="unbudgeted_item">
          <input type="hidden" id="relocation_item" name="relocation_item">
          <input type="hidden" id="item_source" name="item_source">
          <button type="button" class="btn btn-flat btn-default" data-dismiss="modal">Close</button>

          <button type="submit" id="modal-add-item-submit" class="btn btn-primary btn-create ink-reaction">
            Add Item
          </button>

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

  function popup(mylink, windowname) {
    var height = window.innerHeight;
    var widht;
    var href;

    if (screen.availWidth > 768) {
      width = 769;
    } else {
      width = screen.availWidth;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    var top = 0;
    // var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

    if (!window.focus) return true;
    else return false;
  }

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

    // var today       = new Date();
    var today = $('[name="required_date"]').val();
    var today_2 = $('[name="pr_date"]').val();
    // today.setDate(today.getDate() + 30);
    $('#required_date').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
      startDate: today,
    });

    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
      startDate: today_2,
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
      if (confirm('Are you sure want to save this request and sending email? Continue?')) {
        $.post(url, formDocument.serialize(), function(data) {
          console.log(data);
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


      $.ajax({
        url: "<?= site_url($module['route'] . '/ajax_editItem/') ?>/" + id,
        type: "GET",
        data: data_send,
        dataType: "JSON",
        success: function(response) {
          var action = "<?= site_url($module['route'] . '/edit_item/') ?>/" + id;
          console.log(JSON.stringify(action));
          console.log(JSON.stringify(response));
          // var maximum_quantity  = parseInt(response.ytd_quantity) - parseInt(response.ytd_used_quantity);
          // var maximum_price     = parseInt(response.ytd_budget) - parseInt(response.ytd_used_budget);

          var maximum_quantity = parseInt(response.mtd_quantity) - parseInt(response.mtd_used_quantity);
          var maximum_price = parseInt(response.mtd_budget) - parseInt(response.mtd_used_budget);


          $('#product_name').val(response.product_name);
          $('#part_number').val(response.part_number);
          $('#serial_number').val(response.serial_number);
          $('#group_name').val(response.group_name);
          $('#unit').val(response.unit);
          $('#additional_info').val(response.additional_info);
          $('#reference_ipc').val(response.reference_ipc);
          $('#inventory_monthly_budget_id').val(response.inventory_monthly_budget_id);
          $('#item_source').val(response.source);
          $('#price').val(parseInt(response.price));
          $('#quantity').val(parseInt(response.quantity));
          $('#maximum_price').val(maximum_price);
          $('#maximum_quantity').val(maximum_quantity);
          $('#total').val(parseInt(response.price)).trigger('change');

          $('#ytd_quantity').val(response.ytd_quantity);
          $('#ytd_used_quantity').val(parseInt(response.ytd_used_quantity));
          $('#ytd_used_budget').val(parseInt(response.ytd_used_budget));
          $('#ytd_budget').val(response.ytd_budget);

          $('#relocation_item').val(response.relocation_item);
          $('#need_budget').val(response.need_budget);
          $('#relocation_budget').val(response.need_budget);

          $('#origin_budget').val(response.part_number_relocation);
          $('#budget_value').val(response.budget_value_relocation);

          if (response.relocation_item != '') {
            $('.form-relokasi').removeClass('hide');
          }

          // var status = $('#inventory_monthly_budget_id').val();

          // if(status==null){
          //   $('.form-unbudgeted').removeClass('hide');
          // }

          $('#mtd_quantity').val(response.mtd_quantity);
          $('#mtd_used_quantity').val(parseInt(response.mtd_used_quantity));
          $('#mtd_used_budget').val(parseInt(response.mtd_used_budget));
          $('#mtd_budget').val(response.mtd_budget);


          // $('input[id="quantity"]').attr('data-rule-max', maximum_quantity).attr('data-msg-max', 'max available '+ maximum_quantity);

          // $('input[id="price"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

          // $('input[id="total"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

          // $('#quantity').attr('max', maximum_quantity).focus();

          $('#modal-add-item form').attr('action', action);
          $('#modal-add-item').modal('show'); // show bootstrap modal when complete loaded
          $('.modal-title').text('Edit Item'); // Set title to Bootstrap modal title

        },
        error: function(jqXHR, textStatus, errorThrown) {
          alert('Error get data from ajax');
        }
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

    $.ajax({
      url: $('#suggested_supplier').data('autocomplete'),
      dataType: "json",
      success: function(data) {
        $('#suggested_supplier').autocomplete({
          source: function(request, response) {
            var results = $.ui.autocomplete.filter(data, request.term);
            response(results.slice(0, 10));
          }
        });
      }
    });

    $.ajax({
      url: $('#search_budget').data('target'),
      dataType: "json",
      error: function(xhr, response, results) {
        console.log(xhr.responseText);
      },
      success: function(resource) {
        $('#search_budget').autocomplete({
            autoFocus: true,
            minLength: 1,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
              console.log(results);
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              // var maximum_quantity  = parseInt(ui.item.ytd_quantity) - parseInt(ui.item.ytd_used_quantity);
              // var maximum_price     = parseInt(ui.item.ytd_budget) - parseInt(ui.item.ytd_used_budget);
              var maximum_quantity = parseInt(ui.item.mtd_quantity) - parseInt(ui.item.mtd_used_quantity);
              var maximum_price = parseInt(ui.item.mtd_budget) - parseInt(ui.item.mtd_used_budget);

              $('#product_name').val(ui.item.product_name);
              $('#part_number').val(ui.item.product_code);
              $('#group_name').val(ui.item.group_name);
              $('#unit').val(ui.item.measurement_symbol);
              $('#additional_info').val(ui.item.additional_info);
              $('#inventory_monthly_budget_id').val(ui.item.id);
              $('#item_source').val(ui.item.source);
              $('#price').val(parseInt(ui.item.price));
              $('#maximum_price').val(maximum_price);
              $('#maximum_quantity').val(maximum_quantity);
              $('#total').val(parseInt(ui.item.price)).trigger('change');

              $('#ytd_quantity').val(ui.item.ytd_quantity);
              $('#ytd_used_quantity').val(parseInt(ui.item.ytd_used_quantity));
              $('#ytd_used_budget').val(parseInt(ui.item.ytd_used_budget));
              $('#ytd_budget').val(ui.item.ytd_budget);

              $('#mtd_quantity').val(ui.item.mtd_quantity);
              $('#mtd_used_quantity').val(parseInt(ui.item.mtd_used_quantity));
              $('#mtd_used_budget').val(parseInt(ui.item.mtd_used_budget));
              $('#mtd_budget').val(ui.item.mtd_budget);
              $('#on_hand_quantity').val(parseInt(ui.item.on_hand_quantity));
              $('#minimum_quantity').val(ui.item.minimum_quantity);



              $('#unbudgeted_item').val(0);

              // $('input[id="quantity"]').attr('data-rule-max', maximum_quantity).attr('data-msg-max', 'max available '+ maximum_quantity);

              // $('input[id="price"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

              // $('input[id="total"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

              // $('#quantity').attr('max', maximum_quantity).focus();
              // $('#total').attr('max', maximum_price);

              $('#search_budget').val('');

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
      // url: $('#origin_budget').data('target'),
      url: "<?= site_url($module['route'] . '/search_budget_for_relocation/'); ?>",
      dataType: "json",
      error: function(xhr, response, results) {
        console.log(xhr.responseText);
      },
      success: function(resource) {
        $('#origin_budget').autocomplete({
            autoFocus: true,
            minLength: 1,

            source: function(request, response) {
              var results = $.ui.autocomplete.filter(resource, request.term);
              response(results.slice(0, 5));
              console.log(results);
            },

            focus: function(event, ui) {
              return false;
            },

            select: function(event, ui) {
              // var maximum_quantity  = parseInt(ui.item.ytd_quantity) - parseInt(ui.item.ytd_used_quantity);
              // var maximum_price     = parseInt(ui.item.ytd_budget) - parseInt(ui.item.ytd_used_budget);
              var maximum_quantity = parseInt(ui.item.mtd_quantity) - parseInt(ui.item.mtd_used_quantity);
              var maximum_price = parseInt(ui.item.mtd_budget) - parseInt(ui.item.mtd_used_budget);
              var total = $('#total').val();
              var budget = $('#maximum_price').val();
              var need_budget = parseFloat(total) - parseFloat(budget);

              $('#budget_value').val(ui.item.maximum_price);
              $('#need_budget').val(need_budget);
              // $('#part_number').val( ui.item.product_code );
              // $('#group_name').val( ui.item.group_name );
              // $('#unit').val( ui.item.measurement_symbol );
              // $('#additional_info').val( ui.item.additional_info );
              $('#relocation_item').val(ui.item.id);
              // $('#item_source').val(ui.item.source);
              // $('#price').val( parseInt(ui.item.price) );
              // $('#maximum_price').val( maximum_price );
              // $('#maximum_quantity').val( maximum_quantity );
              // $('#total').val( parseInt(ui.item.price) ).trigger('change');

              // $('#ytd_quantity').val(ui.item.ytd_quantity);
              // $('#ytd_used_quantity').val( parseInt(ui.item.ytd_used_quantity) );
              // $('#ytd_used_budget').val( parseInt(ui.item.ytd_used_budget) );
              // $('#ytd_budget').val(ui.item.ytd_budget);
              $('#relocation_budget').val(need_budget);

              // $('input[id="relocation_budget"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max available '+ maximum_price);
              // $('input[id="relocation_budget"]').attr('data-rule-min', need_budget).attr('data-msg-min', 'min relocation '+ need_budget);

              // $('input[id="price"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

              // $('input[id="total"]').attr('data-rule-max', maximum_price).attr('data-msg-max', 'max allowed '+ maximum_price);

              $('#relocation_budget').attr('max', maximum_price).focus();
              // $('#total').attr('max', maximum_price);

              $('#origin_budget').val(ui.item.product_code);
              if (parseFloat($("#budget_value").val()) < parseFloat($("#need_budget").val())) {
                $("#modal-add-item-submit").prop("disabled", true);

              } else {
                $("#modal-add-item-submit").prop("disabled", false);
              }

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
        console.log(resource);
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
              $('input[id="serial_number"]').val(ui.item.serial_number);
              $('input[id="additional_info"]').val('Alternate P/N: ' + ui.item.alternate_part_number);
              $('input[id="product_name"]').val(ui.item.description);
              $('select[id="group_name"]').val(ui.item.group);
              $('input[id="unit"]').val(ui.item.unit);
              $('input[id="quantity"]').val();
              $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
              $('input[id="on_hand_quantity"]').val(ui.item.on_hand_quantity);
              $('input[id="price"]').val(ui.item.price);
              $('input[id="total"]').val(ui.item.price).trigger('change');

              $('#maximum_price').val(0);
              $('#maximum_quantity').val(0);

              $('#ytd_quantity').val(0);
              $('#ytd_used_quantity').val(parseInt(0));
              $('#ytd_used_budget').val(parseInt(0));
              $('#ytd_budget').val(0);

              $('#mtd_quantity').val(0);
              $('#mtd_used_quantity').val(parseInt(0));
              $('#mtd_used_budget').val(parseInt(0));
              $('#mtd_budget').val(0);

              $('#inventory_monthly_budget_id').val('');

              $('input[id="quantity"]').focus();

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
      url: $('input[id="serial_number"]').data('source'),
      dataType: "json",
      success: function(resource) {
        console.log(resource);
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
              $('input[id="part_number"]').val(ui.item.part_number);
              $('input[id="serial_number"]').val(ui.item.serial_number);
              $('input[id="additional_info"]').val('Alternate P/N: ' + ui.item.alternate_part_number);
              $('input[id="product_name"]').val(ui.item.description);
              $('select[id="group_name"]').val(ui.item.group);
              $('input[id="unit"]').val(ui.item.unit);
              $('input[id="quantity"]').val();
              $('input[id="minimum_quantity"]').val(ui.item.minimum_quantity);
              $('input[id="on_hand_quantity"]').val(ui.item.on_hand_quantity);
              $('input[id="price"]').val(ui.item.price);
              $('input[id="total"]').val(ui.item.price).trigger('change');

              $('#maximum_price').val(0);
              $('#maximum_quantity').val(0);

              $('#ytd_quantity').val(0);
              $('#ytd_used_quantity').val(parseInt(0));
              $('#ytd_used_budget').val(parseInt(0));
              $('#ytd_budget').val(0);

              $('#mtd_quantity').val(0);
              $('#mtd_used_quantity').val(parseInt(0));
              $('#mtd_used_budget').val(parseInt(0));
              $('#mtd_budget').val(0);

              $('#inventory_monthly_budget_id').val('');

              $('input[id="quantity"]').focus();

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
      url: $('input[id="product_name"]').data('source'),
      dataType: "json",
      success: function(data) {
        $('input[id="product_name"]').autocomplete({
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

    // $.ajax({
    //   url: $( 'input[id="group_name"]' ).data('source'),
    //   dataType: "json",
    //   success: function (data) {
    //     $( 'input[id="group_name"]' ).autocomplete({
    //       source: function (request, response) {
    //         var results = $.ui.autocomplete.filter(data, request.term);
    //         response(results.slice(0, 10));
    //       }
    //     });
    //   }
    // });

    $("#quantity").on("keydown keyup", function() {
      var max_price = parseInt($("#maximum_price").val()) / parseInt($("#quantity").val());

      if (parseInt($("#price").val()) > max_price && max_price > 0) {
        // $("#price").val(max_price);
        $('.form-relokasi').removeClass('hide');
        $('#budget_value').attr('required', true);
        $('#need_budget').attr('required', true);
        $('#relocation_item').attr('required', true);
        $('#relocation_budget').attr('required', true);
        $('#origin_budget').attr('required', true);
      } else {
        $('.form-relokasi').addClass('hide');
        $('#relocation_item').val('');
        $('#budget_value').val('');
        $('#need_budget').val('');
        $('#relocation_item').val('');
        $('#relocation_budget').val('');
        $('#origin_budget').val('');

        $('#budget_value').attr('required', false);
        $('#need_budget').attr('required', false);
        $('#relocation_item').attr('required', false);
        $('#relocation_budget').attr('required', false);
        $('#origin_budget').attr('required', false);
      }
      if (parseInt($("#quantity").val() > 0)) {
        $("#modal-add-item-submit").prop("disabled", false);
      }


      sum();
    });

    $("#price").on("keydown keyup", sum);

    $("#total").on("change", function() {
      if (parseInt($(this).val()) > parseInt($("#maximum_price").val())) {

        // $("#modal-add-item-submit").prop("disabled", true);

        // $("#price").closest("div").addClass("has-error").append('<p class="help-block total-error">Not allowed!</p>').focus();

        // toastr.options.timeOut = 10000;
        // toastr.options.positionClass = 'toast-top-right';
        // toastr.error('Price or total price is over maximum price allowed! You can not add this item.');
      } else {
        console.log(321)
        $("#price").closest("div").removeClass("has-error");
        $(".total-error").remove();
        $("#modal-add-item-submit").prop("disabled", false);
      }
    });

    $("#relocation_budget").on("change", function() {
      var budget_value = $("#budget_value").val();
      if (parseFloat($(this).val()) > parseFloat($("#budget_value").val())) {

        // $("#modal-add-item-submit").prop("disabled", true);

        $("#relocation_budget").closest("div").addClass("has-error").append('<p class="help-block total-error">Not allowed! max available ' + budget_value + '</p>').focus();
        $(this).val(budget_value);
        $(this).focus();
        // toastr.options.timeOut = 10000;
        // toastr.options.positionClass = 'toast-top-right';
        // toastr.error('Price or total price is over maximum price allowed! You can not add this item.');
      } else {
        console.log(321)
        $("#relocation_budget").closest("div").removeClass("has-error");
        // $(".total-error").remove();
        $("#modal-add-item-submit").prop("disabled", false);
      }
    });

    $('#annual_cost_center_id').on('change', function() {
      var prev = $(this).data('val');
      var val = $(this).val();
      var url = $(this).data('source');

      if (prev != ''){
        var conf = confirm("You have changing Department. Continue?");

        if (conf == false){
          return false;
        }
      }

      window.location.href = url + '/' + val;

    });

    $('#head_dept_select').on('change', function() {
      var val = $(this).val();
      var url = $(this).data('source');

      $.get(url, {
        data: val
      });
      $('#head_dept').val(val).trigger('change');

    });

    function get_head_dept_user() {
      $('#head_dept').html('');

      var head_dept = $('#head_dept').val();

      $.ajax({
        url: "<?= site_url($module['route'] . '/get_head_dept_user'); ?>",
        dataType: "json",
        success: function(resource) {
          console.log(resource);
          $('#head_dept_select').html('');
          $("#head_dept_select").append('<option value="">--Select Head Dept--</option>');
          $.each(resource, function(i, item) {
            if(head_dept==item.username){
              var text = '<option value="' +item.username+'" selected>' +item.person_name+'</option>';
            }else{
              var text = '<option value="' +item.username+'">' +item.person_name+'</option>';
            }            
            $("#head_dept_select").append(text);
          });
          
        }
      });
    }

    get_head_dept_user()
  });

  function sum() {
    var total = parseInt($("#quantity").val()) * parseInt($("#price").val());

    $("#total").val(total).trigger("change");
  }

  function unbudgeted() {
    var status = $('#inventory_monthly_budget_id').val();

    if (status == null) {
      $('.form-unbudgeted').removeClass('hide');
    }
  }
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>