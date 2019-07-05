<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('usage_format_number')) {
  function usage_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['usage']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'MS' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

// if ( ! function_exists('usage_last_number')) {
//   function usage_last_number()
//   {
//     $div  = config_item('document_format_divider');
//     $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
//     $mod  = config_item('module');
//     $year = date('Y');

//     $CI =& get_instance();

//     $CI->db->select('code');
//     $CI->db->from( 'tb_master_item_categories' );
//     $CI->db->where('category', $_SESSION['usage']['category']);

//     $query  = $CI->db->get();
//     $row    = $query->unbuffered_row();
//     $format = $div . 'MS' . $div . $row->code . $base . $div . $year;

//     $CI->db->select_max('document_number', 'last_number');
//     $CI->db->from('tb_issuances');
//     $CI->db->like('document_number', $format, 'before');

//     $query  = $CI->db->get();
//     $row    = $query->unbuffered_row();
//     $last   = $row->last_number;
//     $number = substr($last, 0, 6);//0 6
//     $next   = $number + 1;
//     $return = sprintf('%06s', $next);

//     return $return;
//   }

// }

if ( ! function_exists('usage_last_number')) {
  function usage_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['usage']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'MS' . $div . $row->code . $base . $div . $year;

    // $CI->db->select_max('document_number', 'last_number');
    // $CI->db->from('tb_issuances');
    // $CI->db->like('document_number', $format, 'before');
    $CI->db->order_by('id',"desc")
        ->limit(1)
        ->like('document_number', $format, 'before')
        ->from('tb_issuances');
        //->row();
   //$CI->db->query('') SELECT TOP 1 * FROM Table ORDER BY ID DESC

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->document_number;
    if($_SESSION['usage']['category']=='BAHAN BAKAR'){
        $number = substr($last, 1, 5);//0 6
    }else{
        $number = substr($last, 2, 4);//0 6
    }    
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

if ( ! function_exists('getMs')) {
  function getMs($group,$date)
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

if ( ! function_exists('adj_last_number')) {
  function adj_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['adj']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'ADJ' . $div . $row->code . $base . $div . $year;

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_stock_adjustments');
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

if ( ! function_exists('adj_format_number')) {
  function adj_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['adj']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'ADJ' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

