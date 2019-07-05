<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('receipt_format_number')) {
  function receipt_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['receipt']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'GRN' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

if ( ! function_exists('receipt_last_number')) {
  function receipt_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['receipt']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'GRN' . $div . $row->code . $base . $div . $year;

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_doc_receipts');
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
    $CI->db->from('tb_doc_deliveries');
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

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_doc_usages');
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

if ( ! function_exists('shipment_format_number')) {
  function shipment_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['shipment']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'SD' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

if ( ! function_exists('shipment_last_number')) {
  function shipment_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['shipment']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'SD' . $div . $row->code . $base . $div . $year;

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_doc_shipments');
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

if ( ! function_exists('return_format_number')) {
  function return_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['return']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'SD' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

if ( ! function_exists('return_last_number')) {
  function return_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['return']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'SD' . $div . $row->code . $base . $div . $year;

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_doc_returns');
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

/**
 * Helper for SD document
 */
if ( ! function_exists('pr_format_number')) {
  function pr_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['pr']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();

    $return = $div . 'PR' . $div . $row->code . $base . $div . $year;

    return $return;
  }
}

if ( ! function_exists('pr_last_number')) {
  function pr_last_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');

    $CI =& get_instance();

    $connection = $CI->load->database('budgetcontrol', TRUE);

    $CI->db->select('code');
    $CI->db->from( 'tb_master_item_categories' );
    $CI->db->where('category', $_SESSION['pr']['category']);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $format = $div . 'PR' . $div . $row->code . $base . $div . $year;

    $connection->select_max('pr_number', 'last_number');
    $connection->from('tb_stocks_purchase_requisitions');
    $connection->like('pr_number', $format, 'before');

    $query  = $connection->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}

if ( ! function_exists('adj_format_number')) {
  function usage_format_number()
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


