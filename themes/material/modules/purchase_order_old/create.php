<?php
/**
 * @var $fields array
 * @var $submits array
 * @var $hidden array
 */
echo form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form-horizontal'));?>
<div class="box-body">
    <?php if ( $this->session->flashdata('alert') )
        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);?>

    <fieldset>
        <?php foreach ($fields as $name => $input):?>
        <div class="form-group <?=(form_error($name)) ? 'has-error' : '';?>">
            <label for="<?=$name;?>" class="control-label col-sm-3"><?=$input['label'];?></label>
            <div class="col-sm-9">
                <?php
                if ($input['type'] === 'password'){
                    echo form_input($input['attr']);
                } elseif ($input['type'] === 'textarea'){
                    echo form_textarea($input['attr']);
                } else {
                    echo form_input($input['attr']);
                }
                ?>
                <?=form_error($name, '<span class="help-block text-danger">', '</span>'); ?>
            </div>
        </div>
        <?php endforeach;?>
    </fieldset>
</div>

<div class="box-footer">
    <?php
    if (isset($hidden))
        echo form_hidden($hidden);

    foreach ($submits as $submit)
        echo form_submit($submit);

    if (is_granted($acl[$module]['index']))
        echo anchor(site_url('item', LINK_PROTOCOL), 'Cancel', 'class="btn btn-default"');
    ?>
</div>
<?=form_close();?>
