<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
<?php $this->load->view('material/templates/datatable') ?>
<div id="import-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="import-modal-label" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <?= form_open_multipart(site_url($module['route'] . '/import'), array('autocomplete' => 'off', 'class' => 'form form-validate form-xhr ui-front')); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

        <h4 class="modal-title" id="import-modal-label">Import Data</h4>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <label for="userfile">CSV File</label>

          <input type="file" name="userfile" id="userfile" required>
        </div>

        <div class="form-group">
          <label>Value Delimiter</label>

          <div class="radio">
            <input type="radio" name="delimiter" id="delimiter_2" value=";">
            <label for="delimiter_2">Semicolon ( ; )</label>
          </div>

          <div class="radio">
            <input type="radio" name="delimiter" id="delimiter_1" value="," checked>
            <label for="delimiter_1">Comma ( , )</label>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-block btn-primary ink-reaction">Import Data</button>
      </div>
      <?= form_close(); ?>
    </div>
  </div>
</div>
<div id="attachment_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

        <h4 class="modal-title" id="import-modal-label">Attachment</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <table style="width: 100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Link</th>
                </tr>
              </thead>
              <tbody id="listView">

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endblock() ?>

<?php startblock('page_modals') ?>
<?php $this->load->view('material/templates/modal_fs') ?>

<?php endblock() ?>

<?php startblock('actions_right') ?>
<div class="section-floating-action-row">
  <?php if (is_granted($modules['purchase_order'], 'document')) : ?>
    <div class="btn-group dropup">
      <a type="button" href="<?= site_url($modules['purchase_order']['route'] . '/index_report'); ?>" class="btn btn-floating-action btn-md btn-info btn-tooltip ink-reaction">
        <i class="md md-assignment"></i>
        <small class="top right">Report</small>
      </a>
    </div>
    <div class="btn-group dropup">
      <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-create-document" data-toggle="dropdown">
        <i class="md md-add"></i>
        <small class="top right">Create <?= $module['label']; ?></small>
      </button>
      <ul class="dropdown-menu dropdown-menu-right" role="menu">
        <?php foreach (config_item('auth_inventory') as $category) : ?>
          <li>
            <a href="<?= site_url($modules['purchase_order']['route'] . '/create/' . $category); ?>"><?= $category; ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="btn-group dropup">
      <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-import-data" data-toggle="modal" data-target="#import-modal">
        <i class="md md-attach-file"></i>
        <small class="top right">Import Data</small>
      </button>
    </div>
  <?php endif ?>

  <?php if (is_granted($modules['purchase_order'], 'approval')) : ?>
    <div class="btn-group dropup">
      <button type="button" data-source="<?= site_url($module['route'] . '/multi_approve/'); ?>" class="btn btn-floating-action btn-lg btn-primary btn-tooltip ink-reaction" id="modal-approve-data-button-multi">
        <i class="md md-spellcheck"></i>
        <small class="top right">approve</small>
      </button>
    </div>
  <?php endif; ?>
  <?php if (is_granted($modules['purchase_order'], 'approval')) : ?>
    <div class="btn-group dropup">
      <button type="button" data-source="<?= site_url($module['route'] . '/multi_reject/'); ?>" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="modal-reject-data-button-multi">
        <i class="md md-close"></i>
        <small class="top right">reject</small>
      </button>
    </div>
  <?php endif; ?>
  <?php if (is_granted($modules['purchase_order'], 'order')) : ?>
    <!-- <div class="btn-group dropup">
      <button type="button" data-source="<?= site_url($module['route'] . '/order/'); ?>" class="btn btn-floating-action btn-lg btn-success btn-tooltip ink-reaction" id="modal-reject-data-button-multi">
        <i class="md md-shopping-cart"></i>
        <small class="top right">Order</small>
      </button>
    </div> -->
  <?php endif; ?>
</div>
<?php endblock() ?>

<?php startblock('datafilter') ?>
<div class="form force-padding">
  <div class="form-group">
    <label for="filter_received_date">Date</label>
    <input class="form-control input-sm filter_daterange" data-column="2" id="filter_received_date" readonly>
  </div>

  <div class="form-group">
    <label for="start_date">Supplier</label>
    <select class="form-control input-sm filter_dropdown" id="vendor" name="vendor" data-column="1">
      <option value="all" <?= ('all' == $selected_vendor) ? 'selected' : ''; ?>>All Supplier</option>
      <?php foreach (available_vendors() as $vendor) : ?>
        <option value="<?= $vendor; ?>" <?= ($vendor == $selected_vendor) ? 'selected' : ''; ?>>
          <?= $vendor; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_status">Status</label>
    <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_status">
      <?php if ((config_item('auth_role') != 'CHIEF OPERATION OFFICER') && (config_item('auth_role') != 'HEAD OF SCHOOL') && (config_item('auth_role') != 'CHIEF OF FINANCE') && (config_item('auth_role') != 'FINANCE MANAGER') && (config_item('auth_role') != 'VP FINANCE')) : ?>
        <option value="">
          All
        </option>
      <?php endif; ?>
      <option value="review">
        Review
      </option>
      <?php if ((config_item('auth_role') == 'CHIEF OPERATION OFFICER') || (config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE') || (config_item('auth_role') == 'FINANCE MANAGER') || (config_item('auth_role') == 'VP FINANCE')) : ?>
        <option value="review_approved">
          Approved
        </option>
      <?php else : ?>
        <option value="approved">
          Approved
        </option>
      <?php endif; ?>
      <option value="rejected">
        Rejected
      </option>
      <option value="order">
        Order
      </option>
      <option value="revisi">
        Revisi
      </option>
      <option value="open">
        Open
      </option>
      <option value="closed">
        Closed
      </option>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_item_group">Category</label>
    <select class="form-control input-sm filter_dropdown" data-column="3" id="filter_item_category">
      <option value="">
        Not filtered
      </option>

      <?php foreach (config_item('auth_inventory') as $category) : ?>
        <option value="<?= $category; ?>">
          <?= $category; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
</div>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/pace/pace.min.js') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?= html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
<?= html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>
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
  var id_purchase_order = "";
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

    var datatableElement = $('[data-provide="datatable"]');
    var datatableOptions = new Object();

    datatableOptions.selectedRows = [];
    datatableOptions.selectedIds = [];
    datatableOptions.clickDelay = 700;
    datatableOptions.clickCount = 0;
    datatableOptions.clickTimer = null;
    datatableOptions.summaryColumns = <?= json_encode($grid['summary_columns']); ?>;

    $(datatableElement)
      .addClass('stripe row-border cell-border order-column nowrap')
      .attr('width', '100%');

    <?php
    if ((config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE') || (config_item('auth_role') == 'FINANCE')) {
      ?>
      $(datatableElement).find('thead tr:first-child th:nth-child(2)').attr('width', 1).text('No.');
      $(datatableElement).find('table td:nth-child(2)').attr('align', 'right');
    <?php
    } else { ?>
      $(datatableElement).find('thead tr:first-child th:first-child').attr('width', 1).text('No.');
      $(datatableElement).find('table td:first-child').attr('align', 'right');
    <?php } ?>
    $.fn.dataTable.ext.errMode = 'throw';

    var datatable = $(datatableElement).DataTable({
      searchDelay: 350,
      scrollY: 410,
      scrollX: true,
      scrollCollapse: true,
      lengthMenu: [
        [10, 50, 100, -1],
        [10, 50, 100, "All"]
      ],
      pageLength: 10,
      pagingType: 'full',

      order: <?= json_encode($grid['order_columns']); ?>,
      fixedColumns: {
        leftColumns: <?= $grid['fixed_columns']; ?>
      },

      language: {
        info: "Total _TOTAL_ entries"
      },

      processing: true,
      serverSide: true,
      ajax: {
        url: "<?= $grid['data_source']; ?>",
        type: "POST",
        error: function(xhr, ajaxOptions, thrownError) {
          console.log(xhr.responseText);
          if (xhr.status == 404) {
            toastr.clear();
            toastr.error('Request page not found. Please contact Technical Support.', 'Loading data failed!');
            alert("page not found");
          } else {
            toastr.clear();
            toastr.error(xhr.status + ': ' + thrownError + '. Report this error!', 'Loading data failed!');
          }
        }
      },

      rowCallback: function(row, data) {
        if ($.inArray(data.DT_RowId, datatableOptions.selectedRows) !== -1) {
          $(row).addClass('selected');
        }
      },
      drawCallback: function(settings) {
        var api = this.api();
        var data = api.rows({
          page: 'current'
        }).data()
        $.each(data, function(i, item) {
          var id = $(item[0]).attr("data-id");
          if (id_purchase_order.indexOf("|" + id + ",") !== -1) {
            $("#cb_" + id).attr('checked', true);
          }
        });

      },
      <?php if ((config_item('auth_role') == 'HEAD OF SCHOOL') || (config_item('auth_role') == 'CHIEF OF FINANCE') || (config_item('auth_role') == 'FINANCE')) { ?>
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: [0]
          },
          {
            searchable: false,
            orderable: false,
            visible: false,
            targets: [21]
          },
          {
            searchable: false,
            orderable: false,
            visible: false,
            targets: [22]
          }
        ],
      <?php } else { ?>
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: [0]
          },
          {
            searchable: false,
            orderable: false,
            visible: false,
            targets: [20]
          },
          {
            searchable: false,
            orderable: false,
            visible: false,
            targets: [21]
          }
        ],
      <?php } ?>
      dom: "<'row'<'col-sm-12'tr>>" +
        "<'datatable-footer force-padding no-y-padding'<'row'<'col-sm-4'i<'clearfix'>l><'col-sm-8'p>>>",
    });

    new $.fn.dataTable.Buttons(datatable, {
      dom: {
        container: {
          className: 'btn-group pull-left'
        },
        button: {
          className: 'btn btn-lg btn-icon-toggle ink-reaction'
        }
      },
      buttons: [{
          extend: 'print',
          className: 'btn-tooltip',
          text: '<i class="fa fa-print"></i><small class="top center">Quick Print</small>',
          // titleAttr: 'Quick print',
          autoPrint: false,
          footer: true,
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'csv',
          name: 'csv',
          text: '<i class="fa fa-file-text-o"></i><small class="top center">export to CSV</small>',
          // titleAttr: 'export to CSV',
          className: 'btn-tooltip',
          footer: true,
          key: {
            ctrlKey: true,
            key: 's'
          },
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          extend: 'excel',
          name: 'excel',
          text: '<i class="fa fa-file-excel-o"></i><small class="top center">export to EXCEL</small>',
          // titleAttr: 'export to EXCEL',
          className: 'btn-tooltip',
          footer: true,
          key: {
            ctrlKey: true,
            key: 'x'
          },
          exportOptions: {
            columns: ':visible'
          }
        },
        {
          name: 'pdf',
          className: 'buttons-pdf btn-tooltip',
          text: '<i class="fa fa-file-pdf-o"></i><small class="top center">export to PDF</small>',
          // titleAttr: 'export to PDF',
          key: {
            ctrlKey: true,
            key: 'd'
          },
          action: function(e, dt, node, config) {
            var pdfUrl = '<?= site_url('pdf'); ?>',
              pdfTitle = '<?= PAGE_TITLE; ?>',
              pdfData = datatable.buttons.exportData({
                columns: ':visible'
              });

            submit_post_via_hidden_form(
              pdfUrl, {
                datatable: pdfData,
                title: pdfTitle
              }
            );
          }
        }
      ]
    });

    datatable.buttons(0, null).container()
      .appendTo($('.btn-toolbar'));

    if (datatableOptions.summaryColumns) {
      datatable.on('xhr', function() {
        var json = datatable.ajax.json();

        $.each(datatableOptions.summaryColumns, function(key, value) {
          $(datatable.column(value).footer()).html(
            json.total[value]
          );
        });
      });
    }
    $(datatableElement).find('tbody').on('click', 'tr', function(e) {
      console.log(e.target.nodeName);
      if (e.target.nodeName === "INPUT") {
        if ($(e.target).attr("type") === "checkbox") {
          if ($(e.target).prop('checked')) {
            id_purchase_order += "|" + $(e.target).attr('data-id') + ",";
          } else {
            id_purchase_order = id_purchase_order.replace("|" + $(this).attr('data-id') + ",", "");
          }
        }

      } else if (e.target.nodeName === "I") {
        var id = $(e.target).attr('data-id');
        getAttachment(id);
        console.log(id);
      } else if (e.target.nodeName === "SPAN") {
        // var a = $(e.target).data('id');
        // console.log(e.target.nodeName);
        ///////////////////////////////////////eventdefault
      } else {
        $(this).popup();
      }

    });

    function getAttachment(id) {
      $.ajax({
        type: "GET",
        url: 'purchase_order/listAttachmentpoe/' + id,
        cache: false,
        success: function(response) {
          var data = jQuery.parseJSON(response)
          $("#listView").html("")
          $("#attachment_modal").modal("show");
          $.each(data, function(i, item) {
            var text = '<tr>' +
              '<td>' + (i + 1) + '</td>' +
              '<td><a href="<?= base_url() ?>' + item.file + '" target="_blank">' + item.file + '</a></td>' +
              '</tr>';
            $("#listView").append(text);
          });
        },
        error: function(xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        }
      });
    }

    $("#modal-approve-data-button-multi").click(function() {
      var action = $(this).data('source');
      $(this).attr('disabled', true);
      if (id_purchase_order !== "") {
        $.post(action, {
          'id_purchase_order': id_purchase_order
        }).done(function(data) {
          console.log(data);
          $("#modal-approve-data-button-multi").attr('disabled', false);
          var result = jQuery.parseJSON(data);
          if (result.status == 'success') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.success('Success aprove data the page will reload');
            window.location.reload();
          } else {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.danger('Failed aprove data');
          }
        }).fail(function() {
          $("#modal-approve-data-button-multi").attr('disabled', false);
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('Delete Failed! This data is still being used by another document.');
        });
      } else {
        $(this).attr('disabled', false);
        toastr.options.timeOut = 10000;
        toastr.options.positionClass = 'toast-top-right';
        toastr.error('Empty selected data');
      }
    });
    $(datatableElement).find('tbody').on('click', 'td', function() {
      datatableOptions.clickCount++;

      var modalOpenOnClick = datatable.row(this).data().DT_RowData.modal;
      var singleClickRow = datatable.row(this).data().DT_RowData.single_click;
      var doubleClickRow = datatable.row(this).data().DT_RowData.double_click;

      if (modalOpenOnClick) {
        var dataModal = $('#data-modal');
        var dataPrimaryKey = datatable.row(this).data().DT_RowData.pkey;

        $.get(modalOpenOnClick, function(data) {
          var obj = $.parseJSON(data);

          if (obj.type == 'denied') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info, 'ACCESS DENIED!');
          } else {
            $(dataModal)
              .find('.modal-body')
              .empty()
              .append(obj.info);

            $(dataModal)
              .find('#modal-print-data-button')
              .attr('href', obj.link.print);

            $(dataModal)
              .find('#modal-edit-data-button')
              .attr('href', obj.link.edit);

            $(dataModal)
              .find('#btn-order')
              .attr('href', obj.link.delete);

            $(dataModal).modal('show');

            $(dataModal).on('click', '.modal-header:not(a)', function() {
              $(dataModal).modal('hide');
            });

            $(dataModal).on('click', '.modal-footer:not(a)', function() {
              $(dataModal).modal('hide');
            });
          }
        });
      } else {
        if (datatableOptions.clickCount === 1) {
          datatableOptions.clickTimer = setTimeout(function() {
            datatableOptions.clickCount = 0;

            if (singleClickRow)
              window.location = singleClickRow;
          }, datatableOptions.clickDelay);
        } else {
          clearTimeout(datatableOptions.clickTimer);
          datatableOptions.clickCount = 0;

          if (doubleClickRow)
            window.location = doubleClickRow;
        }
      }
    });

    $('.filter_numeric_text').on('keyup click', function() {
      var i = $(this).data('column');
      var v = $(this).val();
      datatable.columns(i).search(v).draw();
    });

    $('.filter_dropdown').on('change', function() {
      var i = $(this).data('column');
      var v = $(this).val();
      datatable.columns(i).search(v).draw();
    });

    $('.filter_boolean').on('change', function() {
      var checked = $(this).is(':checked');
      var i = $(this).data('column');

      if (checked) {
        datatable.columns(i).search('true').draw();
      } else {
        datatable.columns(i).search('').draw();
      }
    });

    $('.filter_daterange').daterangepicker({
      autoUpdateInput: false,
      parentEl: '#offcanvas-datatable-filter',
      locale: {
        cancelLabel: 'Clear'
      }
    }).on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' ' + picker.endDate.format('YYYY-MM-DD'));
      var i = $(this).data('column');
      var v = $(this).val();
      datatable.columns(i).search(v).draw();
    }).on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
      var i = $(this).data('column');
      datatable.columns(i).search('').draw();
    });

    $('a.column-toggle').on('click', function(e) {
      e.preventDefault();
      var column = datatable.column($(this).attr('data-column'));

      column.visible(!column.visible());

      var label = $(this).attr('data-label');
      var text = (column.visible() === true ? '<div class="tile-text">' + label + '</div>' : '<div class="tile-text text-muted">' + label + '</div>');

      $(this).html(text);
    });

    $('.dataTables_paginate').find('a').removeClass();
    $('#datatable-form').removeClass('hidden');
    $('#datatable-form input').on('keyup', function() {
      datatable.search(this.value).draw();
    });
    $('[data-toggle="reload"]').on('click', function() {
      datatable.ajax.reload(null, false);
    });

    datatable.on('processing.dt', function(e, settings, processing) {
      if (processing) {
        $('.progress-overlay').show();
      } else {
        $('.progress-overlay').hide();
      }
    });
    var notes = "";
    $("#modal-reject-data-button-multi").click(function() {
      if (!encodeNotes()) {
        toastr.options.timeOut = 10000;
        toastr.options.positionClass = 'toast-top-right';
        toastr.error('You must filled notes for each item that you want to reject');
      } else {

        if (id_purchase_order == "") {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('You must select item that you want to reject');
        } else {
          $.ajax({
            type: "POST",
            url: 'purchase_order/multi_reject',
            data: {
              "id_purchase_order": id_purchase_order,
              "notes": notes
            },
            cache: false,
            success: function(response) {
              console.log(response);
              var data = jQuery.parseJSON(response);
              if (data.status == "success") {
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.success('Successfully reject item, the page will reload now');
                window.location.reload();
              } else {
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error('Failed rejected item');
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              console.log(xhr.status);
              console.log(xhr.responseText);
              console.log(thrownError);
            }
          });
        }
      }
    });

    function encodeNotes() {
      new_id_purchase_order = id_purchase_order.replace(/\|/g, "");
      new_id_purchase_order = new_id_purchase_order.substring(0, new_id_purchase_order.length - 1);
      arr = new_id_purchase_order.split(",");
      notes = "";
      y = 0;
      $.each(arr, function(i, x) {
        if ($("#note_" + x).val() != "") {
          notes = notes + "|" + $("#note_" + x).val() + "##,";
          y += 1;
        } else {
          return false;
        }
      });
      if (y == arr.length) {
        return true
      } else {
        return false
      }

    }
    $(document).on('click', '.btn-xhr-delete', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('.form-xhr');
      var action = button.attr('href');
      $('#order-modal').show();

      button.attr('disabled', true);

      if (confirm('Are you sure want to delete this data? Beware of this data can not be restored after it is removed. Continue?')) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);
          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);

            buttonToDelete.attr('disabled', false);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            form.reset();

            $('[data-dismiss="modal"]').trigger('click');

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        }).fail(function() {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('Delete Failed! This data is still being used by another document.');
        });
      }

      button.attr('disabled', false);
    });

    $(document).on('click', '.btn-xhr-order', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('.form-xhr-order');
      var action = button.attr('href');
      // $('#order-modal form').attr('action', button.data('href'));
      // $('#order-modal').show();
      button.attr('disabled', true);

      if (confirm('Are you sure want to order this Purchase ? Continue?')) {
        $.post(action, form.serialize()).done(function(data) {
          var obj = $.parseJSON(data);
          if (obj.type == 'danger') {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error(obj.info);

            buttonToDelete.attr('disabled', false);
          } else {
            toastr.options.positionClass = 'toast-top-right';
            toastr.success(obj.info);

            form.reset();

            $('[data-dismiss="modal"]').trigger('click');

            if (datatable) {
              datatable.ajax.reload(null, false);
            }
          }
        }).fail(function() {
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error('Delete Failed! This data is still being used by another document.');
        });
      }

      button.attr('disabled', false);
    });
  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>