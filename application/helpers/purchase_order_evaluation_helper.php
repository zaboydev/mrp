<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('find_pr_number')) {
  function find_pr_number($id)
  {
    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $connection->select('tb_inventory_purchase_requisitions.pr_number');
    $connection->from('tb_inventory_purchase_requisition_details');
    $connection->join('tb_inventory_purchase_requisitions', 'tb_inventory_purchase_requisitions.id = tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id');
    $connection->where('tb_inventory_purchase_requisition_details.id', $id);

    $query  = $connection->get();
    $pr     = $query->unbuffered_row('array');
    $return = $pr['pr_number'];

    return $return;
  }
}

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

if ( ! function_exists('poe_format_number')) {
  function poe_format_number()
  {
    $year = date('Y');
    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('tb_master_item_categories.category', $_SESSION['poe']['category']);

    $query    = $CI->db->get();
    $category = $query->unbuffered_row('array');

    // $return = '/POE/'. $category['code'] .'/'. find_budget_setting('Active Year');
    $return = '/POE/'.date('Y');
    // $return = '/POE/'. $category['code'] .'/'. $year;


    return $return;
  }
}

if ( ! function_exists('poe_last_number')) {
  function poe_last_number()
  {
    
    $CI =& get_instance();

    $format = poe_format_number();

    $CI->db->select_max('evaluation_number', 'last_number');
    $CI->db->from('tb_purchase_orders');
    $CI->db->like('tb_purchase_orders.evaluation_number', $format, 'before');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}

if ( ! function_exists('getStatusPOE')) {
  function getStatusPOE($document_number)
  {
    
    $CI =& get_instance();
    
    $CI->db->from('tb_po_item');
    $CI->db->where('tb_po_item.poe_number', $document_number);
    $num_rows = $CI->db->count_all_results();
    if($num_rows>0){
      $status = 'approved';
    }else{
      $CI->db->select('status');
      $CI->db->from('tb_purchase_orders');
      $CI->db->where('tb_purchase_orders.evaluation_number', $document_number);

      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      $status = $row->status;
    }

    return $status;
  }
}

if ( ! function_exists('generatePartNumber')) {
  function generatePartNumber($kode)
  {

    $CI =& get_instance();

    $CI->db->select_max('part_number', 'last_number');
    $CI->db->from('tb_master_items');
    $CI->db->like('tb_master_items.part_number', $kode.'-', 'after');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, -3);
    $next   = $number + 1;
    $return = sprintf('%03s', $next);

    return $kode.'-'.$return;
  }
}
