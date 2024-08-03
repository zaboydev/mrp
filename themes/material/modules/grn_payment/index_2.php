<?php include 'themes/material/page.php' ?>

<!--tambahan 9/Mei/2018-->

<!--tambahan 9/Mei/2018-->

<?php startblock('page_head_tools') ?>

<?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>

<?php $this->load->view('material/templates/datatable') ?>
<!-- <div id="add-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="add-modal-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= form_open_multipart(site_url($module['route'] . '/add_cot'), array('autocomplete' => 'off', 'class' => 'form form-validate form-xhr ui-front', 'id' => 'add_cot_form')); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>

        <h4 class="modal-title" id="import-modal-label">Info AP</h4>
      </div>

      <div class="modal-body">
        <div class="well">
          <div class="clearfix">
            <div class="pull-left">AP NUMBER.: </div>

            <div class="pull-right" id="ap_number_lbl"></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">DATE: </div>
            <div class="pull-right" id="date_lbl"></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">GRN NUMBER: </div>
            <div class="pull-right" id="grn_number_lbl"></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">VENDOR: </div>
            <div class="pull-right" id="vendor_lbl"></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">AMOUNT: </div>
            <div class="pull-right" id="amount_lbl"></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">REMAINING DEBT: </div>
            <div class="pull-right" id="sisa_lbl"></div>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <div id="waiting_pay">
          <?php if (config_item('auth_role') == 'FINANCE') { ?>
            <div class="col-md-6">

              <button type="button" class="btn btn-block btn-primary ink-reaction payment" id="btn_payment" data-id="">PAYMENT</button>

            </div>
            <div class="col-md-6">
              <button type="button" class="btn btn-block btn-danger ink-reaction urgent" id="btn_urgent" data-id="">URGENT</button>
            </div>
          <?php } else {
            ?>
            <div class="col-md-12">
              <button type="button" class="btn btn-block btn-danger ink-reaction urgent" id="btn_urgent" data-id="">URGENT</button>
            </div>
          <?php
          } ?>
        </div>
        <div id="urgent_pay">
          <?php if (config_item('auth_role') == 'FINANCE') { ?>
            <div class="col-md-12">
              <button type="button" class="btn btn-block btn-primary ink-reaction payment" id="btn_payment" data-id="">PAYMENT</button>
            </div>
          <?php } ?>
        </div>
      </div>
      <?= form_close(); ?>
    </div>
  </div>
</div> -->
<?php endblock() ?>

<?php startblock('page_modals') ?>
<?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>

<?php startblock('datafilter') ?>
<div class="form force-padding">
  <div class="form-group">
    <label for="filter_received_date">Received Date</label>
    <input class="form-control input-sm filter_daterange" data-column="2" id="filter_received_date" readonly>
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

  <div class="form-group">
    <label for="filter_warehouse">Base</label>
    <select class="form-control input-sm filter_dropdown" data-column="4" id="filter_warehouse">
      <option value="">
        Not filtered
      </option>

      <?php foreach (config_item('auth_warehouses') as $warehouse) : ?>
        <option value="<?= $warehouse; ?>">
          <?= $warehouse; ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_condition">Condition</label>
    <select class="form-control input-sm filter_dropdown" data-column="9" id="filter_condition">
      <option value="">
        SERVICEABLE
      </option>

      <?php foreach (config_item('condition') as $condition) : ?>
        <?php if ($condition !== 'SERVICEABLE') : ?>
          <option value="<?= $condition; ?>">
            <?= $condition; ?>
          </option>
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="form-group">
    <label for="filter_description">Description</label>
    <input type="text" class="form-control input-sm filter_numeric_text" data-column="5" id="filter_description">
  </div>

  <div class="form-group">
    <label for="filter_part_number">Part Number</label>
    <input type="text" class="form-control input-sm filter_numeric_text" data-column="6" id="filter_part_number">
  </div>

  <div class="form-group">
    <label for="filter_received_from">Received From</label>
    <input type="text" class="form-control input-sm filter_numeric_text" data-column="15" id="filter_received_from">
  </div>
</div>

<?php endblock() ?>
<?php if (is_granted($module, 'import')) : ?>
  <?php startblock('offcanvas_left_actions') ?>
  <li class="tile">
    <a class="tile-content ink-reaction" href="#offcanvas-import" data-toggle="offcanvas">
      <div class="tile-icon">
        <i class="fa fa-download"></i>
      </div>
      <div class="tile-text">
        Import Data
        <small>import from csv file</small>
      </div>
    </a>
  </li>
  <?php endblock() ?>

  <?php startblock('offcanvas_left_list') ?>
  <div id="offcanvas-import" class="offcanvas-pane width-8">
    <div class="offcanvas-head style-primary-dark">
      <header>Import</header>
      <div class="offcanvas-tools">
        <a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">
          <i class="md md-close"></i>
        </a>
        <a class="btn btn-icon-toggle pull-right" href="#offcanvas-datatable-filter" data-toggle="offcanvas">
          <i class="md md-arrow-back"></i>
        </a>
      </div>
    </div>

    <div class="offcanvas-body no-padding">
      <?php $this->load->view('material/modules/stores/import') ?>
    </div>
  </div>
  <?php endblock() ?>

<?php endif; ?>

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

  function detailAP(uri) {
    $.ajax({
      type: "POST",
      url: uri,
      cache: false,
      success: function(response) {
        var data = jQuery.parseJSON(response);
        parseDetail(data);
      },
      error: function(xhr, ajaxOptions, thrownError) {
        console.log(xhr.status);
        console.log(xhr.responseText);
        console.log(thrownError);
      }
    });
  }

  function parseDetail(element) {
    // console.log(data);
    $("#ap_number_lbl").html(element.find("td").eq(1).find("span").html());
    $("#date_lbl").html(element.find("td").eq(2).html());
    $("#grn_number_lbl").html(element.find("td").eq(3).find("span").html());
    $("#vendor_lbl").html(element.find("td").eq(4).find("span").html());
    $("#amount_lbl").html(element.find("td").eq(5).find("span").html());
    $("#sisa_lbl").html(element.find("td").eq(6).find("span").html());
    if (element.find("td").eq(7).find("span").html() == "waiting for repayment") {
      $("#waiting_pay").css("display", "block");
      $("#urgent_pay").css("display", "none");
    } else if (element.find("td").eq(7).find("span").html() == "urgent") {
      $("#urgent_pay").css("display", "block");
      $("#waiting_pay").css("display", "none");
    } else {
      $("#urgent_pay").css("display", "none");
      $("#waiting_pay").css("display", "none");
    }
    $(".payment").attr("data-id", element.data('id'));
    $(".urgent").attr("data-id", element.data('id'));
    $("#add-modal").modal("show");

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

    $(datatableElement).find('thead tr:first-child th:first-child').attr('width', 1).text('No.');
    $(datatableElement).find('table td:first-child').attr('align', 'right');

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
          if (xhr.status == 404) {
            toastr.clear();
            toastr.error('Request page not found. Please contact Technical Support.', 'Loading data failed!');
            alert("page not found");
          } else {
            toastr.clear();
            toastr.error(textStatus + ': ' + errorThrown + '. Report this error!', 'Loading data failed!');
          }
        }
      },

      rowCallback: function(row, data) {
        if ($.inArray(data.DT_RowId, datatableOptions.selectedRows) !== -1) {
          $(row).addClass('selected');
        }
      },

      columnDefs: [{
        searchable: false,
        orderable: false,
        targets: [0]
      }],

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
              .find('#modal-delete-data-button')
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

    // $(datatableElement).find('tbody').on('click', 'tr', function(e) {
    //   //detailAP($(this).data('source'));
    //   parseDetail($(this));
    // });
    $('.filter_numeric_text').on('keyup', function() {
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
    $(".urgent").click(function() {
      var id = $(this).attr("data-id");
      $.ajax({
        type: "POST",
        url: 'account_payable/urgent/' + id,
        cache: false,
        success: function(response) {
          var data = jQuery.parseJSON(response);
          if (data.status == "success") {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.success('Success change status data the page will reload');
            window.location.reload();
          } else {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.danger('Failed change status data');
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        }
      });
    })
    $(document).on('click', '.btn-xhr-delete', function(e) {
      e.preventDefault();

      var button = $(this);
      var form = $('.form-xhr');
      var action = button.attr('href');

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
  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>