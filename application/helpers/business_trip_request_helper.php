<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('travel_on_duty_format_number')) {
  function travel_on_duty_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');
    $month = date('m');

    $return = $div . 'SPD' . $div . 'BWD-BIFA' . $div .$month .$div .$year;

    return $return;
  }
}

if ( ! function_exists('travel_on_duty_last_number')) {
  function travel_on_duty_last_number()
  {
    $CI =& get_instance();

    $format = travel_on_duty_format_number();

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_business_trip_purposes');
    $CI->db->like('document_number', $format, 'before');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}
if ( ! function_exists('kurs_dollar')) {
  function kurs_dollar()
  {

    $CI =& get_instance();

    // $CI->db->select('kurs_dollar');
    // $CI->db->from( 'tb_master_kurs_dollar' );
    // $CI->db->where('date', $date);

    // $query  = $CI->db->get();
    // $row    = $query->unbuffered_row();
    // $kurs_dollar   = $row->kurs_dollar;

    $CI->db->order_by('date', 'desc')->limit(1)->from('tb_master_kurs_dollar');
    // $CI->db->from('tb_master_kurs_dollar');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $kurs_dollar   = $row->kurs_dollar;
    

    return $kurs_dollar;
  }
}

if ( ! function_exists('tgl_kurs')) {
  function tgl_kurs($date)
  {

    $CI =& get_instance();
    $kurs_dollar = 0;
    $tanggal = $date;

    while ($kurs_dollar==0) {
        // $CI->db->select('kurs_dollar');
        // $CI->db->from( 'tb_master_kurs_dollar' );
        // $CI->db->where('date', $date);

        // $query  = $CI->db->get();
        // $row    = $query->unbuffered_row();
        // $kurs_dollar   = $row->kurs_dollar;

    
        $CI->db->select('kurs_dollar');
        $CI->db->from( 'tb_master_kurs_dollar' );
        $CI->db->where('date', $tanggal);

        $query = $CI->db->get();

        if($query->num_rows() > 0 ){
            $row    = $query->unbuffered_row();
            $kurs_dollar   = $row->kurs_dollar;
        }else{
            $kurs_dollar=0;
        }
        $tgl=strtotime('-1 day',strtotime($tanggal));
        $tanggal = date('Y-m-d', $tgl);
    }

    return $kurs_dollar;
    
  }
}

if ( ! function_exists('getGrn')) {
  function getGrn($group,$date)
  {

    $CI =& get_instance();

    // $CI->db->select('kurs_dollar');
    $CI->db->from( 'tb_konsolidasi_ms_grn' );
    $CI->db->where('date', $date);
    $CI->db->where('group', $group);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    

    return $row;
  }
}

if ( ! function_exists('isKonsolidasiDateExists')) {
  function isKonsolidasiDateExists($group,$date)
  {

    $CI =& get_instance();

    // $CI->db->select('kurs_dollar');
    $CI->db->from( 'tb_konsolidasi_ms_grn' );
    $CI->db->where('date', $date);
    $CI->db->where('group', $group);

    $query  = $CI->db->get();
    return ( $query->num_rows() > 0 ) ? true : false;
  }
}

if ( ! function_exists('getIdJurnal')) {
  function getIdJurnal($no_grn)
  {

    $CI =& get_instance();

    $CI->db->select('id');
    $CI->db->from( 'tb_jurnal' );
    $CI->db->where('no_jurnal', $no_grn);
    // $CI->db->where('group', $group);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    

    return $row->id;
  }
}

if ( ! function_exists('getReferenceIpcByPoItemId')) {
  function getReferenceIpcByPoItemId($po_item_id)
  {

    $CI =& get_instance();

    $CI->db->select('reference_ipc');
    $CI->db->from('tb_inventory_purchase_requisition_details' );
    $CI->db->join('tb_purchase_order_items','tb_inventory_purchase_requisition_details.id=tb_purchase_order_items.inventory_purchase_request_detail_id');
    $CI->db->join('tb_po_item', 'tb_purchase_order_items.id = tb_po_item.poe_item_id');
    $CI->db->where('tb_po_item.id', $po_item_id);
    // $CI->db->where('group', $group);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->reference_ipc;

    return $return;
  }
}

