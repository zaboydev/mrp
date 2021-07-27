<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('find_poe_number')) {
  function find_poe_number($id)
  {
    $CI =& get_instance();

    $CI->db->select('tb_purchase_order_evaluation_items.document_number');
    $CI->db->from('tb_purchase_order_evaluation_items_vendors');
    $CI->db->join('tb_purchase_order_evaluation_items', 'tb_purchase_order_evaluation_items.id = tb_purchase_order_evaluation_items_vendors.poe_item_id');
    $CI->db->where('tb_purchase_order_evaluation_items_vendors.id', $id);

    $query  = $CI->db->get();
    $order  = $query->unbuffered_row('array');
    $return = $order['document_number'];

    return $return;
  }
}

if ( ! function_exists('find_budget_setting')) {
  function find_budget_setting($name, $group = NULL)
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->from('tb_settings');
    $connection->where('setting_name', $name);

    if ($group !== NULL){
      $connection->where('setting_group', $group);
    }

    $query    = $connection->get();
    $setting  = $query->unbuffered_row('array');
    $return   = $setting['setting_value'];

    return $return;
  }
}

if ( ! function_exists('find_product_category')) {
  function find_product_category($name)
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->from('tb_product_categories');
    $connection->where('UPPER(category_name)', strtoupper($name));

    $query  = $connection->get();
    $return = $query->unbuffered_row('array');

    return $return;
  }
}

if ( ! function_exists('print_person_name')) {
  function print_person_name($username)
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->select('real_name');
    $connection->from('tb_users');
    $connection->where('username', $username);

    $query  = $connection->get();

    if ($query->num_rows() > 0){
      $user   = $query->unbuffered_row('array');
      $return = $user['real_name'];
    } else {
      $return = $username;
    }

    return $return;
  }
}

if ( ! function_exists('order_format_number')) {
  function order_format_number($category = NULL)
  {
    $CI =& get_instance();

    if ($category === NULL){
      $category = $_SESSION['order']['category'];
    }

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('tb_master_item_categories.category', $category);

    $query    = $CI->db->get();
    $category = $query->unbuffered_row('array');

    // $return = '/PO/'. $category['code'] .'/'. find_budget_setting('Active Year');
    $return = 'POM';

    return $return;
  }
}

if ( ! function_exists('order_last_number')) {
  function order_last_number($format,$category = NULL)
  {
    $CI =& get_instance();

    if ($category === NULL){
      $category = $_SESSION['order']['category'];
    }

    // $format = order_format_number($category);

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->like('tb_po.document_number', $format, 'after');
    $CI->db->from('tb_po');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 3, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}

if (!function_exists('tgl_kurs')) {
    function tgl_kurs($date)
    {

      $CI = &get_instance();
      $kurs_dollar = 0;
      $tanggal = $date;

      while ($kurs_dollar == 0) {
        // $CI->db->select('kurs_dollar');
        // $CI->db->from( 'tb_master_kurs_dollar' );
        // $CI->db->where('date', $date);

        // $query  = $CI->db->get();
        // $row    = $query->unbuffered_row();
        // $kurs_dollar   = $row->kurs_dollar;


        $CI->db->select('kurs_dollar');
        $CI->db->from('tb_master_kurs_dollar');
        $CI->db->where('date', $tanggal);

        $query = $CI->db->get();

        if ($query->num_rows() > 0) {
          $row    = $query->unbuffered_row();
          $kurs_dollar   = $row->kurs_dollar;
        } else {
          $kurs_dollar = 0;
        }
        $tgl = strtotime('-1 day', strtotime($tanggal));
        $tanggal = date('Y-m-d', $tgl);
      }

      return $kurs_dollar;
    }
}

if ( ! function_exists('getReferenceIpc')) {
  function getReferenceIpc($id,$tipe)
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->select('reference_ipc');
    if($tipe=='capex'){
      $connection->from('tb_capex_purchase_requisition_details');
    }
    if($tipe=='inventory'){
      $connection->from('tb_inventory_purchase_requisition_details');
    }
    if($tipe=='expense'){
      $connection->from('tb_expense_purchase_requisition_details');
    }
    $connection->where('id', $id);

    $query  = $connection->get();
    $row    = $query->unbuffered_row();
    $return = $row->reference_ipc;

    return $return;
  }
}


