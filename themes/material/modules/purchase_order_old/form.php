<?php
/**
 * @var $fields array
 * @var $submits array
 * @var $hidden array
 */
$label_attr = array(
        'class' => 'control-label col-sm-3'
    );

echo form_open(current_url(), array('autocomplete' => 'on', 'class' => 'form-horizontal'));?>
<div class="box-body">
    <?php if ( $this->session->flashdata('alert') )
        render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);?>

    <fieldset>
        <?php foreach ($form['field'] as $name => $input):?>
        <div class="form-group <?=(form_error($name)) ? 'has-error' : '';?>">
            <?=lang('label_'.$name, $name, $label_attr);?>

            <div class="col-sm-9">
                <?=$input;?>

                <?=form_error($name, '<span class="help-block text-danger">', '</span>'); ?>
            </div>
        </div>
        <?php endforeach;?>
    </fieldset>
</div>

<div class="box-footer">
    <?php
    if (isset($form['hidden'])){
        foreach ($form['hidden'] as $hidden)
            echo $hidden;
    }

    foreach ($form['submit'] as $submit)
        echo form_submit($submit);

    if (is_granted($acl[$module]['index']))
        echo anchor(site_url('item_detail/index/'.$item_id, LINK_PROTOCOL), 'Cancel', 'class="btn btn-default"');
    ?>
</div>
<?=form_close();?>
