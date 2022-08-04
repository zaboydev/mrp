<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('delivery_format_number')) {
  function delivery_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['delivery']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'DP' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

if ( ! function_exists('delivery_last_number')) {
  function delivery_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['delivery']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'DP' . $div . $row->code . $base . $div . $year;

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_internal_delivery');
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

if ( ! function_exists('shipping_delivery_format_number')) {
  function shipping_delivery_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['shipping_internal']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'SID' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

if ( ! function_exists('shipping_delivery_last_number')) {
  function shipping_delivery_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['shipping_internal']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'DP' . $div . $row->code . $base . $div . $year;

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_returns');
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
