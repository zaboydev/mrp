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

if ( ! function_exists('print_number_left')) {
  function print_number_left($number, $decimal = 0, $force_right = TRUE)
  {
    if (trim($number) === '' || empty($number))
      $number = 0;

    if ($force_right === TRUE)
      $text = '<span style="display:block; text-align:left">';
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
    if ( isset($module['permission'][$roles]) && in_array(config_item('auth_role'), (array)explode(',', $module['permission'][$roles])) ){
      return TRUE;
    }else{
      if (config_item('as_head_department')=='yes') {
        if($roles=='index'||$roles=='info'||$roles=='print'||$roles=='approval'){
          if(in_array($module['name'],config_item('modules_for_head_dept'))){
            return TRUE;
          }else{
            return FALSE;
          }          
        }else{
          return FALSE;
        }
      }
      elseif(in_array(config_item('hr_department_name'),config_item('head_department'))){
        if(in_array($module['name'],config_item('additional_modules_for_hr_depatment'))){
          return TRUE;
        }else{
          return FALSE;
        }   
      }
      else{
        return FALSE;
      }
    }

    
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

if ( ! function_exists('currency_for_vendor_list')) {
  function currency_for_vendor_list($vendor)
  {
    $CI =& get_instance();

    $CI->db->select('currency');
    $CI->db->from('tb_master_vendors_currency');

    if (is_array($vendor)){
      $CI->db->where_in('vendor', $vendor);
    } else {
      $CI->db->where('vendor', $vendor);
    }

    $CI->db->order_by('currency', 'ASC');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['currency'];
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
    $CI->db->order_by('warehouse', 'asc');

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

if ( ! function_exists('get_available_warehouses')) {
  function get_available_warehouses($warehouse = NULL)
  {
    $CI =& get_instance();

    $CI->db->select('warehouse,address');
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
    $return = $query->result_array();

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
    $CI->db->order_by('tb_master_vendors.vendor','ASC');

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

if ( ! function_exists('available_vendors_by_currency')) {
  function available_vendors_by_currency($currency = NULL)
  {
    $CI =& get_instance();

    $CI->db->distinct();
    $CI->db->select('tb_master_vendors.vendor');
    $CI->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
    if($currency!=NULL){
      $CI->db->where('tb_master_vendors_currency.currency', $currency);
    }		
    $CI->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
    $CI->db->from('tb_master_vendors');
    $CI->db->order_by('tb_master_vendors.vendor', 'asc');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['vendor'];
    }

    return $return;
  }
}

if ( ! function_exists('search_vendors_by_currency')) {
  function search_vendors_by_currency($currency = NULL)
  {
    $CI =& get_instance();

    // $CI->db->distinct();
    $CI->db->select(array('tb_master_vendors.vendor'));
    if($currency!=NULL){
      $CI->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
      $CI->db->where('tb_master_vendors_currency.currency', $currency);
    }		
    $CI->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
    $CI->db->from('tb_master_vendors');
    $CI->db->order_by('tb_master_vendors.vendor', 'asc');

    $query  = $CI->db->get();
    $return = $query->result();
    
    return $return;
  }
}

if ( ! function_exists('available_vendors_for_poe')) {
  function available_vendors_for_poe($currency = NULL)
  {
    $CI =& get_instance();

    $CI->db->distinct();
    $CI->db->select('tb_master_vendors.vendor,tb_master_vendors_currency.currency');
    $CI->db->join('tb_master_vendors_currency', 'tb_master_vendors_currency.vendor=tb_master_vendors.vendor');
    $CI->db->from('tb_master_vendors');
    $CI->db->where('UPPER(tb_master_vendors.status)', 'AVAILABLE');
    // $CI->db->join('tb_master_vendor_categories', 'tb_master_vendors.vendor = tb_master_vendor_categories.vendor');
    if($currency!=NULL){
      $CI->db->where('tb_master_vendors_currency.currency', $currency);
    }
    $CI->db->order_by('tb_master_vendors.vendor', 'asc');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['currency'].'-'. $row['vendor'];
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
    $head_dept  = config_item('as_head_department');
    $results    = array();

    foreach ($modules as $key => $module){
      $roles = explode(',', $module['permission']['index']);
      $visible = $module['visible'];
      $main_warehouse = $module['main_warehouse'];
      $in_main_warehouse = (config_item('main_warehouse') == config_item('auth_warehouse')) ? TRUE : FALSE;

      if (in_array($permission, (array)$roles) && $visible == TRUE){
        if ( $main_warehouse == FALSE || ( $main_warehouse == TRUE && $in_main_warehouse == TRUE ) )
          $results[$module['parent']][] = $module;
      }else{
        if($head_dept=='yes'){
          if(in_array($module['name'],config_item('modules_for_head_dept')) && $visible == TRUE){
            if ( $main_warehouse == FALSE || ( $main_warehouse == TRUE && $in_main_warehouse == TRUE ) )
              $results[$module['parent']][] = $module;
          }
        }
        // else if(in_array(config_item('auth_username'),list_username_in_head_department(11))){
        //   if(in_array($module['name'],config_item('additional_modules_for_hr_depatment')) && $visible == TRUE){
        //     if ( $main_warehouse == FALSE || ( $main_warehouse == TRUE && $in_main_warehouse == TRUE ) )
        //       $results[$module['parent']][] = $module;
        //   }
        // }
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
    $CI->db->select('tb_master_item_groups.id');
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
    $CI->db->order_by('nama_pesawat');

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

if ( ! function_exists('get_ttd')) {
  function get_ttd($person_name)
  {
    $CI =& get_instance();

    $CI->db->select('ttd_user');
    $CI->db->from('tb_auth_users');
    $CI->db->where('person_name', $person_name);

    $query  = $CI->db->get();
    $result = $query->unbuffered_row('array');
    if($result['ttd_user']==null){
      $return = 'no_signature.PNG';
    }else{
      $return = $result['ttd_user'];
    }    

    return $return;
  }
}

if ( ! function_exists('budget_year')) {
  function budget_year()
  {
    $CI =& get_instance();

    $CI->db->select('year');
    $CI->db->from('tb_budget_cot');
    // $CI->db->where('stock_id', $stock_id);
    $CI->db->group_by('year');
    $CI->db->order_by('year','asc');

    $query  = $CI->db->get();
    $result = $query->result_array();
    $return = array();

    foreach ($result as $row) {
      $return[] = $row['year'];
    }

    return $return;
  }
}

if ( ! function_exists('get_user')) {
  function get_user($level)
  {
    $CI =& get_instance();

    $CI->db->select('person_name');
    $CI->db->from('tb_auth_users');
    $CI->db->where('auth_level', $level);
    $CI->db->limit(1);

    $query  = $CI->db->get();
    $result = $query->unbuffered_row('array');
    $return = $result['person_name'];

    return $return;
  }
}

if ( ! function_exists('month')) {
  function month()
  {
    $return = [
      '1' => 'Januari',
      '2' => 'Februari',
      '3' => 'Maret',
      '4' => 'April',
      '5' => 'Mei',
      '6' => 'Juni', 
      '7' => 'Juli',
      '8' => 'Agustus',
      '9' => 'September',
      '10' => 'Oktober',
      '11' => 'November',
      '12' => 'Desember',
    ];

    return $return;
  }
}

if (!function_exists('currency_for_vendor_list')) {
    function currency_for_vendor_list($vendor)
    {
      $CI = &get_instance();

      $CI->db->select('currency');
      $CI->db->from('tb_master_vendors_currency');

      if (is_array($vendor)) {
        $CI->db->where_in('vendor', $vendor);
      } else {
        $CI->db->where('vendor', $vendor);
      }

      $CI->db->order_by('currency', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();
      $return = array();

      foreach ($result as $row) {
        $return[] = $row['currency'];
      }

      return $return;
    }
  }

  if (!function_exists('available_categories')) {
    function available_categories()
    {
      $CI = &get_instance();

      $CI->db->from('tb_master_item_categories');
      // $CI->db->where('banned', '0');

      $query = $CI->db->get();

      return $query->result_array();
    }
  }

  if (!function_exists('category_for_user_list')) {
    function category_for_user_list($user)
    {
      $CI = &get_instance();

      $CI->db->select('category');
      $CI->db->from('tb_auth_user_categories');

      if (is_array($user)) {
        $CI->db->where_in('username', $user);
      } else {
        $CI->db->where('username', $user);
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

  if (!function_exists('get_vendor_name')) {
    function get_vendor_name($id)
    {
      $CI = &get_instance();

      $CI->db->select('vendor');
      $CI->db->from('tb_master_vendors');

      $CI->db->where('id', $id);

      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      $return = $row->vendor;

      return $return;
    }
  }

  if (!function_exists('get_part_number')) {
    function get_part_number($id)
    {
      $CI = &get_instance();

      $CI->db->select('part_number');
      $CI->db->from('tb_master_part_number');

      $CI->db->where('id', $id);

      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      $return = $row->part_number;

      return $return;
    }
  }

  if (!function_exists('getItemsById')) {
    function getItemsById($id)
    {
      $CI = &get_instance();

      $CI->db->select('*');
      $CI->db->from('tb_master_part_number');

      $CI->db->where('id', $id);

      $query  = $CI->db->get();
      $return    = $query->unbuffered_row('array');

      return $return;
    }
  }

  if (!function_exists('getBenefitById')) {
    function getBenefitById($id)
    {
      $CI = &get_instance();

      $CI->db->select('*');
      $CI->db->from('tb_master_employee_benefits');

      $CI->db->where('id', $id);

      $query  = $CI->db->get();
      $return    = $query->unbuffered_row('array');

      return $return;
    }
  }


  if (!function_exists('getExpiringContracts')) {
    function getExpiringContracts()
    {
      $CI = &get_instance();

      $CI->db->select('e.employee_number, e.name, c.contract_number, c.end_date');
      $CI->db->from('tb_employee_contracts c');
      $CI->db->join('tb_master_employees e', 'c.employee_number = e.employee_number');
      $CI->db->where('c.status', 'ACTIVE');
      $CI->db->where('c.end_date', date('Y-m-d', strtotime('+20 days')));

      $query = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }


  if (!function_exists('get_set_up_akun')) {
    function get_set_up_akun($id)
    {
      $CI = &get_instance();

      $CI->db->select('*');
      $CI->db->from('tb_master_akun');

      $CI->db->where('id', $id);

      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      // $return = $row->part_number;

      return $row;
    }
  }

  if (!function_exists('master_coa')) {
    function master_coa()
    {
      $CI = &get_instance();

      $CI->db->select('tb_master_coa.group');
      $CI->db->select('tb_master_coa.coa');
      $CI->db->select('tb_master_coa.id');
      $CI->db->from('tb_master_coa');
      $CI->db->where('UPPER(tb_master_coa.status)', 'AVAILABLE');
      $CI->db->order_by('tb_master_coa.coa','asc');

      $query  = $CI->db->get();
      $result = $query->result_array();
      // $return = array();

      // foreach ($result as $row) {
      //   $return[] = $row['group'];
      // }

      return $result;
    }
  }

  if ( ! function_exists('available_categories_for_user')) {
    function available_categories_for_user()
    {
      $CI =& get_instance();

      if ($select !== NULL){
        
      }
      $CI->db->select('category');
      $CI->db->from('tb_master_item_categories');
      $CI->db->where('status', 'AVAILABLE');

      $CI->db->order_by('category', 'ASC');

      $query = $CI->db->get();

      return $query->result_array();
    }
  }

  if ( ! function_exists('get_divisions')) {
    function get_divisions()
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->from('tb_divisions');
      // $connection->where('setting_name', $name);

      $query    = $connection->get();

      return $query->result_array();
    }
  }

  if ( ! function_exists('user_in_head_department')) {
    function user_in_head_department($department_id)
    {
      $CI =& get_instance();

      $CI->db->select('username');
      $CI->db->from('tb_head_department');

      if (is_array($category)){
        $CI->db->where_in('department_id', $department_id);
      } else {
        $CI->db->where('department_id', $department_id);
      }

      $CI->db->where('status', 'active');

      $CI->db->order_by('username', 'ASC');

      $query  = $CI->db->get();
      $result = $query->unbuffered_row('array');
      $return = $result['username'];

      return $return;
    }
  }

  if ( ! function_exists('user_in_head_department_list')) {
    function user_in_head_department_list($department_id)
    {
      $CI =& get_instance();

      $CI->db->select('username');
      $CI->db->from('tb_head_department');
      $CI->db->where('department_id', $department_id);
      $CI->db->where('status', 'active');
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

  if ( ! function_exists('list_user_in_head_department')) {
    function list_user_in_head_department($department_id)
    {
      $CI =& get_instance();

      if(config_item('auth_level')=='23'){
        $CI->db->select('tb_auth_users.username,tb_auth_users.person_name');
        $CI->db->from('tb_auth_users');
        $CI->db->where('tb_auth_users.auth_level', '24');
        $CI->db->order_by('tb_auth_users.username', 'ASC');

        $query  = $CI->db->get();
        $result = $query->result_array();
      }else{
        $CI->db->select('tb_head_department.username,tb_auth_users.person_name');
        $CI->db->from('tb_head_department');
        $CI->db->join('tb_auth_users','tb_auth_users.username=tb_head_department.username');
        $CI->db->where('tb_head_department.department_id', $department_id);
        $CI->db->where('tb_head_department.status', 'active');
        $CI->db->order_by('tb_head_department.username', 'ASC');

        $query  = $CI->db->get();
        $result = $query->result_array();
      }      

      return $result;
    }
  }

  if ( ! function_exists('available_user_for_head_department')) {
    function available_user_for_head_department($select = NULL, $department_id = NULL)
    {
      $CI =& get_instance();

      $CI->db->select('username');
      $CI->db->from('tb_head_department');
      if($department_id!==NULL){
        $CI->db->where('department_id !=', $department_id);
      }      
      $CI->db->where('status', 'active');
      $CI->db->order_by('username', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();
      $user_in_head_dept_list = array();

      foreach ($result as $row) {
        $user_in_head_dept_list[] = $row['username'];
      }

      if ($select !== NULL){
        $CI->db->select($select);
      }

      $CI->db->from('tb_auth_users');
      $CI->db->where('banned', '0');
      if(count($user_in_head_dept_list)>0){
        $CI->db->where_not_in('username', $user_in_head_dept_list);
      }
      $CI->db->order_by('person_name', 'ASC');

      $query = $CI->db->get();

      return $query->result_array();
    }
  }

  if ( ! function_exists('user_in_annual_cost_centers_list')) {
    function user_in_annual_cost_centers_list($annual_cost_center_id)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('username');
      $connection->from('tb_users_mrp_in_annual_cost_centers');

      if (is_array($annual_cost_center_id)){
        $connection->where_in('annual_cost_center_id', $annual_cost_center_id);
      } else {
        $connection->where('annual_cost_center_id', $annual_cost_center_id);
      }

      $connection->order_by('username', 'ASC');

      $query  = $connection->get();
      $result = $query->result_array();
      $return = array();

      foreach ($result as $row) {
        $return[] = $row['username'];
      }

      return $return;
    }
  }

  if ( ! function_exists('available_cost_centers')) {
    function available_cost_centers($select = NULL)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      if ($select !== NULL){
        $connection->select($select);
      }
      $connection->from('tb_cost_centers');
      $connection->order_by('cost_center_name', 'ASC');

      $query = $connection->get();

      return $query->result_array();
    }
  }

  if ( ! function_exists('annual_cost_centers')) {
    function annual_cost_centers($year)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('cost_center_id');
      $connection->from('tb_annual_cost_centers');
      $connection->where('year_number', $year);
      $connection->order_by('id', 'ASC');

      $query  = $connection->get();
      $result = $query->result_array();
      $return = array();

      foreach ($result as $row) {
        $return[] = $row['cost_center_id'];
      }

      return $return;
    }
  }

  if ( ! function_exists('getCostCenterNameByAnnualCostCenterId')) {
    function getCostCenterNameByAnnualCostCenterId($id)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('tb_cost_centers.cost_center_name');
      $connection->from('tb_cost_centers');
      $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
      $connection->where('tb_annual_cost_centers.id', $id);

      $query  = $connection->get();
      $row    = $query->unbuffered_row();
      $return = $row->cost_center_name;

      return $return;
    }
  }

  if ( ! function_exists('findProductCategoryById')) {
    function findProductCategoryById($id)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('category_name,category_code');
      $connection->from('tb_product_categories');
      $connection->where('id', $id);

      $query  = $connection->get();
      $row    = $query->unbuffered_row('array');
      // $return = $row->category_name;

      return $row;
    }
  }

  if ( ! function_exists('expense_item_without_po')) {
    function expense_item_without_po()
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('account_id');
      $connection->from('tb_expense_item_without_po');

      $connection->order_by('account_id', 'ASC');

      $query  = $connection->get();
      $result = $query->result_array();
      $return = array();

      foreach ($result as $row) {
        $return[] = $row['account_id'];
      }

      return $return;
    }
  }

  if ( ! function_exists('getReferenceIpcByPrlItemId')) {
    function getReferenceIpcByPrlItemId($prl_item_id,$type)
    {

      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      if($type='expense'){
        $connection->select('reference_ipc');
        $connection->from('tb_expense_purchase_requisition_details' );
        $connection->where('tb_expense_purchase_requisition_details.id', $prl_item_id);
      }
      else if($type='capex'){
        $connection->select('reference_ipc');
        $connection->from('tb_capex_purchase_requisition_details' );
        $connection->where('tb_capex_purchase_requisition_details.id', $prl_item_id);
      }
      else if($type='inventory'){
        $connection->select('reference_ipc');
        $connection->from('tb_inventory_purchase_requisition_details' );
        $connection->where('tb_inventory_purchase_requisition_details.id', $prl_item_id);
      }

      $query  = $connection->get();
      $row    = $query->unbuffered_row();
      $return = $row->reference_ipc;

      return $return;
    }
  }

  if ( ! function_exists('getPrlid')) {
    function getPrlid($id)
    {
      $CI =& get_instance();

      $CI->db->select('inventory_purchase_request_detail_id');
      $CI->db->from( 'tb_purchase_order_items' );
      $CI->db->where('tb_purchase_order_items.id', $id);

      $query    = $CI->db->get();
      $row      = $query->unbuffered_row();
      $return   = $row->inventory_purchase_request_detail_id;

      return $return;
    }
  }

  if ( ! function_exists('getStatusItemExpense')) {
    function getStatusItemExpense($account_id)
    {

      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('account_id');
      $connection->from('tb_expense_item_without_po' );
      $connection->where('tb_expense_item_without_po.account_id', $account_id);

      $num_rows = $connection->count_all_results();

      return ($num_rows > 0) ? 'f' : 't';
    }
  }

  if ( ! function_exists('getAccount')) {
    function getAccount($type=NULL)
    {
      $CI =& get_instance();

      $CI->db->select('group,coa');
      $CI->db->from( 'tb_master_coa' );
      if($type!=NULL){
        if($type=='CASH'){
          $CI->db->like('coa', '1-11');
        }else{
          $CI->db->like('coa', '1-12');
          $CI->db->or_like('coa', '1-13');
        }        
      }    
      // $CI->db->where('category', "Bank");
      $CI->db->order_by('coa', "asc");

      $query    = $CI->db->get();
      $return = $query->result_array();

      return $return;
    }
  }

  if ( ! function_exists('getAccountByCode')) {
    function getAccountByCode($code)
    {
      $CI =& get_instance();

      $CI->db->select('group,coa');
      $CI->db->from( 'tb_master_coa' ); 
      $CI->db->where('coa', $code);

      $query    = $CI->db->get();
      $row      = $query->unbuffered_row();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('getAccountBudgetControlByCode')) {
    function getAccountBudgetControlByCode($code)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('account_code as coa,account_name as group');
      $connection->from( 'tb_accounts' ); 
      $connection->where('account_code', $code);

      $query    = $connection->get();
      $row      = $query->unbuffered_row();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('getAccountsBudgetControl')) {
    function getAccountsBudgetControl()
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('account_code as coa,account_name as group');
      $connection->from( 'tb_accounts' ); 
      $connection->order_by('account_code','asc');

      $query    = $connection->get();
      $row      = $query->result_array();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('getAccountsMrp')) {
    function getAccountsMrp()
    {
      $CI =& get_instance();

      $CI->db->select('coa,group');
      $CI->db->from( 'tb_master_coa' ); 
      $CI->db->order_by('coa','asc');

      $query    = $CI->db->get();
      $row      = $query->result_array();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('getAccountMrpByCode')) {
    function getAccountMrpByCode($code)
    {
      $CI =& get_instance();
      // $connection = $CI->load->database('budgetcontrol', TRUE);

      $CI->db->select('coa,group');
      $CI->db->from( 'tb_master_coa' ); 
      $CI->db->where('coa', $code);

      $query    = $CI->db->get();
      $row      = $query->unbuffered_row();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('getMonthName')) {
    function getMonthName($month, $case = NULL)
    {
      $month = intval($month);

      switch ($month) {
        case 1:
        case 01:
          $print = 'Jan';
          break;

        case 2:
        case 02:
          $print = 'Feb';
          break;

        case 3:
        case 03:
          $print = 'Mar';
          break;

        case 4:
        case 04:
          $print = 'Apr';
          break;

        case 5:
        case 05:
          $print = 'May';
          break;

        case 6:
        case 06:
          $print = 'Jun';
          break;

        case 7:
        case 07:
          $print = 'Jul';
          break;

        case 8:
        case 08:
          $print = 'Aug';
          break;

        case 9:
        case 09:
          $print = 'Sep';
          break;

        case 10:
          $print = 'Oct';
          break;

        case 11:
          $print = 'Nov';
          break;

        case 12:
          $print = 'Dec';
          break;
      }

      if ($case == 'uppercase'){
        $print = strtoupper($print);
      } else if ($case == 'lowercase') {
        $print = strtolower($print);
      }

      return $print;
    }
  }

  if ( ! function_exists('get_annual_cost_centers')) {
    function get_annual_cost_centers($year,$tipe)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      if ($tipe!='all'){
        $connection->select(array('tb_annual_cost_centers.id','tb_cost_centers.cost_center_name','tb_cost_centers.cost_center_code','tb_departments.department_code'));
        $connection->from('tb_users_mrp_in_annual_cost_centers');
        $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
        $connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
        $connection->join('tb_departments','tb_cost_centers.department_id=tb_departments.id');
        $connection->where('tb_users_mrp_in_annual_cost_centers.username', $_SESSION['username']);
        $connection->where('tb_annual_cost_centers.year_number', $year);
        $connection->order_by('tb_cost_centers.id', 'ASC');
      } else {
        $connection->select(array('tb_annual_cost_centers.id','tb_cost_centers.cost_center_name','tb_cost_centers.cost_center_code','tb_departments.department_code'));
        $connection->from('tb_annual_cost_centers');
        // $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.id=tb_users_mrp_in_annual_cost_centers.annual_cost_center_id');
        $connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
        $connection->join('tb_departments','tb_cost_centers.department_id=tb_departments.id');
        $connection->where('tb_annual_cost_centers.year_number', $year);
        $connection->order_by('tb_cost_centers.id', 'ASC');
      }

      $query  = $connection->get();
      $return = $query->result_array();

      return $return;
    }
  }

  if ( ! function_exists('getYearNumber')) {
    function getYearNumber()
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('year_number');
      $connection->from('tb_annual_cost_centers');
      $connection->group_by('year_number');
      $connection->order_by('year_number','asc');

      $query  = $connection->get();
      $result = $query->result_array();
      $return = array();

      foreach ($result as $row) {
        $return[] = $row['year_number'];
      }

      return $return;
    }
  }

  if ( ! function_exists('getDeliveryTo')) {
    function getDeliveryTo()
    {
      $CI =& get_instance();

      $CI->db->select(array('warehouse','address','country'));
      $CI->db->from('tb_delivery_to');
      $CI->db->where('status','AVAILABLE');

      $query = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('getWarehouseByName')) {
    function getWarehouseByName($warehouse)
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->from( 'tb_master_warehouses' );
      $CI->db->where('tb_master_warehouses.warehouse', $warehouse);

      $query    = $CI->db->get();
      $warehosue = $query->unbuffered_row('array');
      return $warehosue;
    }
  }

  if ( ! function_exists('getBillTo')) {
    function getBillTo()
    {
      $CI =& get_instance();

      $CI->db->select(array('warehouse','address','country'));
      $CI->db->from('tb_bill_to');
      $CI->db->where('status','AVAILABLE');

      $query = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('getDefaultDeliveryTo')) {
    function getDefaultDeliveryTo()
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->from( 'tb_delivery_to' );
      $CI->db->where('tb_delivery_to.is_default', 'yes');

      $query    = $CI->db->get();
      $warehosue = $query->unbuffered_row('array');
      return $warehosue;
    }
  }

  if ( ! function_exists('getDefaultBillTo')) {
    function getDefaultBillTo()
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->from( 'tb_bill_to' );
      $CI->db->where('tb_bill_to.is_default', 'yes');

      $query    = $CI->db->get();
      $warehosue = $query->unbuffered_row('array');
      return $warehosue;
    }
  }

  if ( ! function_exists('getRequestItemIdsByPoeId')) {
    function getRequestItemIdsByPoeId($poe_id)
    {
      $CI =& get_instance();

      $CI->db->select('inventory_purchase_request_detail_id');
      $CI->db->from( 'tb_purchase_order_items' );
      $CI->db->where('tb_purchase_order_items.purchase_order_id', $poe_id);

      $query    = $CI->db->get();
      $result      = $query->result_array();
      $return = array();

      foreach ($result as $row) {
        $return[] = $row['inventory_purchase_request_detail_id'];
      }

      return $return;
    }
  }

  if ( ! function_exists('getDepartmentByAnnualCostCenterId')) {
    function getDepartmentByAnnualCostCenterId($annual_cost_center_id)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('tb_departments.*');
      $connection->from('tb_departments');
      $connection->join('tb_cost_centers', 'tb_departments.id = tb_cost_centers.department_id');
      $connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.cost_center_id = tb_cost_centers.id');
      $connection->where('tb_annual_cost_centers.id',$annual_cost_center_id);
      $query  = $connection->get();
      $result = $query->unbuffered_row('array');

      

      return $result;
    }
  }

  if ( ! function_exists('getHeadDeptByDeptid')) {
    function getHeadDeptByDeptid($department_id)
    {
      $CI =& get_instance();
      // $connection = $CI->load->database('budgetcontrol', TRUE);

      $CI->db->select('username');
      $CI->db->from('tb_head_department');
      $CI->db->where('department_id',$department_id);
      $CI->db->where('status','active');
      $query  = $CI->db->get();
      $return = array();
      if($query->num_rows()>0){
        $result = $query->result_array();
        foreach ($result as $row) {
          $return[] = $row['username'];
        }
      }else{
        $return[] = 'aidanurul';
      }

      return $return;
    }
  }

  if ( ! function_exists('getCostCenterByAnnualCostCenterId')) {
    function getCostCenterByAnnualCostCenterId($annual_cost_center_id)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('tb_cost_centers.*');
      $connection->from('tb_cost_centers');
      $connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.cost_center_id = tb_cost_centers.id');
      $connection->where('tb_annual_cost_centers.id',$annual_cost_center_id);
      $query  = $connection->get();
      $result = $query->unbuffered_row('array');     

      return $result;
    }
  }

  if ( ! function_exists('isAttachementExists')) {
    function isAttachementExists($request_id,$tipe)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->from('tb_attachment');
      $connection->where('id_purchase', $request_id);
      $connection->where('tipe', $tipe);

      $num_rows = $connection->count_all_results();

      if($tipe=='POE'||$tipe=='PO'||$tipe=='GRN'){
        $CI =& get_instance();
        $CI->db->from( 'tb_attachment_poe' );
        $CI->db->where('id_poe', $request_id);
        $CI->db->where('tipe', $tipe);

        $num_rows = $CI->db->count_all_results();
        // $num_rows = 2;
      }

      return $num_rows;
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

  if ( ! function_exists('getCostCenterByIdRequest')) {
    function getCostCenterByIdRequest($id,$tipe)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('cost_center_code','cost_center_name'));
      $connection->from( 'tb_cost_centers' );
      $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
      if($tipe=="capex"){
        $connection->join('tb_capex_purchase_requisitions','tb_capex_purchase_requisitions.annual_cost_center_id=tb_annual_cost_centers.id');
        $connection->where('tb_capex_purchase_requisitions.id', $id);
      }
      if($tipe=="expense"){
        $connection->join('tb_expense_purchase_requisitions','tb_expense_purchase_requisitions.annual_cost_center_id=tb_annual_cost_centers.id');
        $connection->where('tb_expense_purchase_requisitions.id', $id);
      }
      if($tipe=="inventory"){
        $connection->join('tb_inventory_purchase_requisitions','tb_inventory_purchase_requisitions.annual_cost_center_id=tb_annual_cost_centers.id');
        $connection->where('tb_inventory_purchase_requisitions.id', $id);
      }

      $query    = $connection->get();
      $cost_center = $query->unbuffered_row('array');
      

      return $cost_center;
    }
  }

  if ( ! function_exists('viewOrNot')) {
    function viewOrNot($status,$head_dept,$department_request=NULL)
    {
      if($status=='WAITING FOR HEAD DEPT' || $status=='WAITING APPROVAL BY HEAD DEPT'){
        //untuk expense,capex,inv request
        if(config_item('as_head_department')=='yes'){
          if(in_array($department_request,config_item('head_department')) && $head_dept==config_item('auth_username')){
            return true;
          }elseif(in_array(config_item('auth_role'),['HEAD OF SCHOOL','CHIEF OPERATION OFFICER'])){
            if($head_dept==config_item('auth_username')){
              return true;
            }else{
              return false;
            }
          }else{
            return false;
          }
        }elseif(in_array(config_item('auth_role'),['HEAD OF SCHOOL','CHIEF OPERATION OFFICER'])){
          if($head_dept==config_item('auth_username')){
            return true;
          }else{
            return false;
          }
        }else{
          return true;
        }
      }else if($status=='waiting'){
        //untuk purchase request maintenance
        if(config_item('as_head_department')=='yes'){
          if($head_dept==config_item('auth_username')){
            return true;
          }else{
            return false;
          }
        }else{
          return true;
        } 
      }else{
        return true;
      }
    }
  }

  if ( ! function_exists('readyToCloseRequest')) {
    function readyToCloseRequest($purchase_request_id,$tipe)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);
      
      if($tipe=='expense'){
        $connection->from('tb_expense_purchase_requisitions');
        $connection->where('id',$purchase_request_id);
        $query    = $connection->get();
        $request  = $query->unbuffered_row('array');
        
        if($request['with_po']=='t'){
          return true;
        }else{
          return false;
        }
      }

      if($tipe=='capex'){
        $connection->from('tb_capex_purchase_requisitions');
        $connection->where('id',$purchase_request_id);
        $query    = $connection->get();
        $request  = $query->unbuffered_row('array');
        
        if($request['with_po']=='t'){
          return true;
        }else{
          return false;
        }
      }
    }
  }

  if ( ! function_exists('isItemRequestAlreadyInClosures')) {
    function isItemRequestAlreadyInClosures($purchase_request_detail_id,$tipe)
    {
      $CI =& get_instance();

      $CI->db->from('tb_purchase_request_closures');
      $CI->db->where('purchase_request_detail_id',$purchase_request_detail_id);
      $CI->db->where('tipe',$tipe);

      $query = $CI->db->get();

      return ( $query->num_rows() > 0 ) ? true : false;
    }
  }

  if ( ! function_exists('getUsernameByPersonName')) {
    function getUsernameByPersonName($person_name)
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->from('tb_auth_users');
      $CI->db->where('person_name', $person_name);

      $query = $CI->db->get();

      return $query->unbuffered_row('array');
    }
  }

  if ( ! function_exists('listAttachmentRequest')) {
    function listAttachmentRequest($poe_id,$tipe)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $CI->db->select('inventory_purchase_request_detail_id');
      $CI->db->where('purchase_order_id', $poe_id);
      $query    = $CI->db->get('tb_purchase_order_items');    
      $result = $query->result_array();
      $request_item_id = array();

      foreach ($result as $row) {
        $request_item_id[] = $row['inventory_purchase_request_detail_id'];
      }

      //get request id
      if($tipe=='EXPENSE'){
        $connection->select('expense_purchase_requisition_id');
        $connection->where_in('id', $request_item_id);
        $queryrequest_id    = $connection->get('tb_expense_purchase_requisition_details');    
        $resultrequest_id = $queryrequest_id->result_array();
        $request_id = array();

        foreach ($resultrequest_id as $row) {
          $request_id[] = $row['expense_purchase_requisition_id'];
        }
        $tipe_request = 'expense';

      }
      if($tipe=='CAPEX'){
        $connection->select('capex_purchase_requisition_id');
        $connection->where_in('id', $request_item_id);
        $queryrequest_id    = $connection->get('tb_capex_purchase_requisition_details');    
        $resultrequest_id = $queryrequest_id->result_array();
        $request_id = array();

        foreach ($resultrequest_id as $row) {
          $request_id[] = $row['capex_purchase_requisition_id'];
        }
        $tipe_request = 'capex';

      }

      if($tipe=='INVENTORY'){
        $connection->select('inventory_purchase_requisition_id');
        $connection->where_in('id', $request_item_id);
        $queryrequest_id    = $connection->get('tb_inventory_purchase_requisition_details');    
        $resultrequest_id = $queryrequest_id->result_array();
        $request_id = array();

        foreach ($resultrequest_id as $row) {
          $request_id[] = $row['inventory_purchase_requisition_id'];
        }
        $tipe_request = 'inventory';

      }

      if($tipe=='EXPENSE' || $tipe=='CAPEX' || $tipe=='INVENTORY'){
        if(count($request_id)>0){
          $connection->where_in('id_purchase', $request_id);
          $connection->where('tipe', $tipe_request);
          return $connection->get('tb_attachment')->result_array();
        }else{
          return [];
        }        
      }else{
        return [];
      }
    }
  }

  if ( ! function_exists('idPoehaveAttachment')) {
    function idPoehaveAttachment($id,$tipe)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $CI->db->select('*');
      $CI->db->from('tb_attachment_poe');
      $CI->db->where('id_poe', $id);
      $CI->db->where('tipe', 'POE');

      $query = $CI->db->get();
      $att_poe = $query->num_rows();

      $CI->db->select('tb_purchase_order_items.inventory_purchase_request_detail_id');
      // $CI->db->from('tb_purchase_order_items');
      $CI->db->where('purchase_order_id', $id);
      $query_poe = $CI->db->get('tb_purchase_order_items');
      $request_item_id = array();

      foreach ($query_poe->result_array() as $row) {
        $request_item_id[] = $row['inventory_purchase_request_detail_id'];
      }

      if($tipe=='EXPENSE'){
        $connection->select('expense_purchase_requisition_id');
        $connection->where_in('id', $request_item_id);
        $queryrequest_id    = $connection->get('tb_expense_purchase_requisition_details');    
        $resultrequest_id = $queryrequest_id->result_array();
        $request_id = array();

        foreach ($resultrequest_id as $row) {
          $request_id[] = $row['expense_purchase_requisition_id'];
        }
        $tipe_request = 'expense';
      }

      if($tipe=='CAPEX'){
        $connection->select('capex_purchase_requisition_id');
        $connection->where_in('id', $request_item_id);
        $queryrequest_id    = $connection->get('tb_capex_purchase_requisition_details');    
        $resultrequest_id = $queryrequest_id->result_array();
        $request_id = array();

        foreach ($resultrequest_id as $row) {
          $request_id[] = $row['capex_purchase_requisition_id'];
        }
        $tipe_request = 'capex';
      }

      if($tipe=='EXPENSE' || $tipe=='CAPEX' || $tipe=='INVENTORY'){
        if(count($request_id)>0){
          $connection->where_in('id_purchase', $request_id);
          $connection->where('tipe', $tipe_request);
          $query_request = $connection->get('tb_attachment');
          $att_request = $query_request->num_rows();
        }else{
          $att_request = 0;
        }
        
      }else{
        $att_request = 0;
      }

      $total = $att_poe+$att_request;
      return $total > 0? true:false;
    }
  }

  if ( ! function_exists('idPoHaveAttachment')) {
    function idPoHaveAttachment($id,$tipe)
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $CI->db->select('*');
      $CI->db->from('tb_attachment_poe');
      $CI->db->where('id_poe', $id);
      $CI->db->where('tipe', 'PO');

      $query = $CI->db->get();
      $att_po = $query->num_rows();

      // //POE
      // $CI->db->select('poe_number');
      // $CI->db->where('purchase_order_id', $id);
      // $query    = $CI->db->get('tb_po_item');
      // $item_po = $query->unbuffered_row('array');
      // $poe_number = $item_po['poe_number'];

      // $CI->db->select('id');
      // $CI->db->where('evaluation_number', $poe_number);
      // $query    = $CI->db->get('tb_purchase_orders');
      // $evaluation = $query->unbuffered_row();
      // $poe_id = $evaluation->id;

      // $CI->db->select('*');
      // $CI->db->from('tb_attachment_poe');
      // $CI->db->where('id_poe', $poe_id);
      // $CI->db->where('tipe', 'POE');

      // $query = $CI->db->get();
      // $att_poe = $query->num_rows();

      // //request
      // $CI->db->select('tb_purchase_order_items.inventory_purchase_request_detail_id');
      // $CI->db->where('purchase_order_id', $poe_id);
      // $query_poe = $CI->db->get('tb_purchase_order_items');
      // $request_item_id = array();

      // foreach ($query_poe->result_array() as $row) {
      //   $request_item_id[] = $row['inventory_purchase_request_detail_id'];
      // }

      // if($tipe=='EXPENSE'){
      //   $connection->select('expense_purchase_requisition_id');
      //   $connection->where_in('id', $request_item_id);
      //   $queryrequest_id    = $connection->get('tb_expense_purchase_requisition_details');    
      //   $resultrequest_id = $queryrequest_id->result_array();
      //   $request_id = array();

      //   foreach ($resultrequest_id as $row) {
      //     $request_id[] = $row['expense_purchase_requisition_id'];
      //   }
      //   $tipe_request = 'expense';
      // }

      // if($tipe=='EXPENSE' || $tipe=='CAPEX' || $tipe=='INVENTORY'){
      //   $connection->where_in('id_purchase', $request_id);
      //   $connection->where('tipe', $tipe_request);
      //   $query_request = $connection->get('tb_attachment');
      //   $att_request = $query_request->num_rows();
      // }else{
      //   $att_request = 0;
      // }

      // $total = $att_po+$att_poe+$att_request;
      $total = $att_po;
      return $total > 0? true:false;
    }
  }

  if ( ! function_exists('getStatusEditPoe')) {
    function getStatusEditPoe($evaluation_number)
    {
      $CI =& get_instance();

      $CI->db->from('tb_po_item');
      $CI->db->join('tb_po','tb_po.id=tb_po_item.purchase_order_id');
      $CI->db->where('tb_po_item.poe_number',$evaluation_number);
      $CI->db->where_not_in('tb_po.status',['REVISI','REJECTED','CANCEL']);

      $query = $CI->db->get();

      return ( $query->num_rows() > 0 ) ? false : true;
    }
  }

  if ( ! function_exists('payment_request_format_number')) {
    function payment_request_format_number($type,$category='SPEND')
    {
      $div  = config_item('document_format_divider');
      $year = date('Y');

      if($type=='BANK'){
        $kode = 'B';
      }else{
        $kode = 'C';
      }

      if($category=='SPEND'){
        $kode2 = 'P';
      }elseif($category=='RECEIVE'){
        $kode2 = 'R';
      }

      $kodeFinal = $kode.$kode2.'V';

      $return = $div . $kodeFinal . $div . $year;

      return $return;
    }
  }

  if ( ! function_exists('payment_request_last_number')) {
    function payment_request_last_number($type,$category='SPEND')
    {
      $CI =& get_instance();
      $format = payment_request_format_number($type,$category);

      $CI->db->select_max('document_number', 'last_number');
      $CI->db->from('tb_po_payment_no_transaksi');
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
  
  if ( ! function_exists('getStatusCancelRequest')) {
    function getStatusCancelRequest($pr_number)
    {
      $CI =& get_instance();

      $CI->db->from('tb_purchase_order_items');
      $CI->db->where('tb_purchase_order_items.purchase_request_number',$pr_number);

      $query = $CI->db->get();

      return ( $query->num_rows() > 0 ) ? false : true;
    }
  }

  if ( ! function_exists('getEvaluationId')) {
    function getEvaluationId($po_id)
    {
      $CI =& get_instance();

      $CI->db->select('poe_number');
      $CI->db->where('purchase_order_id', $po_id);
      $query        = $CI->db->get('tb_po_item');
      $item_po      = $query->unbuffered_row('array');
      $no_evaluasi  =  $item_po['poe_number'];


      $CI->db->select('id');
      $CI->db->where('evaluation_number', $no_evaluasi);
      $query    = $CI->db->get('tb_purchase_orders');
      $evaluation = $query->unbuffered_row('array');
      return $evaluation['id'];
    }
  }

  if ( ! function_exists('getAllPoeAtt')) {
    function getAllPoeAtt($poe_id)
    {
      $CI =& get_instance();

      $CI->db->where('id_poe', $poe_id);
      $CI->db->where('tipe', 'POE');
      $CI->db->where(array('deleted_at' => NULL));
      return $CI->db->get('tb_attachment_poe');
    }
  }

  if ( ! function_exists('getAccount')) {
    function getAccountByCurrency($currency=null)
    {
      $CI =& get_instance();

      $CI->db->select('group,coa');
		  $CI->db->from('tb_master_coa');
      // $CI->db->like('group', $currency);
      $CI->db->where('category', "Bank");
      $query    = $CI->db->get();
      $accounts = $query->result_array();
      return $accounts;
    }
  }

  if ( ! function_exists('getYears')) {
    function getYears()
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('year_number');
      $connection->group_by('year_number');
      $connection->order_by('year_number','desc');      
      $query    = $connection->get('tb_annual_cost_centers');
      $year = $query->result_array();
      return $year;
    }
  }

  if ( ! function_exists('request_payment_format_number')) {
    function request_payment_format_number($type)
    {
      $div  = config_item('document_format_divider');
      $year = date('Y');
      $category = $_SESSION['request_closing']['category'];

      if($type=='BANK'){
        $kode = 'BPV';
      }else{
        $kode = 'CPV';
      }

      $return = $div. $category. $div . $kode . $div . $year;

      return $return;
    }
  }

  if ( ! function_exists('request_payment_last_number')) {
    function request_payment_last_number()
    {
      $CI =& get_instance();
      $connection = $CI->load->database('budgetcontrol', TRUE);
      $format = request_payment_format_number($_SESSION['request_closing']['type']);

      $connection->select_max('document_number', 'last_number');
      $connection->from('tb_request_payments');
      $connection->like('document_number', $format);

      $query  = $connection->get();
      $row    = $query->unbuffered_row();
      $last   = $row->last_number;
      $number = substr($last, 0, 6);
      $next   = $number + 1;
      $return = sprintf('%06s', $next);

      return $return;
    }
  }

  if ( ! function_exists('saldo_format_number')) {
    function saldo_format_number()
    {
      $div  = config_item('document_format_divider');
      $year = date('Y');

      $kode = 'SA';

      $return = $div . $kode . $div . $year;

      return $return;
    }
  }

  if ( ! function_exists('saldo_last_number')) {
    function saldo_last_number()
    {
      $CI =& get_instance();
      $format = saldo_format_number();

      $CI->db->select_max('transaction_number', 'last_number');
      $CI->db->from('tb_saldo_awal');
      $CI->db->like('transaction_number', $format);

      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      $last   = $row->last_number;
      $number = substr($last, 0, 6);
      $next   = $number + 1;
      $return = sprintf('%06s', $next);

      return $return;
    }
  }

  if ( ! function_exists('getAccountByCategory')) {
    function getAccountByCategory($category)
    {
      $CI =& get_instance();

      $CI->db->select('group,coa');
      $CI->db->from( 'tb_master_coa' );

      if (is_array($category)){
        $CI->db->where_in('category', $category);
      } else {
        $CI->db->where('category', $category);
      }

      $CI->db->order_by('coa', "asc");

      $query    = $CI->db->get();
      $return = $query->result_array();

      return $return;
    }
  }

  if ( ! function_exists('kurs')) {
    function kurs($date)
    {
      $CI =& get_instance();
      $kurs_dollar = 0;
      $tanggal = $date;

      while ($kurs_dollar == 0) {
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

  if ( ! function_exists('saldoAwalExists')) {
    function saldoAwalExists($category)
    {
      $CI =& get_instance();
      $CI->db->where('category', $category);
      $query = $CI->db->get('tb_saldo_awal');

      if ($query->num_rows() > 0)
        return true;

      return false;
    }
  }

  if ( ! function_exists('getTaxs')) {
    function getTaxs()
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->from( 'tb_master_daftar_pajak' ); 
      $CI->db->order_by('id','asc');

      $query    = $CI->db->get();
      $row      = $query->result_array();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('getAccounts')) {
    function getAccounts()
    {
      $CI =& get_instance();

      $CI->db->select('coa,group');
      $CI->db->from( 'tb_master_coa' ); 
      $CI->db->order_by('coa','asc');

      $query    = $CI->db->get();
      $row      = $query->result_array();
      $return   = $row;

      return $return;
    }
  }

  if ( ! function_exists('cekDirektori')) {
    function cekDirektori($upload_path)
    {
      if ( ! is_dir($upload_path))
      {
        if ( ! mkdir ($upload_path, 0777, TRUE))
        {
          return FALSE;
        }
      }
  
      if ( ! is_really_writable($upload_path))
      {
        if ( ! chmod($upload_path, 0777))
        {
          return FALSE;
        }
      }
      return TRUE;
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

  if ( ! function_exists('getRequest')) {
    function getRequest($id,$tipe,$select)
    {
      $CI =& get_instance();
  
      $connection = $CI->load->database('budgetcontrol', TRUE);
  
  
      if($tipe=='capex'){
        $connection->select('tb_capex_purchase_requisitions.*, tb_cost_centers.cost_center_name, tb_cost_centers.cost_center_code, tb_cost_centers.department_id,tb_departments.department_name');
        $connection->from('tb_capex_purchase_requisitions');
        $connection->join('tb_capex_purchase_requisition_details', 'tb_capex_purchase_requisition_details.capex_purchase_requisition_id = tb_capex_purchase_requisitions.id');
        $connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_capex_purchase_requisitions.annual_cost_center_id');
        $connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $connection->where('tb_capex_purchase_requisition_details.id', $id);
      }
  
      if($tipe=='inventory'){
        $connection->select('tb_inventory_purchase_requisitions.*, tb_cost_centers.cost_center_name, tb_cost_centers.cost_center_code, tb_cost_centers.department_id,tb_departments.department_name');
        $connection->from('tb_inventory_purchase_requisitions');
        $connection->join('tb_inventory_purchase_requisition_details', 'tb_inventory_purchase_requisition_details.inventory_purchase_requisition_id = tb_inventory_purchase_requisitions.id');
        $connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_inventory_purchase_requisitions.annual_cost_center_id');
        $connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $connection->where('tb_inventory_purchase_requisition_details.id', $id);
      }
  
      if($tipe=='expense'){      
        $connection->select('tb_expense_purchase_requisitions.*, tb_cost_centers.cost_center_name, tb_cost_centers.cost_center_code, tb_cost_centers.department_id,tb_departments.department_name');
        $connection->from('tb_expense_purchase_requisitions');
        $connection->join('tb_expense_purchase_requisition_details', 'tb_expense_purchase_requisition_details.expense_purchase_requisition_id = tb_expense_purchase_requisitions.id');
        $connection->join('tb_annual_cost_centers', 'tb_annual_cost_centers.id = tb_expense_purchase_requisitions.annual_cost_center_id');
        $connection->join('tb_cost_centers', 'tb_cost_centers.id = tb_annual_cost_centers.cost_center_id');
        $connection->join('tb_departments', 'tb_departments.id = tb_cost_centers.department_id');
        $connection->where('tb_expense_purchase_requisition_details.id', $id);
      }
  
      $query  = $connection->get();
      $row    = $query->unbuffered_row('array');
      $return = $row[$select];
  
      return $return;
    }
  }

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

  if ( ! function_exists('available_stores_by_category')) {
    function available_stores_by_category($category)
    {
      $CI =& get_instance();
      
  
      $CI->db->select('*');
      $CI->db->from('tb_master_stores');
      $CI->db->where('category', $category);
      $CI->db->where('warehouse', config_item('auth_warehouse'));
      $CI->db->where('status', 'AVAILABLE');
      $CI->db->order_by('stores', 'ASC');
  
      $query  = $CI->db->get();
      $result = $query->result_array();
      $data  = array();

      foreach ($result as $key => $row){
        if ($row['stores'] != null)
          $data[] = $row['stores'];
      }
  
      return $data;
    }
  }

  if (!function_exists('closeReturnDocument')) {
    function closeReturnDocument($return_id)
    {
      $CI = &get_instance();

      $CI->db->select('left_process_quantity');
      $CI->db->from('tb_return_items');
      $CI->db->where('return_id', $return_id);
      // $CI->db->where('stores', strtoupper($stores));

      $query  = $CI->db->get();
      $result = $query->result_array();
      $return = 0;

      foreach ($result as $row) {
        $return = $return + $row['left_process_quantity'];
      }

      return $return==0? true:false;
    }
  }

  if ( ! function_exists('getReturnIdByReturnItemId')) {
    function getReturnIdByReturnItemId($return_item_id)
    {
      $CI =& get_instance();

      $CI->db->select('tb_return_items.*');
      $CI->db->from( 'tb_return_items' );
      $CI->db->where('tb_return_items.id', $return_item_id);

      $query    = $CI->db->get();
      $row      = $query->unbuffered_row('array');
      $return   = $row['return_id'];

      return $return;
    }
  }

  if ( ! function_exists('available_warehouses_alternate_name')) {
    function available_warehouses_alternate_name($warehouse = NULL)
    {
      $CI =& get_instance();
  
      $CI->db->select('tb_master_warehouses.*');
      $CI->db->from('tb_master_warehouses');
      $CI->db->where('UPPER(status)', 'AVAILABLE');
      // $CI->db->where('alternate_warehouse_name is NOT NULL', NULL, FALSE);
  
      if ($warehouse !== NULL){
        if (is_array($warehouse)){
          $CI->db->where_not_in('warehouse', $warehouse);
        } else {
          $CI->db->where('warehouse != ', $warehouse);
        }
      }
  
      $query  = $CI->db->get();
      $result = $query->result_array();
      // $return = array();
  
      // foreach ($result as $row) {
      //   $return[] = $row['alternate_warehouse_name'];
      // }
  
      return $result;
    }
  }

  if ( ! function_exists('findWarehouseByAlternateName')) {
    function findWarehouseByAlternateName($alternate_warehouse_name)
    {
      $CI =& get_instance();
  
      $CI->db->select('tb_master_warehouses.*');
      $CI->db->from('tb_master_warehouses');
      $CI->db->where('UPPER(status)', 'AVAILABLE');
      $CI->db->where('alternate_warehouse_name',$alternate_warehouse_name);
  
  
      $query  = $CI->db->get();
      $result = $query->row_array();
      $return = $result['warehouse'];
  
      return $return;
    }
  }

  if ( ! function_exists('isComponentExist')) {
    function isComponentExist($aircraft_code,$type)
    {
      $CI =& get_instance();
  
      $CI->db->from('tb_aircraft_components');
      $CI->db->where('aircraft_code', strtoupper($aircraft_code));
      $CI->db->where('type', $type);

      $num_rows = $CI->db->count_all_results();

      return ($num_rows > 0) ? TRUE : FALSE;
    }
  }

  if ( ! function_exists('available_aircrafts')) {
    function available_aircrafts()
    {
      $CI =& get_instance();
  
      $CI->db->select(
        array(
          'id',
          'nama_pesawat',
          'base'
        )
      );
      $CI->db->order_by('nama_pesawat','asc');
      $CI->db->from('tb_master_pesawat');
  
      $query  = $CI->db->get();
      $result = $query->result_array();
  
      return $result;
    }
  }

  if ( ! function_exists('findCostCenterByAnnualCostCenterId')) {
    function findCostCenterByAnnualCostCenterId($annual_cost_center_id)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('cost_center_code','cost_center_name','department_id','tb_cost_centers.id'));
      $connection->from( 'tb_cost_centers' );
      $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
      $connection->where('tb_annual_cost_centers.id', $annual_cost_center_id);

      $query    = $connection->get();
      $cost_center = $query->unbuffered_row('array');
      

      return $cost_center;
    }
  }

  if ( ! function_exists('getTotalFlightTarget')) {
    function getTotalFlightTarget($year_number)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('flight_target'));
      $connection->from( 'tb_monthly_flights' );
      $connection->where('tb_monthly_flights.year_number', $year_number);

      $query    = $connection->get();
      $result   = $query->result_array();

      $total_flight_target = 0;
      foreach($result as $item){
        $total_flight_target = $total_flight_target+$item['flight_target'];
      }
  
      return $total_flight_target;
    }
  }

  if ( ! function_exists('getNotifRecipient_byUsername')) {
    function getNotifRecipient_byUsername($username)
    {
      $CI =& get_instance();

      $CI->db->select('email');
      $CI->db->from('tb_auth_users');
      if(is_array($username)){
        $CI->db->where_in('tb_auth_users.username',$username);
      }else{
        $CI->db->where('tb_auth_users.username',$username);
      }
      // $CI->db->where('username', $username);
      $query  = $CI->db->get();
      $result = $query->result_array();
      return $result;
    }
  }

  if ( ! function_exists('getAllAnnualCostCenters')) {
    function getAllAnnualCostCenters()
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $year = find_budget_setting('Active Year');

      $connection->select(array('tb_cost_centers.cost_center_name','tb_annual_cost_centers.id'));
      $connection->from('tb_annual_cost_centers');
      $connection->join('tb_cost_centers','tb_cost_centers.id=tb_annual_cost_centers.cost_center_id');
      $connection->order_by('cost_center_name', 'ASC');
      $connection->where('tb_annual_cost_centers.year_number', $year);
      $query  = $connection->get();
      $result = $query->result_array();
      return $result;
    }
  }

  if ( ! function_exists('list_user_as_roles_level')) {
    function list_user_as_roles_level($level)
    {
      $CI =& get_instance();

      $CI->db->select('tb_auth_users.username,tb_auth_users.person_name');
      $CI->db->from('tb_auth_users');
      $CI->db->where('tb_auth_users.auth_level', $level);
      $CI->db->order_by('tb_auth_users.username', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('available_levels')) {
    function available_levels($level = NULL)
    {
      $CI =& get_instance();
  
      $CI->db->select('level');
      $CI->db->from('tb_master_levels');
  
      if ($level !== NULL){
        if (is_array($level)){
          $CI->db->where_not_in('level', $level);
        } else {
          $CI->db->where('level != ', $level);
        }
      }
  
      $query  = $CI->db->get();
      $result = $query->result_array();
      $return = array();
  
      foreach ($result as $row) {
        $return[] = $row['level'];
      }

      return $return;
    }
  }

  if ( ! function_exists('findListCostCenter')) {
    function findListCostCenter()
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select('tb_cost_center_groups.*');
      $connection->from( 'tb_cost_center_groups' );


      $query  = $connection->get();
      $result = $query->result_array();


      // $query  = $connection->get();
      // $result = $query->unbuffered_row('array');

      return $result;

      // $return = '/INV/'. $category['code'] .'/'. find_budget_setting('Active Year');

      //edit
    }
  }
  
  if ( ! function_exists('findCostCenter')) {
    function findCostCenter($annual_cost_center_id)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('cost_center_code','cost_center_name','department_id', 'tb_departments.department_name', 'group_id'));
      $connection->from( 'tb_cost_centers' );
      $connection->join('tb_annual_cost_centers','tb_annual_cost_centers.cost_center_id=tb_cost_centers.id');
      $connection->join('tb_departments','tb_cost_centers.department_id=tb_departments.id');
      $connection->where('tb_annual_cost_centers.id', $annual_cost_center_id);

      $query    = $connection->get();
      $cost_center = $query->unbuffered_row('array');

      // $return = '/INV/'. $category['code'] .'/'. find_budget_setting('Active Year');

      //edit
      

      return $cost_center;
    }
  }

  if ( ! function_exists('occupation_list')) {
    function occupation_list()
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_positions.*');
      $CI->db->from('tb_master_positions');
      $CI->db->order_by('tb_master_positions.position', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('level_list')) {
    function level_list()
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_levels.*');
      $CI->db->from('tb_master_levels');
      $CI->db->order_by('tb_master_levels.level', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('destination_list')) {
    function destination_list()
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_business_trip_destinations.*');
      $CI->db->where('tb_master_business_trip_destinations.deleted_at is NULL',null,false);
      $CI->db->from('tb_master_business_trip_destinations');
      $CI->db->order_by('tb_master_business_trip_destinations.business_trip_destination', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('transportation_list')) {
    function transportation_list()
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_transportations.*');
      $CI->db->where('tb_master_transportations.status','AVAILABLE');
      $CI->db->from('tb_master_transportations');
      $CI->db->order_by('tb_master_transportations.transportation', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('getUserById')) {
    function getUserById($user_id)
    {
      $CI =& get_instance();

      $CI->db->select('tb_auth_users.*');
      $CI->db->where('tb_auth_users.user_id',  $user_id);

      $query  = $CI->db->get('tb_auth_users');
      $result = $query->unbuffered_row('array');

      return $result;
    }
  }

  if ( ! function_exists('findUserByUsername')) {
    function findUserByUsername($username)
    {
      $CI =& get_instance();

      $CI->db->select('tb_auth_users.*');
      $CI->db->where('tb_auth_users.username',  $username);

      $query  = $CI->db->get('tb_auth_users');
      $result = $query->unbuffered_row('array');

      return $result;
    }
  }

  if ( ! function_exists('findEmployeeByUserId')) {
    function findEmployeeByUserId($id)
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_employees.*');
      $CI->db->where('tb_master_employees.user_id',$id);

      $query  = $CI->db->get('tb_master_employees');
      $result = $query->unbuffered_row('array');

      return $result;
    }
  }

  if ( ! function_exists('findPositionByEmployeeNumber')) {
    function findPositionByEmployeeNumber($employee_number)
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_employees.*');
      $CI->db->where('tb_master_employees.employee_number',  $employee_number);

      $query  = $CI->db->get('tb_master_employees');
      $result = $query->unbuffered_row('array');

      return $result;
    }
  }

  if ( ! function_exists('list_username_in_head_department')) {
    function list_username_in_head_department($department_id)
    {
      $CI =& get_instance();

      $CI->db->select('tb_head_department.username,tb_auth_users.person_name');
      $CI->db->from('tb_head_department');
      $CI->db->join('tb_auth_users','tb_auth_users.username=tb_head_department.username');
      $CI->db->where('tb_head_department.department_id', $department_id);
      $CI->db->where('tb_head_department.status', 'active');
      $CI->db->order_by('tb_head_department.username', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();
      
      $return = array();
      foreach ($result as $key) {
        $return[] = $key['username'];
      }

      return $return;
    }

  }

  if ( ! function_exists('available_department')) {
    function available_department()
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('tb_departments.*'));
      $connection->from( 'tb_departments' );

      $query  = $connection->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('getDepartmentById')) {
    function getDepartmentById($id)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('tb_departments.*'));
      $connection->from( 'tb_departments' );
      $connection->where('id', $id);

      $query  = $connection->get();
      $result = $query->unbuffered_row('array');

      return $result;
    }
  }

  if ( ! function_exists('available_employee')) {
    function available_employee($department_id=NULL)
    {
      $CI =& get_instance();
  
      $CI->db->select('*');
      $CI->db->from('tb_master_employees');  

      // Temporary
      
      // if ($department_id !== NULL){
      //   if (is_array($department_id)){
      //     $CI->db->where_in('department_id', $department_id);
      //   } else {
      //     $CI->db->where('department_id', $department_id);
      //   }
      // }
      
      $CI->db->order_by('name', 'ASC');
  
      $query = $CI->db->get();
  
      return $query->result_array();
    }
  }

  if ( ! function_exists('available_expense_reimbursement')) {
    function available_expense_reimbursement($id_benefit)
    {
      $CI =& get_instance();
      $CI->db->select('*');
      $CI->db->from('tb_master_expense_reimbursement');  
      $CI->db->where('id_benefit', $id_benefit);  
      
      
      $CI->db->order_by('expense_name', 'ASC');
  
      $query = $CI->db->get();
  
      return $query->result_array();
    }
  }

  if ( ! function_exists('getEmployeeByEmployeeNumber')) {
    function getEmployeeByEmployeeNumber($employee_number)
    {
      $CI =& get_instance();
  
      $CI->db->select('*');
      $CI->db->from('tb_master_employees');  
      $CI->db->where('employee_number', $employee_number);  
      $query = $CI->db->get();
  
      return $query->unbuffered_row('array');
    }
  }

  if ( ! function_exists('getEmployeeById')) {
    function getEmployeeById($id)
    {
      $CI =& get_instance();
  
      $CI->db->select('*');
      $CI->db->from('tb_master_employees');  
      $CI->db->where('user_id', $id);  
      $query = $CI->db->get();
  
      return $query->unbuffered_row('array');
    }
  }
  

  if ( ! function_exists('getDefaultExpenseName')) {
    function getDefaultExpenseName()
    {
      $CI =& get_instance();
  
      $CI->db->select('tb_master_expense_duty.expense_name');
      $CI->db->from('tb_master_expense_duty');
      $CI->db->where('tb_master_expense_duty.status', 'AVAILABLE');

      $query = $CI->db->get();

      foreach ($query->result_array() as $key => $value) {            
        $row[$key]['expense_name'] = $value['expense_name'];
      }

      return $row;
    }
  }
  
  if ( ! function_exists('destination_list_expense_by_id_and_level')) {
    function destination_list_expense($id,$level)
    {
      $CI =& get_instance();

      $CI->db->select(array('tb_master_business_trip_destination_items.*','tb_master_expense_duty.account_code'));   
      $CI->db->where('tb_master_business_trip_destination_items.deleted_at is NULL',null,false);
      $CI->db->where('tb_master_business_trip_destination_items.business_trip_purposes_id',$id);
      $CI->db->where('tb_master_business_trip_destination_items.level',$level);
      $CI->db->from('tb_master_business_trip_destination_items');   
      $CI->db->join('tb_master_expense_duty','tb_master_expense_duty.expense_name = tb_master_business_trip_destination_items.expense_name');
      $CI->db->order_by('tb_master_business_trip_destination_items.expense_name', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('getLevelByPosition')) {
    function getLevelByPosition($position)
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_positions.level');
      $CI->db->where('tb_master_positions.position',$position);
      $CI->db->from('tb_master_positions');
      $CI->db->order_by('tb_master_positions.position', 'ASC'); 

      $query  = $CI->db->get();
      $result = $query->unbuffered_row('array');
      $return = $result['level'];

      return $return;
    }
  }

  if ( ! function_exists('getEmployeeBenefitByOccupation')) {
    function getEmployeeBenefitByOccupation($position)
    {
      $CI =& get_instance();

      $data_selected = array(
        'tb_master_employee_benefit_items.id', 
        'tb_master_employee_benefit_items.year', 
        'tb_master_employee_benefit_items.amount', 
        'tb_master_employee_benefits.employee_benefit'
      );

      $CI->db->select($data_selected);
      $CI->db->from('tb_master_employee_benefit_items');
      $CI->db->join('tb_master_employee_benefits','tb_master_employee_benefit_items.employee_benefit_id = tb_master_employee_benefits.id');
      $CI->db->join('tb_master_levels','tb_master_employee_benefit_items."level" = tb_master_levels.level');
      $CI->db->join('tb_master_positions','tb_master_levels."level" = tb_master_positions.level');
      $CI->db->where('tb_master_positions.position',$position);    
      $CI->db->where('tb_master_employee_benefit_items.deleted_at IS NULL', null, false);

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('isDepartmentExists')) {
    function isDepartmentExists($department_name)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('tb_departments.*'));
      $connection->from('tb_departments');
      $connection->where('UPPER(department_name)', strtoupper($department_name));

      $query  = $connection->get();

      return ( $query->num_rows() > 0 ) ? true : false;
    }
  }

  if ( ! function_exists('getBenefitType')) {
    function getBenefitType()
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->from('tb_master_benefit_type');
      $CI->db->order_by('tb_master_benefit_type.id', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }

  if ( ! function_exists('getBenefitCategory')) {
    function getBenefitCategory()
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->where('tb_master_benefit_category.status','AVAILABLE');
      $CI->db->from('tb_master_benefit_category');
      $CI->db->order_by('tb_master_benefit_category.id', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }
  

  if ( ! function_exists('getBenefits')) {
    function getBenefits($employee)
    {

      // $selected_person     = getEmployeeByEmployeeNumber($employee);
      // $gender              = $selected_person['gender'];



      if(in_array(config_item('auth_username'),list_username_in_head_department(11))){
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->where('tb_master_employee_benefits.status','AVAILABLE');
        $CI->db->where('tb_master_employee_benefits.reimbursement','t');
        $CI->db->where('tb_master_employee_benefits.deleted_by IS NULL', null, false);
        // $CI->db->where('tb_master_employee_benefits.spesific_gender', $gender);
        // $CI->db->or_where('tb_master_employee_benefits.spesific_gender IS NULL', null, false);
        $CI->db->from('tb_master_employee_benefits');
        $CI->db->order_by('tb_master_employee_benefits.employee_benefit', 'ASC');
  
        $query  = $CI->db->get();
        $result = $query->result_array();
        return $result;
      } else {
        $CI =& get_instance();

        $CI->db->select('*');
        $CI->db->where('tb_master_employee_benefits.status','AVAILABLE');
        $CI->db->where('tb_master_employee_benefits.benefit_code !=','B4');
        // $CI->db->where('tb_master_employee_benefits.spesific_gender', $gender);
        // $CI->db->where('tb_master_employee_benefits.spesific_gender IS NULL', null, false);
        $CI->db->where('tb_master_employee_benefits.reimbursement','t');
        $CI->db->where('tb_master_employee_benefits.deleted_by IS NULL', null, false);
        $CI->db->from('tb_master_employee_benefits');
        $CI->db->order_by('tb_master_employee_benefits.employee_benefit', 'ASC');
  
        $query  = $CI->db->get();
        $result = $query->result_array();
        return $result;
      }
      

      // $CI->db->select(array(
      //     'tb_employee_contracts.start_date',
      //     'tb_employee_contracts.end_date',
      //     'tb_employee_has_benefit.id',
      //     'tb_employee_has_benefit.amount_plafond',
      //     'tb_employee_has_benefit.used_amount_plafond',
      //     'tb_employee_has_benefit.left_amount_plafond',
      //     'tb_master_employee_benefits.employee_benefit'
      // ));
      // $CI->db->join('tb_employee_contracts', 'tb_employee_contracts.id = tb_employee_has_benefit.employee_contract_id');
      // $CI->db->join('tb_master_employee_benefits', 'tb_master_employee_benefits.id = tb_employee_has_benefit.employee_benefit_id');
      // $CI->db->where('tb_employee_has_benefit.employee_number',$employee_number);
      // $CI->db->from('tb_employee_has_benefit');
      // $query  = $CI->db->get();
      // $result = $query->result_array();
    }
  }

  

  if ( ! function_exists('getDepartmentByName')) {
    function getDepartmentByName($department_name)
    {
      $CI =& get_instance();

      $connection = $CI->load->database('budgetcontrol', TRUE);

      $connection->select(array('tb_departments.*'));
      $connection->from('tb_departments');
      $connection->where('UPPER(department_name)', strtoupper($department_name));

      $query  = $connection->get();
      $result = $query->unbuffered_row('array');

      return $result;
    }
  }
  
  if ( ! function_exists('getNotesFromSigner')) {
    function getNotesFromSigner($document_id,$document_type,$document_status)
    {
      $CI =& get_instance();

      $CI->db->select('tb_signers.*');
      $CI->db->where('tb_signers.document_id',$document_id);
      $CI->db->where('tb_signers.document_type',$document_type);
      if($document_status=='REJECTED'){
        $CI->db->where('tb_signers.action','rejected by');
      }else{
        $CI->db->where_not_in('tb_signers.action',['rejected by']);
      }
      $CI->db->where('tb_signers.notes IS NOT NULL', null, false);
      $CI->db->from('tb_signers');

      $query  = $CI->db->get();
      $result = $query->result_array();

      $return = '';
      foreach($result as $key => $item){
        $return .= $item['person_name'].' ('.$item['roles'].') : '.$item['notes'].' ';
      }

      return $return;
    }
  }


  if ( ! function_exists('getAircraftByRegisterNumber')) {
    function getAircraftByRegisterNumber($aircraft_register_number)
    {   
      $CI =& get_instance();

      $CI->db->from('tb_master_pesawat');
      $CI->db->where('tb_master_pesawat.nama_pesawat',$aircraft_register_number);
  
      $query  = $CI->db->get();
      $result = $query->unbuffered_row('array');
  
      return $result;
    }
  }

  if ( ! function_exists('getAircraftComponentById')) {
    function getAircraftComponentById($id)
    {
      $CI =& get_instance();

      $CI->db->from('tb_aircraft_components');
      $CI->db->where('tb_aircraft_components.id',$id);
  
      $query  = $CI->db->get();
      $result = $query->unbuffered_row('array');
  
      return $result;
    }
  }

  if ( ! function_exists('getContractByEmployeeNumber')) {
    function getContractByEmployeeNumber($employee_number)
    {
      $CI =& get_instance();

      $CI->db->select('*');
      $CI->db->where('employee_number',$employee_number);
      $CI->db->from('tb_employee_contracts');
  
      $query  = $CI->db->get();
      $result = $query->result_array();
  
      return $result;
    }
  }

  if ( ! function_exists('benefit_list')) {
    function benefit_list()
    {
      $CI =& get_instance();

      $CI->db->select('tb_master_employee_benefits.*');
      $CI->db->where('reimbursement', 't');
      $CI->db->from('tb_master_employee_benefits');
      $CI->db->order_by('tb_master_employee_benefits.employee_benefit', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array();

      return $result;
    }
  }
  if ( ! function_exists('getBenefitsByEmployeeNumber')) {
      function getBenefitsByEmployeeNumber($employee_number, $year = '2025') {
        $CI =& get_instance();
        $CI->db->select('
            benefit_items.id AS benefit_item_id,
            benefits.id AS benefit_id,
            benefits.employee_benefit,
            benefits.spesific_gender,
            benefit_items.level,
            benefit_items.year,
            benefit_items.amount,
            benefit_items.deleted_by,
            benefits.status
        ');
        $CI->db->from('tb_master_employee_benefit_items AS benefit_items');
        $CI->db->join('tb_master_employee_benefits AS benefits', 'benefit_items.employee_benefit_id = benefits.id', 'left');
        $CI->db->join('tb_master_levels AS levels', 'benefit_items.level = levels.level', 'left');
        $CI->db->join('tb_master_employees AS employees', 'employees.level_id = levels.id', 'right');
        $CI->db->where('employees.employee_number', $employee_number);
        $CI->db->where('benefit_items.year', $year);
        $CI->db->where('benefit_items.deleted_by IS NULL', null, false); // Prevent escaping IS NULL
        $CI->db->where('benefits.status', 'AVAILABLE');
        $CI->db->group_start(); // Start grouping OR conditions
        $CI->db->where('benefits.spesific_gender',  $gender);
        $CI->db->or_where('benefits.spesific_gender IS NULL', null, false); // Prevent escaping IS NULL
        $CI->db->group_end(); // End grouping OR conditions
    
        $query = $CI->db->get();
        $result = $query->result_array(); // Fetch the results as an array
    
        return $result; // Return the query results
    }
  
  // function getBenefitsByEmployeeNumber($employee_number) {
  //     $CI =& get_instance();
  //     $CI->db->select('
  //       benefit_items.id AS benefit_item_id,
  //       benefits.id AS benefit_id,
  //       benefits.employee_benefit,
  //       benefits.spesific_gender,        
  //       benefit_items.level,
  //       benefit_items.year,
  //       benefit_items.amount
  //     ');
  //       $CI->db->from('tb_master_employee_benefit_items AS benefit_items');
  //       $CI->db->join('tb_master_employee_benefits AS benefits', 'benefit_items.employee_benefit_id = benefits.id', 'inner');
  //       $CI->db->join('tb_master_levels AS levels', 'benefit_items.level = levels.level', 'inner');
  //       $CI->db->join('tb_master_employees AS employees', 'employees.level_id = levels.id', 'inner');
  //       $CI->db->where('employees.employee_number', $employee_number);
  //       $CI->db->group_start(); // Start grouping the conditions
  //       $CI->db->where('benefits.spesific_gender', $gender); // Match gender
  //       $CI->db->or_where('benefits.spesific_gender IS NULL', null, false); // Match NULL
  //       $CI->db->group_end(); // End grouping
        
  //       $query = $CI->db->get();
  //       $result = $query->result_array(); // Fetch results

  //       return $result; // Mengembalikan array hasil query
  //   }
  }

  if ( ! function_exists('isEmployeeContractActiveExist')) {
    function isEmployeeContractActiveExist($employee_number)
    {
      $CI =& get_instance();

      $CI->db->from('tb_employee_contracts');
      $CI->db->where('employee_number', $employee_number);
      $CI->db->where('tb_employee_contracts.status', 'ACTIVE');    

      $query = $CI->db->get();

      return ( $query->num_rows() > 0 ) ? true : false;
    }
  }

  if ( ! function_exists('findContractActive')) {
    function findContractActive($employee_number)
    {
      $CI =& get_instance();

      $CI->db->select(array(
        'tb_employee_contracts.*'
      ));
      $CI->db->where('tb_employee_contracts.employee_number', $employee_number);
      $CI->db->where('tb_employee_contracts.status', 'ACTIVE');
      $query      = $CI->db->get('tb_employee_contracts');
      $row        = $query->unbuffered_row('array');
  
      return $row;
    }
  }

  if ( ! function_exists('findDetailBenefit')) {
    function findDetailBenefit($employee_benefit)
    {
      $CI =& get_instance();

      $CI->db->select(array(
        'tb_master_employee_benefits.*'
      ));
      $CI->db->where('tb_master_employee_benefits.employee_benefit', $employee_benefit);
      $CI->db->where('tb_master_employee_benefits.status', 'AVAILABLE');
      $query      = $CI->db->get('tb_master_employee_benefits');
      $row        = $query->unbuffered_row('array');
  
      return $row;
    }
  }

  if ( ! function_exists('get_count_revisi')) {
    function get_count_revisi($document_number,$tipe)
    {
      $CI =& get_instance();

      if($tipe=='SPD'){
        $CI->db->select('document_number');
        $CI->db->from('tb_business_trip_purposes');
        $CI->db->like('document_number', $document_number, 'both');
    
        $query  = $CI->db->get();
        $row    = $query->num_rows();
        $return = $row;
      }elseif($tipe=='PAYMENT'){
        $CI->db->select('document_number');
        $CI->db->from('tb_po_payment_no_transaksi');
        $CI->db->like('document_number', $document_number, 'both');
    
        $query  = $CI->db->get();
        $row    = $query->num_rows();
        $return = $row;
      }elseif($tipe=='REIMBURSEMENT'){
        $CI->db->select('document_number');
        $CI->db->from('tb_reimbursements');
        $CI->db->like('document_number', $document_number, 'both');
    
        $query  = $CI->db->get();
        $row    = $query->num_rows();
        $return = $row;
      }elseif($tipe=='SPPD'){
        $CI->db->select('document_number');
        $CI->db->from('tb_sppd');
        $CI->db->like('document_number', $document_number, 'both');
    
        $query  = $CI->db->get();
        $row    = $query->num_rows();
        $return = $row;
      }
  
  
      
  
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

  if ( ! function_exists('getNotifRecipientFinManager')) {
    function getNotifRecipientFinManager()
    {
      $CI =& get_instance();
  
      $head_dept = array();
  
      foreach (list_user_in_head_department(11) as $head) {
        $head_dept[] = $head['username'];
      }
  
      $CI->db->select('email');
      $CI->db->from('tb_auth_users');
      $CI->db->where_in('auth_level', ['14']);
      $query  = $CI->db->get();
      $result = $query->result_array();
      return $result;
    }
  }

  if ( ! function_exists('getNotifRecipientByRoleLevel')) {
    function getNotifRecipientByRoleLevel($level)
    {
      $CI =& get_instance();
  
      $CI->db->select('email');
      $CI->db->from('tb_auth_users');
      $CI->db->where_in('auth_level', [$level]);
      $query  = $CI->db->get();
      $result = $query->result_array();
      return $result;
    }
  }

  if ( ! function_exists('next_advance_document_number')) {
    function next_advance_document_number()
    {
      $CI =& get_instance();
  
      $format = '/ADV/'.date('Y');
  
      $CI->db->select_max('document_number', 'last_number');
      $CI->db->from('tb_advance_payments');
      $CI->db->like('document_number', $format);
  
      $query  = $CI->db->get();
      $row    = $query->unbuffered_row();
      $last   = $row->last_number;
      $number = substr($last, 0, 6);
      $next   = $number + 1;
      $return = sprintf('%06s', $next).$format;
  
      return $return;
    }
  }

  if ( ! function_exists('findAdvanceList')) {
    function findAdvanceList($doc_id,$type)
    {
      $CI =& get_instance();
  
      if($type=='SPPD'){
        $CI->db->select('tb_sppd.*');
        $CI->db->from('tb_sppd');
        $CI->db->where('tb_sppd.id', $doc_id);

        $query    = $CI->db->get();
        $sppd  = $query->unbuffered_row('array');

        $CI->db->select(
          'tb_advance_payments.id,
          tb_advance_payments.document_number,
          tb_advance_payments.payment_number,
          tb_advance_payments.amount_paid,
          tb_advance_payments.paid_at,
          tb_business_trip_purposes.document_number as spd_number,
          tb_advance_payments_details.document_id as spd_id,
        ');
        $CI->db->from('tb_advance_payments');
        $CI->db->join('tb_advance_payments_details', 'tb_advance_payments_details.advance_id = tb_advance_payments.id');
        $CI->db->join('tb_business_trip_purposes', 'tb_business_trip_purposes.id = tb_advance_payments_details.document_id');
        $CI->db->where('tb_business_trip_purposes.id', $sppd['spd_id']);
        $CI->db->where('tb_advance_payments.source', 'SPD');
        $CI->db->where_in('tb_advance_payments.status', ['OPEN','PAID']);
        $query    = $CI->db->get();
        foreach ($query->result_array() as $key => $value){
          $advance[$key] = $value;
          $advance[$key]['sppd_number'] = $sppd['document_number'];
          $advance[$key]['sppd_id'] = $sppd['id'];
        }

        $return = $advance;
      }
  
      return $return;
    }
  }

  if ( ! function_exists('sppd_format_number')) {
    function sppd_format_number()
    {
      $div  = config_item('document_format_divider');
      $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
      $mod  = config_item('module');
      $year = date('Y');
      $month = date('m');
  
      $return = $div . 'SPPD' . $div . 'BWD-BIFA' . $div .$month .$div .$year;
  
      return $return;
    }
  }
  
  if ( ! function_exists('sppd_last_number')) {
    function sppd_last_number()
    {
      $CI =& get_instance();
  
      $format = sppd_format_number();
  
      $CI->db->select_max('document_number', 'last_number');
      $CI->db->from('tb_sppd');
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

  if ( ! function_exists('cekSettingApproval')) {
    function cekSettingApproval($setting_name)
    {
      $CI =& get_instance();

      $CI->db->select('setting_value');
      $CI->db->where('setting_name', $setting_name);
      $query = $CI->db->get('tb_settings');
      $row = $query->row_array();

      return $row['setting_value'];
    }
  }

  if ( ! function_exists('findApprovalRejectedNotes')) {
    function findApprovalRejectedNotes($document_type,$document_number,$status)
    {
      $CI =& get_instance();      

      $CI->db->select('*');
      $CI->db->from('tb_signers');
      $CI->db->where('tb_signers.document_type', $document_type);
      $CI->db->where('tb_signers.document_number', $document_number);
      if($status=='approved'){
        $CI->db->where_not_in('tb_signers.action', ['rejected by','requested by']);
      }
      if($status=='rejected'){
        $CI->db->where_in('tb_signers.action', ['rejected by']);
      }
      $query_signers = $CI->db->get();
      $count = $query_signers->num_rows();
      $notes = '';
      $no = 1;
      foreach ($query_signers->result_array() as $key => $valuesigners) {
        
        if($no==$count){
          $notes .= $valuesigners['person_name'].' : '.$valuesigners['notes'];
        }else{
          $notes .= $valuesigners['person_name'].' : '.$valuesigners['notes'].' | ';
        }
        $no++;
      }

      return $notes;
    }
  }

  if ( ! function_exists('list_user_approval')) {
    function list_user_approval($auth_level)
    {
      $CI =& get_instance();

      $CI->db->select('tb_auth_users.username,tb_auth_users.person_name');
      $CI->db->from('tb_auth_users');
      $CI->db->where_in('tb_auth_users.auth_level', $auth_level);
      $CI->db->order_by('tb_auth_users.username', 'ASC');

      $query  = $CI->db->get();
      $result = $query->result_array(); 

      return $result;
    }
  }

  if ( ! function_exists('getUserByUserName')) {
    function getUserByUserName($username)
    {
      $CI =& get_instance();

      $CI->db->select('person_name');
      $CI->db->from('tb_auth_users');
      if(is_array($username)){
        $CI->db->where_in('tb_auth_users.username',$username);
      }else{
        $CI->db->where('tb_auth_users.username',$username);
      }
      // $CI->db->where('username', $username);
      $query  = $CI->db->get();
      $result = $query->unbuffered_row('array');
      return $result;
    }
  }

    
