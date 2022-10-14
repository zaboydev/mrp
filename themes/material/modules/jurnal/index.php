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
            <div class="col-sm-12 col-md-4">
              <div class="form-group">
                <input type="text" name="start_date" id="start_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control filter_date" readonly>
                <label for="start_date">Date From</label>
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="form-group">
                <input type="text" name="end_date" id="end_date" data-provide="datepicker" data-date-format="yyyy-mm-dd" class="form-control filter_date" readonly>
                <input type="hidden" name="type" id="type" class="form-control" value="general">
                <label for="end_date">To</label>
              </div>
            </div>
            <div class="col-sm-12 col-md-4">
              <div class="form-group">
                <button type="button" class="btn btn-sm btn-info btn-export" data-tipe="excel">Excel</button>
                <button type="button" class="btn btn-sm btn-danger btn-export" data-tipe="print">Print</button>
              </div>
            </div>
          </div>
        </div>
        <div class="document-data table-responsive">
          <div class="row">
            <div class="col-sm-12 col-md-offset-1 col-md-10 ">
              <div id="report_view">
                <div class="newoverlay" id="loadingScreen2" style="display: none;">
                  <i class="fa fa-refresh fa-spin"></i>
                </div>
                <table class="table table-hover table-bordered" id="table-document">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Date</th>
                      <th>Document Number</th>
                      <th>Account</th>
                      <th>Debit</th>
                      <th>Credit</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr></tr>
                  </tbody>
                </table>
              </div>   
            </div>
          </div>
                 
        </div>
      </div>
    </div>
    <?= form_close(); ?>
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
    $('[data-provide="datepicker"]').datepicker({
      autoclose: true,
      todayHighlight: true,
      format: 'yyyy-mm-dd',
    });

    $('.filter_date').on('change', function() {
      var start_date    = $('#start_date').val();
      var end_date      = $('#end_date').val();
      var type          = $('#type').val();

      if(start_date!='' && end_date!=''){
        var formData = {
          start_date    : start_date,
          end_date      : end_date,
          type          : type,
        };
        var url = "<?= $grid['data_source']; ?>";

        view_report(url,formData);
      }
      
    });

    $('.btn-export').on('click', function(e) {
      var start_date    = $('#start_date').val();
      var end_date      = $('#end_date').val();
      var type          = $('#type').val();
      var _export       = $(this).data('tipe');

      console.log(_export);

      if(start_date!='' && end_date!=''){
        var formData = {
          start_date    : start_date,
          end_date      : end_date,
          type          : type,
          export        : _export
        };
        
        var url = "<?= $grid['data_export']; ?>";

        print_report(url,formData);
      }
    });

    function view_report(url, formData) {
      $("#loadingScreen2").attr("style", "display:block");
      $.ajax({
        url: url,
        type: 'GET',
        data: formData,
        success: function (data) {
          var obj = $.parseJSON(data);
          $('#report_view').html(obj.info);
        },
        error: function (request, status, error) {
          swal({
            title: 'Perhatian',
            text: 'Data Gagal Disimpan! ',
            type: 'error'
          });
        }
      });
    }

    function print_report(url, formData) {
      // $("#loadingScreen2").attr("style", "display:block");
      $.ajax({
        url: url,
        type: 'GET',
        data: formData,
        success: function (data) {
          var obj = $.parseJSON(data);
          // $('#report_view').html(obj.info);
          window.open(obj.open);
        },
        error: function (request, status, error) {
          swal({
            title: 'Perhatian',
            text: 'Data Gagal Disimpan! ',
            type: 'error'
          });
        }
      });
    }

    

  });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>