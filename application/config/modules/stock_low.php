<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_low']['visible']         = FALSE;
$config['module']['stock_low']['main_warehouse']  = TRUE;
$config['module']['stock_low']['parent']          = 'stock';
$config['module']['stock_low']['label']           = 'Low Stock Materials 2';
$config['module']['stock_low']['name']            = 'stock_low';
$config['module']['stock_low']['route']           = 'stock_low';
$config['module']['stock_low']['view']            = config_item('module_path') .'stock_low/';
$config['module']['stock_low']['language']        = 'stock_low_lang';
$config['module']['stock_low']['table']           = 'tb_master_part_number';
$config['module']['stock_low']['model']           = 'Stock_Low_Model';
$config['module']['stock_low']['permission']      = array(
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'show'      => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  );
