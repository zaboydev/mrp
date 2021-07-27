<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>

<?php startblock('page_body') ?>
<div id="modal-item" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-item-label" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <!--  -->

      <div class="modal-body no-padding"></div>

      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<?php $this->load->view('material/templates/datatable') ?>
<?php endblock() ?>

<?php startblock('page_modals') ?>
<?php $this->load->view('material/templates/modal_fs') ?>
<?php endblock() ?>

<?php startblock('actions_right') ?>

<div class="section-floating-action-row">
  <?php if (config_item('as_head_department')=='yes' || config_item('auth_role')=='BUDGETCONTROL') : ?>
    <div class="btn-group dropup">
      <button type="button" data-source="<?= site_url($module['route'] . '/multi_reject/'); ?>" class="btn btn-floating-action btn-md btn-danger btn-tooltip ink-reaction" id="modal-reject-data-button-multi">
        <i class="md md-clear"></i>
        <small class="top right">reject</small>
      </button>
      <button type="button" data-source="<?= site_url($module['route'] . '/multi_approve/'); ?>" class="btn btn-floating-action btn-lg btn-primary btn-tooltip ink-reaction" id="modal-approve-data-button-multi">
        <i class="md md-spellcheck"></i>
        <small class="top right">approve</small>
      </button>
    <?php endif ?>
    <?php if (is_granted($module, 'document')) : ?>
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-create-document" data-toggle="dropdown">
          <i class="md md-add"></i>
          <small class="top right">Create <?= $module['label']; ?></small>
        </button>

        <ul class="dropdown-menu dropdown-menu-right" role="menu">
          <?php foreach (config_item('auth_annual_cost_centers') as $annual_cost_center) : ?>
            <li>
              <a href="<?= site_url($module['route'] . '/create/' . $annual_cost_center['id']); ?>"><?= $annual_cost_center['cost_center_name']; ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif ?>
    <?php if (is_granted($module, 'closing')) : ?>
      <div class="btn-group dropup">
        <button type="button" data-source="<?= site_url($module['route'] . '/multi_closing/'); ?>" class="btn btn-floating-action btn-md btn-info btn-tooltip ink-reaction" id="modal-close-data-button-multi">
          <i class="md md-check"></i>
          <small class="top right">Close</small>
        </button>
      <?php endif ?>

      </div>
      <?php endblock() ?>

      <?php startblock('datafilter') ?>
      <div class="form force-padding">
        <div class="form-group" style="margin-top: 40px">
          <label for="filter_required_date">Required Date</label>
          <input class="form-control input-sm filter_daterange" data-column="1" id="filter_required_date" readonly>
        </div>

        <div class="form-group">
          <label for="filter_status">Status</label>
          <select class="form-control input-sm filter_dropdown" data-column="2" id="filter_status">
            <option value="all">
              All Status
            </option>
            <option value="WAITING FOR BUDGETCONTROL" <?php (config_item('auth_role')=='BUDGETCONTROL')?'selected':''?>>
              WAITING FOR BUDGETCONTROL
            </option>
            <option value="WAITING FOR HEAD DEPT" <?php (config_item('as_head_department')=='yes')?'selected':''?>>
              WAITING FOR HEAD DEPT
            </option>
            <option value="approved">
              APPROVED
            </option>
            <option value="rejected">
              REJECTED
            </option>
            <option value="cancel">
              CANCELED
            </option>
          </select>
        </div>

        <div class="form-group">
          <label for="filter_item_category">Cost Center</label>
          <select class="form-control input-sm filter_dropdown" data-column="3" id="filter_item_category">
            <option value="all">
              Not filtered            
            </option>
            <?php foreach (config_item('auth_annual_cost_centers') as $annual_cost_center) : ?>
              <option value="<?= $annual_cost_center['cost_center_name']; ?>">
                <?= $annual_cost_center['cost_center_name'] ?>
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
        $('input[type=radio][name=request_to]').change(function() {
          var val = $(this).val();
          var url = $(this).data('source');
          console.log(val);
          $.get(url, {
            data: val
          }, function(data) {
            var result = jQuery.parseJSON(data);
            if (result.status == "success") {
              window.location.reload();
            }
          });
        });

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
                console.log(xhr.responseText);
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

          var buttonSubmitDocument = $('#btn-submit-document');
          var formDocument = $('#form-change-item');
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

          $("#modal-approve-data-button-multi").click(function() {
            var action = $(this).data('source');
            encodeNotes();
            // if (!encodeNotes()) {
            //   toastr.options.timeOut = 10000;
            //   toastr.options.positionClass = 'toast-top-right';
            //   toastr.error('You must filled Notes for each item that you want to approve');
            // } else {
              $(this).attr('disabled', true);
              if (id_purchase_order !== "") {
                $.post(action, {
                  'id_capex_request': id_purchase_order,
                  'notes': notes
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
            // }

          });

          $("#modal-close-data-button-multi").click(function() {
            var action = $(this).data('source');
            $(this).attr('disabled', true);
            encodeNotes();
            // if (!encodeNotes()) {
            //   toastr.options.timeOut = 10000;
            //   toastr.options.positionClass = 'toast-top-right';
            //   toastr.error('You must filled notes for each item that you want to reject');
            // } else {
              if (id_purchase_order !== "") {
                $.post(action, {
                  'id_purchase_order': id_purchase_order,
                  'notes': notes
                }).done(function(data) {
                  console.log(data);
                  $("#modal-close-data-button-multi").attr('disabled', false);
                  var result = jQuery.parseJSON(data);
                  if (result.status == 'success') {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.success('Success close data the page will reload');
                    window.location.reload();
                  } else {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.danger('Failed close data');
                  }
                }).fail(function() {
                  $("#modal-close-data-button-multi").attr('disabled', false);
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
            // }

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

          function encodePrice() {
            new_id_purchase_order = id_purchase_order.replace(/\|/g, "");
            new_id_purchase_order = new_id_purchase_order.substring(0, new_id_purchase_order.length - 1);
            arr = new_id_purchase_order.split(",");
            price = "";
            y = 0;
            $.each(arr, function(i, x) {
              if ($("#price_" + x).val() != "") {
                price = price + "|" + $("#price_" + x).val() + "##,";
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

          $("#modal-reject-data-button-multi").click(function() {
            if (!encodeNotes()) {
              toastr.options.timeOut = 10000;
              toastr.options.positionClass = 'toast-top-right';
              toastr.error('You must filled notes for each item that you want to reject');
            } else if (!encodePrice()) {
              toastr.options.timeOut = 10000;
              toastr.options.positionClass = 'toast-top-right';
              toastr.error('You must filled Price for each item that you want to approve');
            } else {

              if (id_purchase_order == "") {
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error('You must select item that you want to reject');
              } else {
                $.ajax({
                  type: "POST",
                  url: 'purchase_request/multi_reject',
                  data: {
                    "id_purchase_order": id_purchase_order,
                    "notes": notes,
                    "price": price
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
              console.log(id_purchase_order);

            } else if (e.target.nodeName === "SPAN") {
              var a = $(e.target).data('id');
              console.log(e.target.nodeName);
              // console.log(price);
              ///////////////////////////////////////eventdefault
            } else {
              $(this).popup();
            }

          });
          
          $(datatableElement).find('tbody').on('click', 'a', function(e) {
            e.preventDefault();
            // console.log("tuliskan fungsinya disini");
            // tulis disini
            var id = $(this).data('id');
            if (id == 'item') {
              var a = $(this).data('item-row');
              $.ajax({
                url: "<?= site_url($module['route'] . '/info_item/'); ?>" + "/" + a,
                type: 'get',
                success: function(data) {
                  var dataModal = $('#modal-item');
                  var obj = $.parseJSON(data);
                  $(dataModal)
                    .find('.modal-body')
                    .empty()
                    .append(obj.info);
                  $(dataModal).modal('show');
                }
              });
            }

            if (id == 'on-hand') {
              var a = $(this).data('item-row');
              $.ajax({
                url: "<?= site_url($module['route'] . '/info_on_hand/'); ?>" + "/" + a,
                type: 'get',
                success: function(data) {
                  var dataModal = $('#modal-item');
                  var obj = $.parseJSON(data);
                  $(dataModal)
                    .find('.modal-body')
                    .empty()
                    .append(obj.info);
                  $(dataModal).modal('show');
                }
              });
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
            },
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