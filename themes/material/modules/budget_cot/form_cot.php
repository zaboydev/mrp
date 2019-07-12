<?php include 'themes/material/page.php' ?>
<?php startblock('page_head_tools') ?>
  <?php $this->load->view('material/templates/datatable_tools') ?>
<?php endblock() ?>
<?php startblock('page_body') ?>
<input type="hidden" id="onCot" value = "1" name="">
	<div class="row">
		<div class="col-md-12">
			<div class="col-md-4">
				<h4 style="text-align: center;">Target Hour : <?=$hour ?> hours</h4>
			</div>
			<div class="col-md-4">
				<h4 style="text-align: center;">Year : <?=$year ?></h4>
			</div>
			<div class="col-md-4">
				<h4 style="text-align: center;">Kelipatan : <?=$kelipatan ?></h4>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" style="padding: 30px">
        <div class="newoverlay" style="" id="loadingScreen" style="display: none;">
              <i class="fa fa-refresh fa-spin"></i>
            </div>
            <div class="col-md-3">
                <div class="dataTables_length">
                  <label>Show 
                    <select id="limit" name="limit">
                      <option value="10">10</option>
                      <option value="25">25</option>
                      <option value="50">50</option>
                      <option value="100">100</option>
                      <option value="250">250</option>
                      <option value="500">500</option>
                  </select> 
                  entries
                </label>
              </div>
            </div>
             <table class="table table-bordered">
               <thead>
               	<tr>
               		<th width="5%">#</th>
               		<th width="5%">No</th>
                  <th width="25%">Part Number</th>
               		<th width="25%">Item</th>
               		<th width="10%">Standard Quantity</th>
                  <th width="5%">start month</th>
                  <th width="5%">end month</th>
               	</tr>
               </thead>
               <tbody id="listView">


              </tbody>
          	</table>
            <div class="box-footer clearfix">
              <div class="col-sm-6 text-left" id="paginContent">
                  <ul class="pagination" id="pagination"></ul>
                </div>
                <div class="col-sm-6 text-right" id="bottomLabel"></div>
            </div>
		</div>
	</div>
<?php endblock() ?>
<?php startblock('actions_right') ?>
  <div class="section-floating-action-row">
      <div class="btn-group dropup">
        <button type="button" class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction" id="btn-save-data" >
          <i class="md md-save"></i>
          <small class="top right">Save Data</small>
        </button>
      </div>
  </div>
<?php endblock() ?>
<?php startblock('datafilter') ?>
<form method="post" class="form force-padding">
<input type="hidden" id="hour" value="<?=$hour ?>" name="hour">
<input type="hidden" id="year" value="<?=$year ?>" name="year">
<input type="hidden" id="id_kelipatan" value="<?=$id_kelipatan ?>" name="id_kelipatan">
<input type="hidden" id="id_kategori" value="<?=$id_kategori ?>">
<input type="hidden" id="kelipatan" value="<?=$kelipatan ?>" name="">
  <div class="form-group">
    <label for="category">Category</label>
    <select class="form-control input-sm" id="category" name="category">
      <option value="all" <?=($selected_category == 'all') ? 'selected' : '';?>>-- ALL CATEGORIES --</option>
      <?php foreach (config_item('auth_inventory') as $category):?>
        <option value="<?=$category;?>" <?=($category == $id_kategori) ? 'selected' : '';?>>
          <?=$category;?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn btn-flat btn-danger btn-block ink-reaction">Generate</button>
</form>
<?php endblock() ?>



