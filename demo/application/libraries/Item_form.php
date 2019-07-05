<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_form
{
    protected $CI;
    protected $description;
    protected $code;
    // protected $item_model;
    protected $group;
    protected $unit_measurement;
    protected $minimum_stock;
    // protected $notes;

    public function __construct($params = NULL)
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();

        $this->description = ( isset($params['entity']->description) )
            ? $params['entity']->description
            : NULL;

        $this->code = ( isset($params['entity']->code) )
            ? $params['entity']->code
            : NULL;

        // $this->item_model = ( isset($params['entity']->item_model) )
        //     ? $params['entity']->item_model
        //     : NULL;

        $this->group = ( isset($params['entity']->group) )
            ? $params['entity']->group
            : NULL;

        $this->unit_measurement = ( isset($params['entity']->unit_measurement) )
            ? $params['entity']->unit_measurement
            : NULL;

        $this->minimum_stock = ( isset($params['entity']->minimum_stock) )
            ? $params['entity']->minimum_stock
            : NULL;

        // $this->notes = ( isset($params['entity']->notes) )
        //     ? $params['entity']->notes
        //     : NULL;
    }

    //... item_name
    public function description_field()
    {
        return form_input('description',
            set_value('description', $this->description),
            array(
                'class' => 'form-control',
                'id'    => 'description',
            ));
    }

    //... item_code
    public function code_field()
    {
        return form_input('code',
            set_value('code', $this->code),
            array(
                'class' => 'form-control',
                'id'    => 'code',
            ));
    }

    public function code_old_field()
    {
        return form_hidden('code_old', $this->code);
    }

    //... item_model
    // public function item_model_field()
    // {
    //     return form_input('item_model',
    //         set_value('item_model', $this->item_model),
    //         array(
    //             'class' => 'form-control',
    //             'id'    => 'item_model',
    //         ));
    // }

    //... group_name
    public function item_group_field()
    {
        return form_input('group',
            set_value('group', $this->group),
            array(
                'class' => 'form-control',
                'id'    => 'group',
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
    // public function notes_field()
    // {
    //     return form_textarea('notes',
    //         set_value('notes', $this->notes),
    //         array(
    //             'class' => 'form-control',
    //             'id'    => 'notes',
    //         ));
    // }

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
        $form['field']['group']        = $this->item_group_field();
        $form['field']['code']              = $this->code_field();
        $form['field']['description']       = $this->description_field();
        // $form['field']['item_model']     = $this->item_model_field();
        $form['field']['unit_measurement']  = $this->unit_measurement_field();
        $form['field']['minimum_stock']     = $this->minimum_stock_field();
        // $form['field']['notes']          = $this->notes_field();

        if ($include_hidden){
            $form['hidden']['code_old']     = $this->code_old_field();
        }

        if ($include_submit)
            $form['submit'] = $this->submit_buttons();

        return $form;
    }
}
