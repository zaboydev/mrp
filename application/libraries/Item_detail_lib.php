<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item_detail_lib
{
    protected $CI;
    protected $item_id;
    protected $code;
    protected $part_number;
    protected $alternate_part_number;
    protected $item_serial;
    protected $manufacture;
    protected $expired_date;
    protected $vendor_id;
    protected $purchase_price;
    protected $condition;
    protected $location;
    protected $shelf_id;
    protected $aircraft_id;

    public function __construct($params = NULL)
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();

        $this->item_id = ( isset($params['entity']->item_id) )
            ? $params['entity']->item_id
            : ((isset($params['item_id'])) ? $params['item_id'] : NULL);

        $this->code = ( isset($params['entity']->code) )
            ? $params['entity']->code
            : NULL;

        $this->part_number = ( isset($params['entity']->part_number) )
            ? $params['entity']->part_number
            : NULL;

        $this->alternate_part_number = ( isset($params['entity']->alternate_part_number) )
            ? $params['entity']->alternate_part_number
            : NULL;

        $this->item_serial = ( isset($params['entity']->item_serial) )
            ? $params['entity']->item_serial
            : NULL;

        $this->manufacture = ( isset($params['entity']->manufacture) )
            ? $params['entity']->manufacture
            : NULL;

        $this->expired_date = ( isset($params['entity']->expired_date) )
            ? $params['entity']->expired_date
            : NULL;

        $this->vendor_id = ( isset($params['entity']->vendor_id) )
            ? $params['entity']->vendor_id
            : NULL;

        $this->purchase_price = ( isset($params['entity']->purchase_price) )
            ? $params['entity']->purchase_price
            : NULL;

        $this->condition = ( isset($params['entity']->condition) )
            ? $params['entity']->condition
            : NULL;

        $this->location = ( isset($params['entity']->location) )
            ? $params['entity']->location
            : NULL;

        $this->shelf_id = ( isset($params['entity']->shelf_id) )
            ? $params['entity']->shelf_id
            : NULL;

        $this->aircraft_id = ( isset($params['entity']->aircraft_id) )
            ? $params['entity']->aircraft_id
            : NULL;
    }

    //... item_id
    public function item_id_field($value = NULL)
    {
        if ($value === NULL)
            return form_hidden('item_id', $this->item_id);

        return form_hidden('item_id', $value);
    }

    //... code
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

    //... part_number
    public function part_number_field()
    {
        return form_input('part_number',
            set_value('part_number', $this->part_number),
            array(
                'class' => 'form-control',
                'id'    => 'part_number',
            ));
    }

    public function part_number_old_field()
    {
        return form_hidden('part_number_old', $this->part_number);
    }

    //... alternate_part_number
    public function alternate_part_number_field()
    {
        return form_input('alternate_part_number',
            set_value('alternate_part_number', $this->alternate_part_number),
            array(
                'class' => 'form-control',
                'id'    => 'alternate_part_number',
            ));
    }

    public function alternate_part_number_old_field()
    {
        return form_hidden('alternate_part_number_old', $this->alternate_part_number);
    }

    //... item_serial
    public function item_serial_field()
    {
        return form_input('item_serial',
            set_value('item_serial', $this->item_serial),
            array(
                'class' => 'form-control',
                'id'    => 'item_serial',
            ));
    }

    public function item_serial_old_field()
    {
        return form_hidden('item_serial_old', $this->item_serial);
    }

    //... manufacture
    public function manufacture_field()
    {
        return form_input('manufacture',
            set_value('manufacture', $this->manufacture),
            array(
                'class' => 'form-control',
                'id'    => 'manufacture',
            ));
    }

    //... expired_date
    public function expired_date_field()
    {
        return form_input('expired_date',
            set_value('expired_date', $this->expired_date),
            array(
                'class' => 'form-control',
                'id'    => 'expired_date',
            ));
    }

    //... vendor_id
    public function vendor_id_field()
    {
        $this->CI->load->model('vendor_model');

        $vendors = $this->CI->vendor_model->finds();

        $vendor[''] = '---';

        foreach ($vendors as $key => $val) {
            $vendor[$val->id] = $val->vendor_name .' ('. $val->vendor_code .')';
        }

        return form_dropdown('vendor_id',
            $vendor,
            set_value('vendor_id', $this->vendor_id),
            array(
                'class' => 'form-control',
                'id'    => 'vendor_id',
            ));
    }

    //... purchase_price
    public function purchase_price_field()
    {
        return form_input('purchase_price',
            set_value('purchase_price', $this->purchase_price),
            array(
                'class' => 'form-control',
                'id'    => 'purchase_price',
            ));
    }

    //... condition
    public function condition_field()
    {
        return form_input('condition',
            set_value('condition', $this->condition),
            array(
                'class' => 'form-control',
                'id'    => 'condition',
            ));
    }

    //... condition
    public function shelf_id_field()
    {
        $this->CI->load->model('shelf_model');

        $shelfs = $this->CI->shelf_model->finds();

        $shelf[''] = '---';

        foreach ($shelfs as $key => $val) {
            $shelf[$val->id] = $val->shelf_code;
        }

        return form_dropdown('shelf_id',
            $shelf,
            set_value('shelf_id', $this->shelf_id),
            array(
                'class' => 'form-control',
                'id'    => 'shelf_id',
            ));
    }

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

    public function build_form_stock($include_hidden = FALSE, $include_submit = TRUE)
    {
        $form['field']['item_id']          = $this->item_id_field();
        $form['field']['part_number']      = $this->part_number_field();
        $form['field']['alternate_part_number']  = $this->alternate_part_number_field();
        $form['field']['item_serial']    = $this->item_serial_field();
        $form['field']['manufacture']      = $this->manufacture_field();
        $form['field']['expired_date']     = $this->expired_date_field();
        $form['field']['vendor_id']        = $this->vendor_id_field();
        $form['field']['purchase_price']   = $this->purchase_price_field();
        $form['field']['condition']        = $this->condition_field();
        $form['field']['shelf_id']         = $this->shelf_id_field();

        if ($include_hidden){
            $form['hidden']['part_number_old']       = $this->part_number_old_field();
            $form['hidden']['alternate_part_number_old']   = $this->alternate_part_number_old_field();
            $form['hidden']['item_serial_old']     = $this->item_serial_old_field();
        }

        if ($include_submit)
            $form['submit'] = $this->submit_buttons();

        return $form;
    }
}
