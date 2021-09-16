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
    if ( isset($module['permission'][$roles]) && in_array(config_item('auth_role'), (array)explode(',', $module['permission'][$roles])) ){
      return TRUE;
    }else{
      if (config_item('as_head_department')=='yes') {
        if($roles=='index'||$roles=='info'||$roles=='print'||$roles=='approval'){
          if($module['route']=='dashboard'||$module['route']=='capex_request'||$module['route']=='expense_request'||$module['route']=='inventory_request'){
            return TRUE;
          }else{
            return FALSE;
          }          
        }else{
          return FALSE;
        }
      }else{
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
          if($key=='capex_request'||$key=='expense_request'||$key=='inventory_request'){
            if ( $main_warehouse == FALSE || ( $main_warehouse == TRUE && $in_main_warehouse == TRUE ) )
              $results[$module['parent']][] = $module;
          }
        }
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
    function getAccount($currency=NULL)
    {
      $CI =& get_instance();

      $CI->db->select('group,coa');
      $CI->db->from( 'tb_master_coa' );
      if($currency!=NULL){
        $CI->db->like('group', $currency);
      }    
      $CI->db->where('category', "Bank");
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

      if($tipe=='POE'||$tipe=='PO'){
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
    function viewOrNot($status,$department_request)
    {
      if($status=='WAITING FOR HEAD DEPT'){
        if(config_item('as_head_department')=='yes'){
          if(in_array($department_request,config_item('head_department'))){
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

    
