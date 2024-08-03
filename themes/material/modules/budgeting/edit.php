<div class="card card-underline style-default-bright">
  <div class="card-head style-primary-dark">
    <header>EDIT <?= strtoupper($module['label']); ?></header>

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
      <?= form_open_multipart(site_url($module['route'] . '/save_budget'), array('autocomplete' => 'off', 'class' => 'form form-validate form-xhr ui-front', "id" => "edit_budget_form")); ?>
      <div class="col-md-12">
        <div class="newoverlay" style="" id="loadingScreen2" style="display: none;">
          <i class="fa fa-refresh fa-spin"></i>
        </div>
        <div class="form-group" style="margin-bottom: 30px;">
          <h5 class="text-center"> Maximum total hour <?= $entity->hours ?> </h5>
        </div>
        <input type="hidden" id="id_cot" name="id_cot" value="<?= $id_cot ?>">
        <input type="hidden" id="item_id" name="item_id" value="<?= $entity->item_id ?>">
        <input type="hidden" name="qty_requirement" value="<?= $entity->qty_requirement ?>">
        <input type="hidden" name="cot_hour" id="maxHour" value="<?= $entity->hours ?>">
        <div class="col-md-6">
          <div class="form-group">
            <label for="exampleInputEmail1">ONHAND</label>
            <div class="col-md-8" style="padding-left: 0">
              <input type="text" class="form-control number" required="" id="onhand" value="<?= $entity->onhand ?>" name="onhand" placeholder="">
            </div>
            <button type="button" class="btn btn-primary " id="update_onhand">update from stock</button>
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">JANUARI QTY</label>
            <input type="text" class="form-control number" required="" id="m_1" value="<?= $hour[0]->mtd_quantity ?>" name="m_1" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">FEBRUARI QTY</label>
            <input type="text" class="form-control number" required="" id="m_2" value="<?= $hour[1]->mtd_quantity ?>" name="m_2" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">MARET QTY</label>
            <input type="text" class="form-control number" required="" id="m_3" value="<?= $hour[2]->mtd_quantity ?>" name="m_3" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">APRIL QTY</label>
            <input type="text" class="form-control number" required="" id="m_4" value="<?= $hour[3]->mtd_quantity ?>" name="m_4" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">MEI QTY</label>
            <input type="text" class="form-control number" required="" id="m_5" value="<?= $hour[4]->mtd_quantity ?>" name="m_5" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">JUN QTY</label>
            <input type="text" class="form-control number" required="" id="m_6" value="<?= $hour[5]->mtd_quantity ?>" name="m_6" placeholder="">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="exampleInputEmail1">JUL QTY</label>
            <input type="text" class="form-control number" required="" id="m_7" value="<?= $hour[6]->mtd_quantity ?>" name="m_7" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">AGUSTUS QTY</label>
            <input type="text" class="form-control number" required="" id="m_8" value="<?= $hour[7]->mtd_quantity ?>" name="m_8" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">SEPTEMBER QTY</label>
            <input type="text" class="form-control number" required="" id="m_9" value="<?= $hour[8]->mtd_quantity ?>" name="m_9" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">OKTOBER QTY</label>
            <input type="text" class="form-control number" required="" id="m_10" value="<?= $hour[9]->mtd_quantity ?>" name="m_10" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">NOVEMBER QTY</label>
            <input type="text" class="form-control number" required="" id="m_11" value="<?= $hour[10]->mtd_quantity ?>" name="m_11" placeholder="">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail1">DESEMBER QTY</label>
            <input type="text" class="form-control number" required="" id="m_12" value="<?= $hour[11]->mtd_quantity ?>" name="m_12" placeholder="">
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group text-center" style="">
          <button type="submit" class="btn btn-primary">save</button>
        </div>
      </div>
      <?= form_close(); ?>
    </div>
  </div>

  <div class="card-foot">
    <?php if (is_granted($module, 'delete') && $entity->status != 'APPROVED') : ?>
      <?= form_open(current_url(), array(
          'class' => 'form-xhr pull-left',
        )); ?>
      <input type="hidden" name="id" id="id" value="<?= $id_cot; ?>">

      <a href="<?= site_url($module['route'] . '/delete_ajax/'); ?>" class="btn btn-floating-action btn-danger btn-xhr-delete btn-tooltip ink-reaction" id="modal-delete-data-button">
        <i class="md md-delete"></i>
        <small class="top left">delete</small>
      </a>
      <?= form_close(); ?>
    <?php endif; ?>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    console.log("here");
    $("#loadingScreen2").attr("style", "display:none");
    $("#edit_budget_form").submit(function(e) {
      e.preventDefault();
      var max = $("#maxHour").val();
      total = 0;
      for (var i = 1; i < 13; i++) {
        total += parseInt($("#m_" + i).val());
      }
      console.log(parseInt($("#maxHour").val()))
      console.log(total);
      if (total != parseInt($("#maxHour").val())) {

        toastr.options.timeOut = 10000;
        toastr.options.positionClass = 'toast-top-right';
        toastr.error('This total hour is ' + (total > maxHour ? "more" : "less") + ' than COT hour');

      } else {
        $("#loadingScreen2").attr("style", "display:block");
        $.ajax({
          type: "POST",
          url: $(this).attr('action'),
          data: new FormData(this),
          processData: false,
          contentType: false,
          success: function(response) {
            var data = jQuery.parseJSON(response);
            $("#loadingScreen2").attr("style", "display:none");
            if (data.status == "success") {
              location.reload();
            } else {
              toastr.options.timeOut = 10000;
              toastr.options.positionClass = 'toast-top-right';
              toastr.error("Failed to update");
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            $("#loadingScreen2").attr("style", "display:none");
            console.log(xhr.status);
            console.log(xhr.responseText);
            console.log(thrownError);
          }
        });
      }
    });
    $("#update_onhand").click(function() {
      var baselink = $("#baselink").val();
      id_cot = $("#id_cot").val();
      $("#loadingScreen2").attr("style", "display:block");
      $.ajax({
        type: "POST",
        url: baselink + '/update_onhand',
        data: {
          "id_cot": id_cot
        },
        cache: false,
        success: function(response) {
          console.log(response);
          $("#loadingScreen2").attr("style", "display:none");
          var data = jQuery.parseJSON(response);
          if (data.status == "success") {
            $("#onhand").val(data.onhand);
          } else {
            toastr.options.timeOut = 10000;
            toastr.options.positionClass = 'toast-top-right';
            toastr.error("Failed getting latest onhand");
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          $("#loadingScreen2").attr("style", "display:none");
          console.log(xhr.status);
          console.log(xhr.responseText);
          console.log(thrownError);
        }
      });
    })
  })
</script>