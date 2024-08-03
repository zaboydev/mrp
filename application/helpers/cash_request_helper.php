<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('cash_request_format_number')) {
  function cash_request_format_number()
  {
    
    // $return = '/Exp/'.$cost_center_code.'/'.date('Y');
    $return = '/CR/'.date('Y');

    return $return;
  }
}

if ( ! function_exists('cash_request_order_number')) {
    function cash_request_order_number()
    {
        $CI =& get_instance();
        $format = cash_request_format_number();

        $CI->db->select_max('document_number', 'last_number');
        $CI->db->from('tb_cash_request');
        $CI->db->like('document_number', $format);

        $query  = $CI->db->get();
        $row    = $query->unbuffered_row();
        $last   = $row->last_number;
        $number = substr($last, 0, 6);
        $next   = $number + 1;
        $return = sprintf('%06s', $next);

        return $return;
    }
}

