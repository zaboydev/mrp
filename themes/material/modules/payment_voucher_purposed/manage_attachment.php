<?php include 'themes/material/simple.php' ?>
<?php startblock('body') ?>
<div class="container">

  <h4 class="page-header">Attachment</h4>
  
  <form id="form_add_vendor" id="inputForm" class="form" role="form" method="post" enctype="multipart/form-data" action="<?=site_url($module['route'] .'/add_attachment_to_db/'. $id_poe);?>">
    <div class="row">
      <div class="col-sm-12">
        <div class="form-group">
          <label> Add Attachment</label>
          <input type="file" name="attachment" id="attachment" accept=".png,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf">
          <p style="font-size: 8pt">Allowing file format <i>doc, docx, xls, xlsx, pdf, jpg, png</i></p>
          <p style="color: red; display: none;" id="typeError">The file type is not allowed to attach</p>
        </div>
      </div>
      <div class="clearfix">
        <button type="submit" class="btn btn-primary">Add Attachment</button>
      </div>
    </div>

    
  </form>
  <div class="clearfix"></div>
  <div class="row" style="margin-top: 30px">
    <div class="col-md-12">
      <h5>List Attachment</h5>
      <div class="clearfix"></div>
      <table style="width: 100%">
        <thead>
          <tr>
            <th>No</th>
            <th>File</th>
            <th>#</th>
          </tr>
        </thead>
        <tbody>
          <?php $n = 0;?>
          <?php foreach ($manage_attachment as $i => $detail):?>
            <?php $n++;?>
            <tr>
              <td><?=$n?></td>
              <td><a href="<?=base_url().$detail['file']?>" target="_blank"><?=$detail['file'];?></a></td>
              <td>
                <a href="<?=site_url($module['route'] .'/delete_attachment_in_db/'. $detail['id'].'/'.$id_poe);?>" style="color: red" class="btn-delete-att">
                  <i class="fa fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach;?>
          
          <tr>
            <td colspan="3" style="text-align: center;">No Attachment</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="clearfix">
      <button type="button" class="btn btn-default pull-right" onclick="popupClose()">Close</button>
    </div>
  <div class="clearfix"></div>
  <hr>

  <p>
    Material Resource Planning - PT Bali Widya Dirgantara
  </p>
</div>
<?php endblock() ?>

<?php startblock('simple_styles') ?>
<?=link_tag('themes/material/assets/css/theme-default/libs/toastr/toastr.css') ?>
<?php endblock() ?>

<?php startblock('simple_scripts')?>
<?=html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?=html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?=html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<script>
$(document).ready(function(){
  $('form').on('submit', function(e){
    e.preventDefault();
    if($('#attachment')[0].files[0] == undefined){
        alert("You must input an file")
        return false
      }     
    var fsize = $('#attachment')[0].files[0].size; //get file size
    var filename = $("#attachment").val();
    var ftype = filename.replace(/^.*\./, '');
    if (ftype == filename) {
          ftype = '';
    } else {
          ftype = ftype.toLowerCase();
    }
      switch(ftype) {
              case 'png': case 'jpeg': case 'doc': case 'docx': case 'xls' : case 'xlsx' : case 'pdf' : case 'jpg' :
                  break;
              default:
               $("#typeError").css('display','block');
               $("#typeError").html('The file type is not allowed to attach');
               return false
              }

            if(fsize>2097152) {
              $("#typeError").css('display','block');
              $("#typeError").html('File size is to large');
              return false
        }
        $("#typeError").css('display','none');
        $.ajax({
          type: "POST",
          url: $(this).attr('action'),
          data: new FormData( this ),
              processData: false,
              contentType: false,
          success: function(response){
            var data = jQuery.parseJSON(response);
            if (data.status == 1){
              window.location.reload()
            }else{
              $("#typeError").css('display','block');
              $("#typeError").html('Failed to add attachment');
            }
          },
          error: function (xhr, ajaxOptions, thrownError) {
                console.log(xhr.status);
                console.log(xhr.responseText);
                console.log(thrownError);
            }
        });  
  });

  // $('.btn-delete-att').on('click',function(e){
  //   $("#typeError").css('display','none');
  //   $.ajax({
  //     type: "GET",
  //     url: $(this).data('href'),
  //     // data: new FormData( this ),
  //     processData: false,
  //     contentType: false,
  //     success: function(response){
  //       var data = jQuery.parseJSON(response);
  //       if (data.status == 1){
  //         window.location.reload()
  //       }else{
  //         $("#typeError").css('display','block');
  //         $("#typeError").html('Failed to delete attachment');
  //       }
  //     },
  //     error: function (xhr, ajaxOptions, thrownError) {
  //       console.log(xhr.status);
  //       console.log(xhr.responseText);
  //       console.log(thrownError);
  //     }
  //   });
  // });

})
</script>
<?php endblock() ?>
