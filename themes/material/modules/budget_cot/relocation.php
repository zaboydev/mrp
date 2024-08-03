<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
  <section class="has-actions style-default">
    <div class="section-body">
      <?=form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-document'));?>
        <div class="card">
          <div class="card-head style-primary-dark">
            <header>Stock Relocation <?=$entity['part_number'];?></header>
          </div>
          <div class="card-body no-padding">
            <?php
            if ( $this->session->flashdata('alert') )
              render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
            ?>

            <div class="document-header force-padding">
              <div class="row">
                <div class="col-sm-6 col-lg-5">
                  <h4>Stock In Stores Info</h4>

                  <div class="">
                    <dl class="dl-inline">
                      <dt>
                        Serial Number
                      </dt>
                      <dd>
                        <?=print_string($entity['serial_number']);?>
                      </dd>

                      <dt>
                        Part Number
                      </dt>
                      <dd>
                        <?=print_string($entity['part_number']);?>
                      </dd>

                      <dt>
                        Description
                      </dt>
                      <dd>
                        <?=print_string($entity['description']);?>
                      </dd>

                      <dt>
                        Group
                      </dt>
                      <dd>
                        <?=print_string($entity['group']);?>
                      </dd>

                      <dt>
                        Category
                      </dt>
                      <dd>
                        <?=print_string($entity['category']);?>
                      </dd>

                      <dt>
                        Base
                      </dt>
                      <dd>
                        <?=print_string($entity['warehouse']);?>
                      </dd>

                      <dt>
                        Stores
                      </dt>
                      <dd>
                        <?=print_string($entity['stores']);?>
                      </dd>

                      <dt>
                        Condition
                      </dt>
                      <dd>
                        <?=print_string($entity['condition']);?>
                      </dd>

                      <dt>
                        Quantity
                      </dt>
                      <dd>
                        <?=number_format($entity['quantity'], 2);?>
                        <?=print_string($entity['unit']);?>
                      </dd>

                      <dt>
                        Expired
                      </dt>
                      <dd>
                        <?=print_date($entity['expired_date']);?>
                      </dd>

                      <dt>
                        Received
                      </dt>
                      <dd>
                        <?=print_date($entity['received_date']);?>
                      </dd>
                    </dl>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-7">
                  <h4>Relocation Form</h4>

                  <div class="well well-lg">
                    <div class="row">
                      <div class="col-sm-5">
                        <div class="form-group">
                          <input type="text" name="relocation_current_stores" id="relocation_current_stores" class="form-control" value="<?=$entity['stores'];?>" readonly>
                          <label for="relocation_current_stores">Current Stores</label>
                        </div>

                        <div class="form-group">
                          <input type="text" name="relocation_stores" id="relocation_stores" class="form-control"  data-source="<?=site_url($modules['ajax']['route'] .'/json_stores/'. $entity['category'] .'/'. $entity['warehouse']);?>" autofocus required>
                          <label for="relocation_stores">Move to Stores</label>
                        </div>
                      </div>
                      <div class="col-sm-7">
                        <div class="form-group">
                          <textarea name="remarks" id="remarks" class="form-control" rows="5"></textarea>
                          <label for="remarks">Remarks</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-actionbar">
            <div class="card-actionbar-row">

              <a href="<?=site_url($module['route'] .'/relocation_discard');?>" class="btn btn-flat btn-danger ink-reaction">
                Discard
              </a>
            </div>
          </div>
        </div>
      <?=form_close();?>
    </div>

    <div class="section-action style-default-bright">
      <div class="section-action-row">
        <div class="btn-toolbar">
          <div id="core-buttons" class="pull-left btn-group">
            <button class="btn btn-icon-toggle btn-lg ink-reaction btn-back" data-toggle="back">
              <i class="md md-arrow-back"></i>
            </button>

            <button class="btn btn-icon-toggle btn-lg ink-reaction btn-home" data-toggle="redirect" data-url="<?=site_url();?>">
              <i class="md md-home"></i>
            </button>
          </div>
        </div>
      </div>

      <div class="section-floating-action-row">
        <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-submit-document" href="<?=site_url($module['route'] .'/relocation_save/'. $entity['id']);?>">
          <i class="md md-save"></i>
          <small class="top right">Save Relocation</small>
        </a>
      </div>
    </div>
  </section>
<?php endblock() ?>

<?php startblock('scripts') ?>
  <?=html_script('vendors/pace/pace.min.js') ?>
  <?=html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
  <?=html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>

  <script>
  Pace.on('start', function(){
    $('.progress-overlay').show();
  });

  Pace.on('done', function(){
    $('.progress-overlay').hide();
  });

  $(function(){
    $('#btn-submit-document').on('click', function(e){
      e.preventDefault();
      $(this).attr('disabled', true);

      var url = $(this).attr('href');
      var frm = $('#form-document');

      $.post(url, frm.serialize(), function(data){
        var obj = $.parseJSON(data);

        if ( obj.success == false ){
          toastr.options.timeOut = 10000;
          toastr.options.positionClass = 'toast-top-right';
          toastr.error(obj.message);
        } else {
          toastr.options.timeOut = 4500;
          toastr.options.closeButton = false;
          toastr.options.progressBar = true;
          toastr.options.positionClass = 'toast-top-right';
          toastr.success(obj.message);

          window.setTimeout(function(){
            window.location.href = '<?=site_url($module['route']);?>';
          }, 5000);
        }
      });

      $(this).attr('disabled', false);
    });

    $( 'input[id="relocation_stores"]' ).on('focus click', function(){
      $.ajax({
        url: $( 'input[id="relocation_stores"]' ).data('source'),
        dataType: "json",
        success: function (data) {
          $( 'input[id="relocation_stores"]' ).autocomplete({
            source: function (request, response) {
              var results = $.ui.autocomplete.filter(data, request.term);
              response(results.slice(0, 10));
            }
          });
        }
      });
    });
  })
  </script>

  <?=html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
