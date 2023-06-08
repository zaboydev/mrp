<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('reimbursement_format_number')) {
  function reimbursement_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');
    $month = date('m');

    $return = $div . 'RF' . $div . 'HRD-BIFA' . $div .$month .$div.'BALI'.$div .$year;

    return $return;
  }
}

if ( ! function_exists('reimbursement_last_number')) {
  function reimbursement_last_number()
  {
    $CI =& get_instance();

    $format = reimbursement_format_number();

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_business_trip_purposes');
    $CI->db->like('document_number', $format, 'both');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}

if ( ! function_exists('getNotifRecipientHrManager')) {
  function getNotifRecipientHrManager()
  {
    $CI =& get_instance();

    $head_dept = array();

    foreach (list_user_in_head_department(11) as $head) {
      $head_dept[] = $head['username'];
    }

    $CI->db->select('email');
    $CI->db->from('tb_auth_users');
    $CI->db->where_in('username', $head_dept);
    $query  = $CI->db->get();
    $result = $query->result_array();
    return $result;
  }
}

if ( ! function_exists('get_reimbursement_last_number')) {
  function get_reimbursement_last_number()
  {
    $CI =& get_instance();

    $format = travel_on_duty_format_number();

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_business_trip_purposes');
    $CI->db->like('document_number', $format, 'both');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    // $last   = $row->last_number;
    // $number = substr($last, 0, 6);
    // $next   = $number + 1;
    // $return = sprintf('%06s', $next);

    return $row;
  }

  if ( ! function_exists('get_count_revisi')) {
    function get_count_revisi($document_number)
    {
      $CI =& get_instance();

  
      $CI->db->select('document_number');
      $CI->db->from('tb_reimbursements');
      $CI->db->like('document_number', $document_number, 'both');
  
      $query  = $CI->db->get();
      $row    = $query->num_rows();
      $return = $row;
  
      return $return;
    }
  }
}

