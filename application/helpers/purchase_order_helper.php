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
    $return = '/PO/'. $category['code'] .'/'. date('Y');

    return $return;
  }
}

if ( ! function_exists('order_last_number')) {
  function order_last_number($category = NULL)
  {
    $CI =& get_instance();

    if ($category === NULL){
      $category = $_SESSION['order']['category'];
    }

    $format = order_format_number($category);

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_purchase_orders');
    $CI->db->like('tb_purchase_orders.document_number', $format, 'before');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}
