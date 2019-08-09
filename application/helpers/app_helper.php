<?php
/**
 * Helper for build HTML format
 */
if ( ! function_exists('html_open')) {
  function html_open($lang = 'en')
  {
    $string = "<!DOCTYPE html>\n";
    $string.= '<html lang="'.$lang.'">';
    $string.= "\n<head>\n";

    return $string;
  }
}

if ( ! function_exists('html_close')) {
  function html_close()
  {
    $string = "\n</body>\n";
    $string.= '<html>';

    return $string;
  }
}

if ( ! function_exists('html_charset')) {
  function html_charset($charset = 'utf-8')
  {
    $string = '<meta charset="';
    $string.= $charset;
    $string.= '">';
    $string.= "\n";

    return $string;
  }
}

if ( ! function_exists('html_title')) {
  function html_title($title)
  {
    $string = '<title>';
    $string.= $title;
    $string.= ' | BWD Material Resource Planning';
    $string.= "</title>\n";

    return $string;
  }
}

if ( ! function_exists('html_body')) {
  function html_body($class = NULL)
  {
    $string = "</head>\n";
    $string.= '<body class="'.$class.'">';
    $string.= "\n";

    return $string;
  }
}

if ( ! function_exists('html_script')) {
  function html_script($src, $local = TRUE)
  {
    if ($local === TRUE)
      $src = base_url($src);

    $string = '<script src="'.$src.'"></script>';
    $string.= "\n";

    return $string;
  }
}

if ( ! function_exists('numberToMonthName')) {
  function numberToMonthName($monthNumber, $format = 'F')
  {
    $dateObj   = DateTime::createFromFormat('!m', $monthNumber);
    $monthName = $dateObj->format($format);

    return $monthName;
  }
}

/**
* @name Date Change Format
* @note PHP > 5.3
* @params date, from date format, to date format
* @use date_change_format()
*/
if ( ! function_exists('date_change_format')) {
  function date_change_format($setDate, $from = 'F d, Y', $to = 'Y-m-d') {
    if ($setDate != '') {
      $date = DateTime::createFromFormat($from, $setDate);
      return $date->format($to);
    } else {
      return '';
    }
  }
}

if ( ! function_exists('valid_date')) {
  function valid_date($date, $format = 'Y-m-d')
  {
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) === $date;
  }
}

if ( ! function_exists('valid_datetime')) {
  function valid_datetime($datetime, $format = 'Y-m-d H:i:s')
  {
    $d = DateTime::createFromFormat($format, $datetime);

    return $d && $d->format($format) == $datetime;
  }
}

/**
 * Helper for print information
 */
if ( ! function_exists('print_string')) {
  function print_string($string, $null = '', $class = '')
  {
    if (trim($string) === '' || empty($string))
      $string = $null;

    $text = '<span class="'.$class.'">';
    $text.= $string;
    $text.= '</span>';

    return $text;
  }
}

if ( ! function_exists('print_number')) {
  function print_number($number, $decimal = 0, $force_right = TRUE)
  {
    if (trim($number) === '' || empty($number))
      $number = 0;

    if ($force_right === TRUE)
      $text = '<span style="display:block; text-align:right">';
    else
      $text = '<span>';

    $text.= number_format($number, $decimal);
    $text.= '</span>';

    return $text;
  }
}

if ( ! function_exists('print_config')) {
  function print_config($config, $data)
  {
    if (trim($data) === '' || empty($data))
      return 'N/A';

    $conf = config_item($config);

    return $conf[$data];
  }
}

if ( ! function_exists('print_date')) {
  function print_date($date, $format = NULL, $null = 'UNKNOWN')
  {
    // if (trim($date) === '' || empty($date) || (valid_date($date) === FALSE && valid_datetime($date) === FALSE))
    if (trim($date) === '' || empty($date))
      return $null;

    if ($format === NULL)
      $format = 'F d, Y';

    return nice_date($date, $format);
  }
}


/**
 * Helper for security options
 */
if ( ! function_exists('is_granted')) {
  function is_granted($module, $roles)
  {
    if ( isset($module['permission'][$roles]) && in_array(config_item('auth_role'), (array)explode(',', $module['permission'][$roles])) )
      return TRUE;

    return FALSE;
  }
}

if ( ! function_exists('clean_import')) {
  function clean_import($string, $return = NULL)
  {
    $string = preg_replace('/[[:^print:]]/', '', $string); // Replaces non printable characters.
    $string = preg_replace('/[^(\x20-\x7F)]*/','', $string); // Replaces non viewable characters.
    $string = preg_replace('!\s+!', ' ', $string); // Replaces multiple spaces with single one.
    $string = preg_replace('/^([\'"])(.*)\\1$/', '\\2', $string); // replace single or double quotes and ensure that they must match.
    $string = preg_replace('/-+/', '-', $string); // Replaces multiple spaces with single one.
    $string = trim($string);

    return (empty($string)) ? $return : $string;
  }
}

if ( ! function_exists('clean_float')) {
  function clean_float($number, $return = NULL)
  {
    return (is_numeric($number) || $number === 0)
      ? $number
      : $return;
  }
}

if ( ! function_exists('get_setting')) {
  function get_setting($setting_name)
  {
    $CI =& get_instance();

    $CI->db->select('setting_value');
    $CI->db->from('tb_settings');
    $CI->db->where('setting_name', $setting_name);

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->setting_value;

    return $return;
  }
}

if ( ! function_exists('get_vendor_code')) {
  function get_vendor_code($id)
  {
    $CI =& get_instance();

    $CI->db->select('code');
    $CI->db->from('tb_master_vendors');

    if (is_integer($id)){
      $CI->db->where('id', $id);
    } else {
      $CI->db->where('vendor', $id);
    }

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->code;

    return $return;
  }
}

if ( ! function_exists('get_vendor_info')) {
  function get_vendor_info($id)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_vendors');

    if (is_integer($id)){
      $CI->db->where('id', $id);
    } else {
      $CI->db->where('vendor', $id);
    }

    $query  = $CI->db->get();
    $return = $query->unbuffered_row('array');

    return $return;
  }
}

if ( ! function_exists('isWarehouseExists')) {
  function isWarehouseExists($warehouse)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_warehouses');
    $CI->db->where('UPPER(tb_master_warehouses.warehouse)', strtoupper($warehouse));

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('get_warehouse_info')) {
  function get_warehouse_info($id)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_warehouses');

    if (is_integer($id)){
      $CI->db->where('id', $id);
    } else {
      $CI->db->where('warehouse', $id);
    }

    $query  = $CI->db->get();
    $return = $query->unbuffered_row('array');

    return $return;
  }
}

if ( ! function_exists('category_for_vendor_list')) {
  function category_for_vendor_list($vendor)
  {
    $CI =& get_instance();

    $CI->db->select('category');
    $CI->db->from('tb_master_vendor_categories');

    if (is_array($vendor)){
      $CI->db->where_in('vendor', $vendor);
    } else {
      $CI->db->where('vendor', $vendor);
    }

    $CI->db->order_by('category', 'ASC');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['category'];
    }

    return $return;
  }
}

if ( ! function_exists('user_in_category_list')) {
  function user_in_category_list($category)
  {
    $CI =& get_instance();

    $CI->db->select('username');
    $CI->db->from('tb_auth_user_categories');

    if (is_array($category)){
      $CI->db->where_in('category', $category);
    } else {
      $CI->db->where('category', $category);
    }

    $CI->db->order_by('username', 'ASC');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['username'];
    }

    return $return;
  }
}

if ( ! function_exists('isItemCategoryExists')) {
  function isItemCategoryExists($category)
  {
    $CI =& get_instance();

    $CI->db->from('tb_master_item_categories');
    $CI->db->where('UPPER(tb_master_item_categories.category)', strtoupper($category));

    $num_rows = $CI->db->count_all_results();

    return ($num_rows > 0) ? TRUE : FALSE;
  }
}

if ( ! function_exists('available_categories')) {
  function available_categories($group = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('category');
    $CI->db->from('tb_master_item_groups');
    $CI->db->where('status', 'AVAILABLE');

    if ($group !== NULL){
      if (is_array($group)){
        $CI->db->where_in('group', $group);
      } else {
        $CI->db->where('group', $group);
      }
    }

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $return = $row->category;

    return $return;
  }
}

if ( ! function_exists('available_conditions')) {
  function available_conditions()
  {
    $CI =& get_instance();

    $CI->db->select('condition');
    $CI->db->from('tb_master_item_conditions');
    $CI->db->where('status', 'AVAILABLE');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['condition'];
    }

    return $return;
  }
}

if ( ! function_exists('available_user')) {
  function available_user($select = NULL, $level = NULL)
  {
    $CI =& get_instance();

    if ($select !== NULL){
      $CI->db->select($select);
    }

    $CI->db->from('tb_auth_users');
    $CI->db->where('banned', '0');

    if ($level !== NULL){
      if (is_array($level)){
        $CI->db->where_in('auth_level', $level);
      } else {
        $CI->db->where('auth_level', $level);
      }
    }

    $CI->db->order_by('person_name', 'ASC');

    $query = $CI->db->get();

    return $query->result_array();
  }
}

if ( ! function_exists('available_warehouses')) {
  function available_warehouses($warehouse = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('warehouse');
    $CI->db->from('tb_master_warehouses');
    $CI->db->where('UPPER(status)', 'AVAILABLE');

    if ($warehouse !== NULL){
      if (is_array($warehouse)){
        $CI->db->where_not_in('warehouse', $warehouse);
      } else {
        $CI->db->where('warehouse != ', $warehouse);
      }
    }

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['warehouse'];
    }

    return $return;
  }
}

if ( ! function_exists('available_item_groups')) {
  function available_item_groups($category = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('tb_master_item_groups.group');
    // $CI->db->select('tb_master_item_groups.coa');
    $CI->db->from('tb_master_item_groups');
    $CI->db->where('UPPER(tb_master_item_groups.status)', 'AVAILABLE');
    // $CI->db->order_by()

    if ($category !== NULL){
      if (is_array($category)){
        $CI->db->where_in('tb_master_item_groups.category', $category);
      } else {
        $CI->db->where('tb_master_item_groups.category', $category);
      }
    }

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['group'];
    }

    return $return;
  }
}

if ( ! function_exists('available_vendors')) {
  function available_vendors($category = NULL)
  {
    $CI =& get_instance();

    $CI->db->distinct();
    $CI->db->select('tb_master_vendors.vendor');
    $CI->db->from('tb_master_vendors');
    $CI->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');

    // if ($category !== NULL){
    //   $CI->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');

    //   if (is_array($category)){
    //     $CI->db->where_in('tb_master_vendor_categories.category', $category);
    //   } else {
    //     $CI->db->where('tb_master_vendor_categories.category', $category);
    //   }
    // }

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['vendor'];
    }

    return $return;
  }
}

if ( ! function_exists('available_vendors_for_poe')) {
  function available_vendors_for_poe($currency = NULL)
  {
    $CI =& get_instance();

    $CI->db->distinct();
    $CI->db->select('tb_master_vendors.vendor');
    $CI->db->from('tb_master_vendors');
    $CI->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
    // $CI->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');
    $CI->db->where('tb_master_vendors.currency', $currency);

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['vendor'];
    }

    return $return;
  }
}

if ( ! function_exists('available_units')) {
  function available_units()
  {
    $CI =& get_instance();

    $CI->db->select('unit');
    $CI->db->from('tb_master_item_units');
    $CI->db->where('UPPER(status)', 'AVAILABLE');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['unit'];
    }

    return $return;
  }
}

if ( ! function_exists('render_alert')) {
  function render_alert($message, $type = 'info', $dismissable = TRUE)
  {
    echo '<div class="alert alert-'.$type.' alert-dismissable no-margin">';

    if ($dismissable === TRUE)
      echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';

    echo nl2br($message);

    echo '</div>';
  }
}

if ( ! function_exists('render_callout')) {
  function render_callout($message, $type = 'info', $dismissable = TRUE)
  {
    echo '<div class="alert alert-'.$type.' alert-callout alert-dismissable no-margin">';

    if ($dismissable === TRUE)
      echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';

    echo nl2br($message);
    echo '</div>';
  }
}

if ( ! function_exists('available_modules')) {
  function available_modules()
  {
    $modules    = config_item('module');
    $permission = config_item('auth_role');
    $results    = array();

    foreach ($modules as $key => $module){
      $roles = explode(',', $module['permission']['index']);
      $visible = $module['visible'];
      $main_warehouse = $module['main_warehouse'];
      $in_main_warehouse = (config_item('main_warehouse') == config_item('auth_warehouse')) ? TRUE : FALSE;

      if (in_array($permission, (array)$roles) && $visible == TRUE){
        if ( $main_warehouse == FALSE || ( $main_warehouse == TRUE && $in_main_warehouse == TRUE ) )
          $results[$module['parent']][] = $module;
      }
    }

    return $results;
  }
}


//tambahan
if ( ! function_exists('kode_akunting')) {
  function kode_akunting()
  {
    $CI =& get_instance();

    $CI->db->select('*');
    $CI->db->from('tb_master_kode_akunting');
    //$CI->db->where('status', 'AVAILABLE');

    $query  = $CI->db->get();
    $result = $query->result_array();
    //$result = $query->unbuffered_row('array');

    // foreach ($result as $row) {
    //   $return[] = $row['kode_akunting'];
    //   //$return[] = $row['description'];
    // }

    return $result;
  }
}

if ( ! function_exists('available_item_groups_2')) {
  function available_item_groups_2($category = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('tb_master_item_groups.group');
    $CI->db->select('tb_master_item_groups.coa');
    $CI->db->from('tb_master_item_groups');
    $CI->db->where('UPPER(tb_master_item_groups.status)', 'AVAILABLE');

    if ($category !== NULL){
      if (is_array($category)){
        $CI->db->where_in('tb_master_item_groups.category', $category);
      } else {
        $CI->db->where('tb_master_item_groups.category', $category);
      }
    }

    $query  = $CI->db->get();
    $result = $query->result_array();
    // $return = array();

    // foreach ($result as $row) {
    //   $return[] = $row['group'];
    // }

    return $result;
  }
}

if ( ! function_exists('last_update')) {
  function last_update()
  {
    $CI =& get_instance();

    $CI->db->select('end_date');
    $CI->db->from('tb_last_opname');
    $CI->db->where('status', 'last_opname');


    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $end_date   = $row->end_date;

    $last_opname_date        = strtotime('+1 day',strtotime($end_date));
    $date    = date('Y-m-d', $last_opname_date);
    

    return $date;
  }
}

if ( ! function_exists('pesawat')) {
  function pesawat()
  {
    $CI =& get_instance();

    $CI->db->select('nama_pesawat');
    $CI->db->from('tb_master_pesawat');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['nama_pesawat'];
    }

    return $return;
  }
}

if ( ! function_exists('start_date_last_opname')) {
  function start_date_last_opname()
  {
    $CI =& get_instance();

    $CI->db->select('start_date');
    $CI->db->from('tb_last_opname');
    $CI->db->where('status', 'last_opname');


    $query        = $CI->db->get();
    $row          = $query->unbuffered_row();
    $start_date   = $row->start_date;

    // $last_opname_date        = strtotime('+1 day',strtotime($end_date));
    // $date    = date('Y-m-d', $last_opname_date);
    

    return $start_date;
  }
}

if ( ! function_exists('end_date_last_opname')) {
  function end_date_last_opname()
  {
    $CI =& get_instance();

    $CI->db->select('end_date');
    $CI->db->from('tb_last_opname');
    $CI->db->where('status', 'last_opname');


    $query        = $CI->db->get();
    $row          = $query->unbuffered_row();
    $end_date     = $row->end_date;

    
    

    return $end_date;
  }
}

if ( ! function_exists('periode_opname')) {
  function periode_opname()
  {
    $CI =& get_instance();

    $CI->db->select('*');
    $CI->db->from('tb_last_opname');
    $CI->db->where('condition','good');
    $CI->db->order_by('id','desc');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    return $result;
  }
}

if ( ! function_exists('periode_opname_cancel')) {
  function periode_opname_cancel()
  {
    $CI =& get_instance();

    $CI->db->select('*');
    $CI->db->from('tb_last_opname');
    $CI->db->where('condition','cancel');
    $CI->db->order_by('id','desc');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    return $result;
  }
}

if ( ! function_exists('unpublish')) {
  function unpublish()
  {
    $CI =& get_instance();

    $CI->db->select('*');
    $CI->db->from('tb_last_opname');
    $CI->db->where('status', 'unpublish');


    $query  = $CI->db->get();
    $row    = $query->num_rows();    

    return $row;
  }
}

if ( ! function_exists('unpublish_date')) {
  function unpublish_date()
  {
    $CI =& get_instance();

    $CI->db->select('*');
    $CI->db->from('tb_last_opname');
    $CI->db->where('status', 'unpublish');


    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
      

    return $row;
  }
}

if ( ! function_exists('last_publish_date')) {
  function last_publish_date()
  {
    $CI =& get_instance();

    $CI->db->select('end_date');
    $CI->db->from('tb_last_opname');
    $CI->db->where('status', 'last_publish');


    $query      = $CI->db->get();
    $row        = $query->unbuffered_row();
    $end_date   = $row->end_date;

    $last_publish_date        = strtotime('+1 day',strtotime($end_date));
    $date                     = date('Y-m-d', $last_publish_date);    

    return $date;
  }
}

if ( ! function_exists('available_stores')) {
  function available_stores()
  {
    $CI =& get_instance();

    $CI->db->select('*');
    $CI->db->from('tb_master_stores');
    // $CI->db->where_in('category', $category);
    $CI->db->where('status', 'AVAILABLE');
    $CI->db->order_by('stores', 'ASC');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    return $result;
  }
}

if ( ! function_exists('get_stores')) {
  function get_stores($stock_id)
  {
    $CI =& get_instance();

    $CI->db->select('stores');
    $CI->db->from('tb_stock_in_stores');
    $CI->db->where('stock_id', $stock_id);
    $CI->db->group_by('stores');
    $CI->db->order_by('stores','asc');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['stores'];
    }

    return $return;
  }
}

    
