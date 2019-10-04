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

                    <input type="text" name="document_number" id="document_number" class="form-control" maxlength="6" value="<?= $_SESSION['jurnal_usage']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" required>
                    <label for="document_number">Inventory Journal Number</label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <input type="text" name="received_date" id="received_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control" value="<?= $_SESSION['jurnal_usage']['date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_received_date'); ?>" required>
                <input type="hidden" name="opname_start_date" id="opname_start_date" data-date-format="yyyy-mm-dd" class="form-control" value="<?= last_publish_date(); ?>" readonly>
                <label for="received_date">Date</label>
              </div>
            </div>

            <div class="col-sm-12 col-lg-5">
              <div class="form-group">
                <textarea name="notes" id="notes" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_notes'); ?>"><?= $_SESSION['jurnal_usage']['notes']; ?></textarea>
                <label for="notes">Notes</label>
              </div>
            </div>
          </div>
        </div>

        <?php if (isset($_SESSION['jurnal_usage']['items'])) : ?>
          <div class="document-data table-responsive">
            <table class="table table-hover" id="table-document">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Description</th>
                  <th>P/N</th>
                  <th>S/N</th>
                  <th>Location</th>
                  <th>Qty</th>
                  <th>Unit Value</th>
                  <th>Amount</th>
                  <th>Account</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($_SESSION['jurnal_usage']['items'] as $i => $detail) : ?>
                  <tr id="row_<?= $i; ?>">
                    <td width="1">
                      <a class="btn btn-icon-toggle btn-info btn-sm btn_edit_document_item" data-todo='{"todo":<?= $i; ?>}'>
                        <i class="fa fa-edit"></i>
                      </a>
                    </td>
                    <td>
                      <?= $detail['description']; ?>
                    </td>
                    <td>
                      <?= $detail['part_number']; ?>
                    </td>
                    <td>
                      <?= $detail['serial_number']; ?>
                    </td>
                    <td>
                      <?= $detail['warehouse']; ?>
                    </td>
                    <td>
                      <?= $detail['trs_kredit'] / $detail['unit_value']; ?>
                    </td>
                    <td>
                      <?= $detail['unit_value']; ?>
                    </td>
                    <td>
                      <?= $detail['trs_kredit']; ?>
                    </td>
                    <td>
                      <?= $detail['kode_pemakaian']; ?>
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
          <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
            Discard
          </a>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
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
            <div class="col-sm-12 col-lg-8">
              <div class="row">
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                      <input type="text" name="serial_number" id="edit_serial_number" class="form-control input-sm input-autocomplete" readonly>
                      <label for="serial_number">Serial Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="part_number" id="edit_part_number" class="form-control input-sm input-autocomplete" readonly>
                      <label for="part_number">Part Number</label>
                    </div>

                    <div class="form-group">
                      <input type="text" name="description" id="edit_description" data-tag-name="item_description" data-search-for="item_description" class="form-control input-sm" readonly>
                      <label for="description">Description</label>
                    </div>
                    <input type="text" name="item_id" id="item_id">
                    <input type="text" name="currency" id="currency">
                    <input type="text" name="id_jurnal_detail" id="id_jurnal_detail">
                    <input type="text" name="stock_in_stores_id" id="stock_in_stores_id">
                    <input type="text" name="id_jurnal" id="id_jurnal" data-tag-name="id_jurnal" data-search-for="id_jurnal" class="form-control input-sm" readonly>
                    <input type="text" name="trs_kredit" id="trs_kredit" data-tag-name="trs_kredit" data-search-for="trs_kredit" class="form-control input-sm" readonly>
                    <input type="text" name="trs_kredit_usd" id="trs_kredit_usd" data-tag-name="trs_kredit_usd" data-search-for="trs_kredit_usd" class="form-control input-sm" readonly>
                    <input type="text" name="unit_value" id="unit_value" data-tag-name="unit_value" data-search-for="unit_value" class="form-control input-sm" readonly>
                    <input type="text" name="stores" id="stores" data-tag-name="stores" data-search-for="stores" class="form-control input-sm" readonly>
                    <input type="text" name="warehouse" id="warehouse" data-tag-name="warehouse" data-search-for="warehouse" class="form-control input-sm" readonly>
                    <input type="text" name="coa" id="coa" data-tag-name="coa" data-search-for="coa" class="form-control input-sm" readonly>
                    <input type="text" name="group" id="group" data-tag-name="group" data-search-for="group" class="form-control input-sm" readonly>
                  </fieldset>
                </div>
                <div class="col-sm-6 col-lg-6">
                  <fieldset>
                    <legend>Account</legend>
                    <div class="form-group">
                      <select name="kode_pemakaian" id="kode_pemakaian" data-tag-name="kode_pemakaian" class="form-control input-sm" required>
                        <option>-- Select One --</option>
                        <?php foreach (available_item_groups_2($_SESSION['receipt']['category']) as $group) : ?>
                          <option value="<?= $group['coa']; ?>">
                            <?= $group['coa']; ?> - <?= $group['group']; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <label for="kode_pemakaian">Account</label>
                    </div>
                  </fieldset>
                </div>
              </div>
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
                $('#received_quantity').val(parseInt(ui.item.left_received_quantity));
                $('#unit').val(ui.item.unit);
                $('#unit_pakai').val(ui.item.unit);
                $('#received_unit_value').val(parseInt(unit_value));
                $('#purchase_order_item_id').val(ui.item.id);
                $('#purchase_order_number').val(ui.item.document_number);
                $('#kode_stok').val(ui.item.kode_stok);
                if (ui.item.default_currency == 'USD') {
                  $('[name="kurs"]').val('dollar');

                } else {
                  $('[name="kurs"]').val('rupiah');

                }

                $('#received_quantity').data('rule-max', parseInt(ui.item.quantity)).data('msg-max', 'max available ' + ui.item.quantity);

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
        var qty = $('[name="received_quantity"]').val();
        var qty_konversi = parseInt(qty) * parseInt(isi);
        $('[name="qty_konversi"]').val(qty_konversi);
      }
    });

    $('input[name="received_quantity"]').keyup(function() {
      var qty = $(this).val();

      if (qty !== '' || qty > 0) {
        var isi = $('[name="isi"]').val();
        var qty_konversi = parseInt(qty) * parseInt(isi);
        $('[name="qty_konversi"]').val(qty_konversi);
      }
    });

    $('input[name="edit_isi"]').keyup(function() {
      var isi = $(this).val();

      if (isi !== '' || isi > 0) {
        var qty = $('[name="received_quantity"]').val();
        var qty_konversi = parseInt(qty) * parseInt(isi);
        $('[name="edit_qty_konversi"]').val(qty_konversi);
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
          $('[name="group"]').val(response.group);
          $('[name="stores"]').val(response.stores);
          $('[name="trs_kredit"]').val(response.trs_kredit);
          $('[name="trs_kredit_usd"]').val(response.trs_kredit_usd);
          $('[name="warehouse"]').val(response.warehouse);
          $('[name="kode_pemakaian"]').val(response.kode_pemakaian);
          $('[name="coa"]').val(response.coa);
          $('[name="unit_value"]').val(response.unit_value);
          $('[name="id_jurnal"]').val(response.id_jurnal);
          $('[name="item_id"]').val(id);
          $('[name="currency"]').val(response.currency);
          $('[name="stock_in_stores_id"]').val(response.stock_in_stores_id);
          $('[name="id_jurnal_detail"]').val(response.id_jurnal_detail);
          $('[name="kode_akun_lawan"]').val(response.kode_akun_lawan);

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