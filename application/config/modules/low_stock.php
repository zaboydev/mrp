<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['low_stock']['visible']         = TRUE;
$config['module']['low_stock']['main_warehouse']  = TRUE;
$config['module']['low_stock']['parent']          = 'stock';
$config['module']['low_stock']['label']           = 'Low Stock Material';
$config['module']['low_stock']['name']            = 'low_stock';
$config['module']['low_stock']['route']           = 'Low_Stock';
$config['module']['low_stock']['view']            = config_item('module_path') .'low_stock/';
$config['module']['low_stock']['language']        = 'low_stock_lang';
$config['module']['low_stock']['table']           = 'tb_master_part_number';
$config['module']['low_stock']['model']           = 'Low_Stock_Model';
$config['module']['low_stock']['permission']      = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'summary'   => 'PROCUREMENT,VP FINANCE,SUPERVISOR,SUPER ADMIN',
  'detail'    => 'PROCUREMENT,VP FINANCE,SUPERVISOR,SUPER ADMIN',
  'document'    => 'PIC STOCK,SUPERVISOR,SUPER ADMIN',
);
