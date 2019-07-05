<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GRN_Form
{
    protected $CI;
    protected $item_name;
    protected $item_code;
    protected $item_model;
    protected $group_name;
    protected $unit_measurement;
    protected $minimum_stock;
    protected $notes;

    public function __construct($params = NULL)
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();

        $this->item_name = ( isset($params['entity']->item_name) )
            ? $params['entity']->item_name
            : NULL;

        $this->item_code = ( isset($params['entity']->item_code) )
            ? $params['entity']->item_code
            : NULL;

        $this->item_model = ( isset($params['entity']->item_model) )
            ? $params['entity']->item_model
            : NULL;

        $this->group_name = ( isset($params['entity']->group_name) )
            ? $params['entity']->group_name
            : NULL;

        $this->unit_measurement = ( isset($params['entity']->unit_measurement) )
            ? $params['entity']->unit_measurement
            : NULL;

        $this->minimum_stock = ( isset($params['entity']->minimum_stock) )
            ? $params['entity']->minimum_stock
            : NULL;

        $this->notes = ( isset($params['entity']->notes) )
            ? $params['entity']->notes
            : NULL;
    }

    //... item_name
    public function item_name_field()
    {
        return form_input('item_name',
            set_value('item_name', $this->item_name),
            array(
                'class' => 'form-control',
                'id'    => 'item_name',
            ));
    }

    //... item_code
    public function item_code_field()
    {
        return form_input('item_code',
            set_value('item_code', $this->item_code),
            array(
                'class' => 'form-control',
                'id'    => 'item_code',
            ));
    }

    public function item_code_old_field()
    {
        return form_hidden('item_code_old', $this->item_code);
    }

    //... item_model
    public function item_model_field()
    {
        return form_input('item_model',
            set_value('item_model', $this->item_model),
            array(
                'class' => 'form-control',
                'id'    => 'item_model',
            ));
    }

    //... group_name
    public function group_name_field()
    {
        return form_input('group_name',
            set_value('group_name', $this->group_name),
            array(
                'class' => 'form-control',
                'id'    => 'group_name',
            ));
    }

    //... unit_measurement
    public function unit_measurement_field()
    {
        return form_input('unit_measurement',
            set_value('unit_measurement', $this->unit_measurement),
            array(
                'class' => 'form-control',
                'id'    => 'unit_measurement',
            ));
    }

    //... minimum_stock
    public function minimum_stock_field()
    {
        return form_input('minimum_stock',
            set_value('minimum_stock', $this->minimum_stock),
            array(
                'class' => 'form-control',
                'id'    => 'minimum_stock',
            ));
    }

    //... notes
    public function notes_field()
    {
        return form_textarea('notes',
            set_value('notes', $this->notes),
            array(
                'class' => 'form-control',
                'id'    => 'notes',
            ));
    }

    //... submit buttons
    public function submit_buttons($class = 'btn btn-primary')
    {
        return array(
            array(
                'name'  => 'save_and_close',
                'value' => lang('submit_save_and_close'),
                'class' => $class
            ),
            array(
                'name'  => 'save_and_stay',
                'value' => lang('submit_save_and_stay'),
                'class' => $class
            ),
        );
    }

    public function build_form_elements($include_hidden = FALSE, $include_submit = TRUE)
    {
        $form['field']['item_name']         = $this->item_name_field();
        $form['field']['item_code']         = $this->item_code_field();
        $form['field']['item_model']        = $this->item_model_field();
        $form['field']['group_name']        = $this->group_name_field();
        $form['field']['unit_measurement']  = $this->unit_measurement_field();
        $form['field']['minimum_stock']     = $this->minimum_stock_field();
        $form['field']['notes']             = $this->notes_field();

        if ($include_hidden){
            $form['hidden']['item_code_old']     = $this->item_code_old_field();
        }

        if ($include_submit)
            $form['submit'] = $this->submit_buttons();

        return $form;
    }
}
