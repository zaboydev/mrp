<div class="widget">
    <form class="widget-body" method="get" action="<?=current_url();?>">
        <fieldset>
            <div class="form-group">
                <label for="period">Year</label>
                <select class="form-control" name="year" id="year">
                  <option value="<?=date('Y') - 2;?>" <?=($year == date('Y') - 2) ? 'selected' : '';?>><?=date('Y') - 2;?></option>
                  <option value="<?=date('Y') - 1;?>" <?=($year == date('Y') - 1) ? 'selected' : '';?>><?=date('Y') - 1;?></option>
                  <option value="<?=date('Y');?>" <?=($year == date('Y')) ? 'selected' : '';?>><?=date('Y');?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="period">Month</label>
                <select class="form-control" name="month" id="month">
                  <option value="1" <?=($month == 1) ? 'selected' : '';?>>January</option>
                  <option value="2" <?=($month == 2) ? 'selected' : '';?>>February</option>
                  <option value="3" <?=($month == 3) ? 'selected' : '';?>>March</option>
                  <option value="4" <?=($month == 4) ? 'selected' : '';?>>April</option>
                  <option value="5" <?=($month == 5) ? 'selected' : '';?>>May</option>
                  <option value="6" <?=($month == 6) ? 'selected' : '';?>>June</option>
                  <option value="7" <?=($month == 7) ? 'selected' : '';?>>July</option>
                  <option value="8" <?=($month == 8) ? 'selected' : '';?>>August</option>
                  <option value="9" <?=($month == 9) ? 'selected' : '';?>>September</option>
                  <option value="10" <?=($month == 10) ? 'selected' : '';?>>October</option>
                  <option value="11" <?=($month == 11) ? 'selected' : '';?>>November</option>
                  <option value="12" <?=($month == 12) ? 'selected' : '';?>>December</option>
                </select>
            </div>

            <div class="form-group">
                <label for="group">Group</label>
                <select class="form-control" name="group" id="group">
                  <option value="ALL" >All Group</option>

                  <?php foreach ($groups as $group):?>
                    <option value="<?=$group['group'];?>" <?=($group->group == $group['group']) ? 'selected' : '';?>><?=$group['description'];?></option>
                  <?php endforeach;?>
                </select>
            </div>

            <div class="form-group">
                <label for="warehouse">Warehouse</label>
                <select class="form-control" name="warehouse" id="warehouse">
                  <option value="GENERAL">ALL BASES</option>

                  <?php foreach ($warehouses as $base):?>
                    <option value="<?=$base['warehouse'];?>" <?=($warehouse == $base['warehouse']) ? 'selected' : '';?>>
                      <?=$base['warehouse'];?>
                    </option>
                  <?php endforeach;?>
                </select>
            </div>

            <div class="form-group">
                <label for="condition">Item Condition</label>
                <select class="form-control" name="condition" id="condition">
                  <?php foreach (config_item('condition') as $key => $value):?>
                    <option value="<?=$key;?>" <?=($condition == $key) ? 'selected' : '';?>><?=$value;?></option>
                  <?php endforeach;?>
                </select>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Get Report">
            </div>
        </fieldset>
    </form>
</div>
