<h4 class="sidebar-title">Filter Data</h4>

<div class="widget">
  <form class="widget-body" method="get" action="<?=current_url();?>">
    <fieldset>
      <div class="form-group">
        <label for="filter_level">Role</label>
        <select class="form-control filter_dropdown" data-column="1" id="filter_level">
          <option value="">
            All Roles
          </option>

          <?php foreach (config_item('levels_and_roles') as $key => $value):?>
            <option value="<?=$key;?>">
              <?=$value;?>
            </option>
          <?php endforeach;?>
        </select>
      </div>

      <div class="form-group">
        <label for="filter_banned">Status</label>
        <select class="form-control filter_dropdown" data-column="6" id="filter_banned">
          <option value="">
            All Status
          </option>

          <option value="0">
            Active
          </option>

          <option value="1">
            Banned
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="filter_last_login">Last Login</label>
        <input type="text" class="form-control filter_daterange" data-column="7" readonly="readonly" id="filter_last_login">
      </div>

      <div class="form-group">
        <label for="filter_last_update">Last Update</label>
        <input type="text" class="form-control filter_daterange" data-column="8" readonly="readonly" id="filter_last_update">
      </div>
    </fieldset>
  </form>
</div>
