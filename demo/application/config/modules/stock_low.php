<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_low']['visible']         = TRUE;
$config['module']['stock_low']['main_warehouse']  = TRUE;
$config['module']['stock_low']['parent']          = 'stock';
$config['module']['stock_low']['label']           = 'Low Stock Materials';
$config['module']['stock_low']['name']            = 'stock_low';
$config['module']['stock_low']['route']           = 'stock_low';
$config['module']['stock_low']['view']            = config_item('module_path') .'stock_low/';
$config['module']['stock_low']['language']        = 'stock_low_lang';
$config['module']['stock_low']['table']           = 'tb_stocks';
$config['module']['stock_low']['model']           = 'Stock_Low_Model';
$config['module']['stock_low']['permission']      = array(
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER',
  'show'      => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER',
  );
