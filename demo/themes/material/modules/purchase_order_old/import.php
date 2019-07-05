<?php echo form_open_multipart('', array(
    'autocomplete' => 'off'
));?>
<div class="box-body">
    <?php if ( $this->session->flashdata('alert') )
        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);?>

    <div class="row">
        <div class="col-sm-5 col-md-4">
            <fieldset>
                <legend>Form</legend>
                <div class="form-group <?php echo (form_error('userfile')) ? 'has-error' : '';?>">
                    <label for="userfile" class="control-label">CSV File</label>
                    <input type="file" name="userfile" id="userfile" placeholder="person_name" required>
                    <?php echo form_error('userfile', '<span class="help-block text-danger">', '</span>'); ?>
                </div>
                <div class="form-group <?php echo (form_error('delimiter')) ? 'has-error' : '';?>">
                    <label for="delimiter" class="control-label">Value Delimiter</label>
                    <div class="radio">
                        <label class="radio-inline">
                            <input type="radio" name="delimiter" id="delimiter[2]" value=";" checked> Semicolon ( ; )
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="delimiter" id="delimiter[1]" value=","> Comma ( , )
                        </label>
                    </div>
                    <?php echo form_error('delimiter', '<span class="help-block text-danger">', '</span>'); ?>
                </div>
            </fieldset>
        </div>
        <div class="col-sm-7 col-md-8">
            <fieldset>
                <legend>Info</legend>
                <ul>
                    <li>The file format should be <code>Comma Separated Value</code> and ending in <code>.csv</code></li>
                    <li><code>Item Name</code> should not be more than 255 characters</li>
                    <li><code>Item Code</code> should not be more than 30 characters and alpha-numeric and not in used</li>
                    <li><code>Model</code> should not be more than 100 characters</li>
                    <li><code>Group Name</code> should not be more than 100 characters</li>
                    <li><code>Unit</code> should not be more than 20 characters</li>
                    <li><code>Min Stock</code> must be filled with numbers only</li>
                    <li>For csv file template, <a href="<?=base_url('uploads/files/item.csv');?>" class="text-danger">download here</a></li>
                </ul>
            </fieldset>
        </div>
    </div>
</div>

<div class="box-footer">
    <input type="submit" class="btn btn-primary" name="save_and_close" value="Import & Save">
    <a class="btn btn-default"
       href="<?=site_url('item', LINK_PROTOCOL);?>">
        Cancel
    </a>
</div>
<?php echo form_close();?>
