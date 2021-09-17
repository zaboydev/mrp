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
  function request_format_number($code)
  {
    
    // $return = '/Inv/'.$code.'/'.date('Y');
    $return = '/Inv/'.$code.'/'.find_budget_setting('Active Year');

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

    $query        = $connection->get();
    if($query->num_rows() > 0){      
      $request      = $query->unbuffered_row('array');
      $last_number  = $request['last_number'];
      $return       = $last_number + 1;
    }else{
      $return = 1;
    }

    return $return;
  }

  if ( ! function_exists('findCostCenter')) {
    function findCostCenter($annual_cost_center_id)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('cost_center_code','cost_center_name','department_id'));
      $connection->from( 'tb_cost_centers' );
      $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
      $connection->where('tb_annual_cost_centers.id', $annual_cost_center_id);

      $query    = $connection->get();
      $cost_center = $query->unbuffered_row('array');

      // $return = '/INV/'. $category['code'] .'/'. find_budget_setting('Active Year');

      //edit
      

      return $cost_center;
    }
  }
}
