<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Items_in_stock_lib
{
    protected $CI;
    protected $entity;
    protected $item_id;
    protected $code;
    protected $part_number;
    protected $alternate_part_number;
    protected $item_serial;
    protected $manufacture;
    protected $expired_date;
    protected $vendor_id;
    protected $purchase_price;
    protected $status;
    protected $shelf_id;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct($entity = NULL)
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
        $this->entity = $entity;

        $this->item_id          = ( isset($this->entity->item_id) ) ? $this->entity->item_id : NULL;
        $this->code             = ( isset($this->entity->code) ) ? $this->entity->code : NULL;
        $this->part_number      = ( isset($this->entity->part_number) ) ? $this->entity->part_number : NULL;
        $this->alternate_part_number  = ( isset($this->entity->alternate_part_number) ) ? $this->entity->alternate_part_number : NULL;
        $this->item_serial    = ( isset($this->entity->item_serial) ) ? $this->entity->item_serial : NULL;
        $this->manufacture      = ( isset($this->entity->manufacture) ) ? $this->entity->manufacture : NULL;
        $this->expired_date     = ( isset($this->entity->expired_date) ) ? $this->entity->expired_date : NULL;
        $this->vendor_id        = ( isset($this->entity->vendor_id) ) ? $this->entity->vendor_id : NULL;
        $this->purchase_price   = ( isset($this->entity->purchase_price) ) ? $this->entity->purchase_price : NULL;
        $this->status           = ( isset($this->entity->status) ) ? $this->entity->status : NULL;
        $this->shelf_id         = ( isset($this->entity->shelf_id) ) ? $this->entity->shelf_id : NULL;
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

    public function code_rules()
    {
        return array(
                'field' => 'code',
                'label' => lang('label_code'),
                'rules' => array('trim', 'required', 'max_length[255]',
                    array('callable_unique_code', 
                        array($this->CI->items_in_stock_model, 'callable_unique_code')
                    ))
            );
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

    public function part_number_rules()
    {
        return array(
                'field' => 'part_number',
                'label' => lang('label_part_number'),
                'rules' => array('trim', 'max_length[255]',
                    array('callable_unique_part_number', 
                        array($this->CI->items_in_stock_model, 'callable_unique_part_number')
                    ))
            );
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

    public function alternate_part_number_rules()
    {
        return array(
                'field' => 'alternate_part_number',
                'label' => lang('label_alternate_part_number'),
                'rules' => array('trim', 'max_length[255]',
                    array('callable_unique_alternate_part_number', 
                        array($this->CI->items_in_stock_model, 'callable_unique_alternate_part_number')
                    ))
            );
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

    public function item_serial_rules()
    {
        return array(
                'field' => 'item_serial',
                'label' => lang('label_item_serial'),
                'rules' => array('trim', 'max_length[255]',
                    array('callable_unique_item_serial', 
                        array($this->CI->items_in_stock_model, 'callable_unique_item_serial')
                    ))
            );
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

    public function manufacture_rules()
    {
        return array(
                'field' => 'manufacture',
                'label' => lang('label_manufacture'),
                'rules' => array('trim', 'max_length[255]')
            );
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

    public function expired_date_rules()
    {
        return array(
                'field' => 'expired_date',
                'label' => lang('label_expired_date'),
                'rules' => array('trim', 'max_length[255]')
            );
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

    public function vendor_id_rules()
    {
        return array(
                'field' => 'vendor_id',
                'label' => lang('label_vendor_id'),
                'rules' => array('trim', 'required', 'max_length[255]')
            );
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

    public function purchase_price_rules()
    {
        return array(
                'field' => 'purchase_price',
                'label' => lang('label_purchase_price'),
                'rules' => array('trim', 'required', 'max_length[255]')
            );
    }

    //... status
    public function status_field()
    {
        return form_input('status',
            set_value('status', $this->status),
            array(
                'class' => 'form-control',
                'id'    => 'status',
            ));
    }

    public function status_rules()
    {
        return array(
                'field' => 'status',
                'label' => lang('label_status'),
                'rules' => array('trim', 'max_length[255]')
            );
    }

    //... shelf_id
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

    public function shelf_id_rules()
    {
        return array(
                'field' => 'shelf_id',
                'label' => lang('label_shelf_id'),
                'rules' => array('trim', 'max_length[255]')
            );
    }
}
