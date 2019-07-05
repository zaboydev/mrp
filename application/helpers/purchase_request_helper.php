<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('find_budget_setting')) {
  function find_budget_setting($name)
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->from('tb_settings');
    $connection->where('setting_name', $name);

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

if ( ! function_exists('request_format_number')) {
  function request_format_number()
  {
    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['request']['category']);

    $query    = $CI->db->get();
    $category = $query->unbuffered_row('array');

    // $return = '/INV/'. $category['code'] .'/'. find_budget_setting('Active Year');

    //edit
    $return = '/PRL/'.date('Y');

    return $return;
  }
}

if ( ! function_exists('request_last_number')) {
  function request_last_number()
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->select_max('order_number', 'last_number');
    $connection->from('tb_inventory_purchase_requisitions');
    // $connection->like('tb_inventory_purchase_requisitions.pr_number', find_budget_setting('Active Year'));

    $connection->like('tb_inventory_purchase_requisitions.pr_number', find_budget_setting('Active Year'));
    $query        = $connection->get();
    $request      = $query->unbuffered_row('array');
    $last_number  = $request['last_number'];
    $return       = $last_number + 1;

    // //edit
    // $CI->db->select_max('document_number','last_number');
    // $CI->db->from('tb_purchase_requests');

    // $query  = $CI->db->get();
    // $row    = $query->unbuffered_row();
    // $last   = $row->last_number;
    // $number = substr($last, 0, 6);
    // $next   = $number + 1;
    // $return = sprintf('%06s', $next);


    return $return;
  }
}
