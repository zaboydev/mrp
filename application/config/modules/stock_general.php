<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_general']['visible']           = TRUE;
$config['module']['stock_general']['main_warehouse']    = TRUE;
$config['module']['stock_general']['parent']            = 'stock';
$config['module']['stock_general']['label']             = 'Report Stock All Base';
$config['module']['stock_general']['name']              = 'stock_general';
$config['module']['stock_general']['route']             = 'stock_general';
$config['module']['stock_general']['view']              = config_item('module_path') .'stock_general/';
$config['module']['stock_general']['language']          = 'stock_general_lang';
$config['module']['stock_general']['table']             = 'tb_stocks';
$config['module']['stock_general']['model']             = 'Stock_General_Model';
$config['module']['stock_general']['permission']        = array(
  'index'       => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'show'        => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'adjustment'  => 'PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
);
