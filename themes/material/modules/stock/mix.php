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
            <div class="col-sm-6">
              <div class="form-group">
                <input type="text" name="date" id="date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['mix']['mixed_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_mixed_date'); ?>" required>
                <label for="date">Date</label>
              </div>

              <div class="form-group">
                <input type="text" name="mixing_category" id="mixing_category" class="form-control" value="<?= $_SESSION['mix']['category']; ?>" disabled>
                <label for="mixing_category">Inventory</label>
              </div>

              <div class="form-group">
                <input type="text" name="mixing_group" id="mixing_group" class="form-control" value="<?= $_SESSION['mix']['group']; ?>" disabled>
                <label for="mixing_group">Group</label>
              </div>

              <div class="form-group">
                <input type="text" name="mixing_part_number" id="mixing_part_number" class="form-control" value="<?= $_SESSION['mix']['part_number']; ?>" disabled>
                <label for="mixing_part_number">Part Number</label>
              </div>

              <div class="form-group">
                <input type="text" name="mixing_description" id="mixing_description" class="form-control" value="<?= htmlspecialchars($_SESSION['mix']['description']); ?>" disabled>
                <label for="mixing_description">Description</label>
              </div>

              <div class="form-group">
                <input type="text" name="mixing_serial_number" id="mixing_serial_number" class="form-control" value="<?= $_SESSION['mix']['serial_number']; ?>" disabled>
                <label for="mixing_serial_number">Serial Number</label>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="number" name="mixing_quantity" id="mixing_quantity" class="form-control" value="<?= $_SESSION['mix']['mixing_quantity']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_mixing_quantity'); ?>" required>
                    <label for="mixing_quantity">Mixing Quantity</label>
                  </div>
                  <span class="input-group-addon"><?= $_SESSION['mix']['unit']; ?></span>
                </div>
              </div>

              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-content">
                    <input type="number" name="mixing_minimum_quantity" id="mixing_minimum_quantity" class="form-control" value="<?= $_SESSION['mix']['minimum_quantity']; ?>" disabled>
                    <label for="mixing_minimum_quantity">Minimum Quantity</label>
                  </div>
                  <span class="input-group-addon"><?= $_SESSION['mix']['unit']; ?></span>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="mixing_stores" id="mixing_stores" class="form-control" value="<?= $_SESSION['mix']['stores']; ?>" disabled>
                <label for="mixing_stores">Stores</label>
              </div>

              <div class="form-group">
                <textarea name="remarks" id="remarks" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_remarks'); ?>"><?= $_SESSION['mix']['remarks']; ?></textarea>
                <label for="remarks">Remarks</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['mix']['mixed_items'])) : ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th></th>
                  <th>Description</th>
                  <th>P/N</th>
                  <th>S/N</th>
                  <th>Condition</th>
                  <th>Stores</th>
                  <th>Unit</th>
                  <th>Qty</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['mix']['mixed_items'] as $i => $items) : ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="1">
                      <a href="<?= site_url($module['route'] . '/mix_del_item/' . $i); ?>" data-qty="<?= $items['mixed_quantity']; ?>" class="btn btn-icon-toggle btn-danger btn-sm btn_delete_document_item">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                    <td>
                      <?= $items['description']; ?>
                    </td>
                    <td class="no-space">
                      <?= $items['part_number']; ?>
                    </td>
                    <td>
                      <?= $items['serial_number']; ?>
                    </td>
                    <td>
                      <?= $items['condition']; ?>
                    </td>
                    <td>
                      <?= $items['stores']; ?>
                    </td>
                    <td>
                      <?= $items['unit']; ?>
                    </td>
                    <td>
                      <?= number_format($items['mixed_quantity'], 2); ?>
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
          <a href="#modal-add-item" data-toggle="modal" data-target="#modal-add-item" class="btn btn-primary ink-reaction btn-open-offcanvas pull-left">
            Add Item
          </a>

          <a href="<?= site_url($module['route'] . '/mix_discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
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

        <?= form_open(site_url($module['route'] . '/mix_add_item'), array(
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
                    <input type="text" id="search_stock_in_stores" class="form-control" data-target="<?= site_url($module['route'] . '/search_stock_in_stores/'); ?>">
                    <label for="search_stock_in_stores">Search item by S/N, P/N, Description</label>
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
                <div class="col-sm-6 col-lg-8">
                  <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                      <input type="text" name="serial_number" id="serial_number" class="form-control input-sm" readonly>
                      <label for="serial_number">Serial Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="part_number" id="part_number" class="form-control input-sm" readonly>
                      <label for="part_number">Part Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="description" id="description" class="form-control input-sm" readonly>
                      <label for="description">Description</label>
                    </div>
                  </fieldset>
                </div>
                <div class="col-sm-6 col-lg-4">
                  <fieldset>
                    <legend>Storage</legend>

                    <div class="form-group">
                      <input type="text" name="stores" id="stores" class="form-control input-sm" readonly>
                      <label for="stores">Stores</label>
                    </div>

                    <div class="form-group">
                      <div class="input-group">
                        <div class="input-group-content">
                          <input type="text" name="minimum_quantity" id="minimum_quantity" class="form-control input-sm" value="0" readonly>
                          <label for="minimum_quantity">Minimum Quantity</label>
                        </div>
                        <span class="input-group-addon unit-addon"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <input type="text" name="condition" id="condition" class="form-control input-sm" readonly>
                      <label for="condition">Condition</label>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>

            <div class="col-sm-12 col-lg-4">
              <fieldset>
                <legend>Mixed</legend>

                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-content">
                      <input type="text" name="mixed_quantity" id="mixed_quantity" data-tag-name="mixed_quantity" class="form-control input-sm" value="1" required>
                      <label for="mixed_quantity">Quantity</label>
                    </div>
                    <span class="input-group-addon unit-addon"></span>
                  </div>
                </div>

                <input type="hidden" id="unit" name="unit">
                <input type="hidden" id="group" name="group">
                <input type="hidden" id="mixed_unit_value" name="mixed_unit_value" value="0">
                <input type="hidden" name="stock_in_stores_id" id="stock_in_stores_id" />
              </fieldset>
            </div>
          </div>
        </div>

        <div class="modal-footer">
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
      <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?= site_url($module['route'] . '/mix_save'); ?>">
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

    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd'
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
      var qty = $(this).data('qty');
      var tot = $('#mixing_quantity').val();

      $.get(url);

      $(tr).remove();
      $('#mixing_quantity').val(tot - qty);

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

    $('#search_stock_in_stores').on('click focus', function() {
      $.ajax({
        url: $('#search_stock_in_stores').data('target'),
        dataType: "json",
        success: function(resource) {
          $('#search_stock_in_stores').autocomplete({
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
                $('#part_number').val(ui.item.part_number);
                $('#serial_number').val(ui.item.serial_number);
                $('#description').val(ui.item.description);
                $('#alternate_part_number').val(ui.item.alternate_part_number);
                $('#group').val(ui.item.group);
                $('#mixed_quantity').val(ui.item.quantity);
                $('#condition').val(ui.item.condition);
                $('#stores').val(ui.item.stores);
                $('#stock_in_stores_id').val(ui.item.id);
                $('#mixed_unit_value').val(ui.item.unit_value);

                $('input[id="mixed_quantity"]').attr('data-rule-max', parseInt(ui.item.quantity)).attr('data-msg-max', 'max available ' + ui.item.quantity);

                $('#mixed_quantity').attr('max', ui.item.quantity).focus();
                $('#unit').val(ui.item.unit);
                $('.unit-addon').text(ui.item.unit);

                $('#search_stock_in_stores').val('');

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
  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>