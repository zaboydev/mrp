<?=form_open(current_url(), array('class' => 'form-horizontal','autocomplete' => 'off'));?>
<div class="box-body">
    <?php
    if ( $this->session->flashdata('alert') )
        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
    ?>

    <div class="form-group <?php echo (form_error('main_warehouse')) ? 'has-error' : '';?>">
        <label for="main_warehouse" class="col-sm-3 control-label">Main Base</label>
        <div class="col-sm-9">
            <select name="main_warehouse" id="main_warehouse" class="form-control" required="required">
                <option value="" <?=($main_warehouse == null) ? 'selected' : '';?>>-- Select Warehouse</option>

                <?php foreach ($warehouses as $warehouse):?>
                    <option value="<?=$warehouse->code;?>" <?=($warehouse->code == $main_warehouse) ? 'selected' : '';?>>
                        <?=$warehouse->code;?>
                    </option>
                <?php endforeach;?>
            </select>
            <?php echo form_error('main_warehouse', '<span class="help-block text-danger">', '</span>'); ?>
        </div>
    </div>
</div>
<div class="box-footer">
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
</div>
<?=form_close();?>
