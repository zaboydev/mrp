<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock_opname']['visible']           = TRUE;
$config['module']['stock_opname']['main_warehouse']    = TRUE;
$config['module']['stock_opname']['parent']            = 'stock';
$config['module']['stock_opname']['label']             = 'Mutasi Stock';
$config['module']['stock_opname']['name']              = 'stock_opname';
$config['module']['stock_opname']['route']             = 'stock_opname';
$config['module']['stock_opname']['view']              = config_item('module_path') .'stock_opname/';
$config['module']['stock_opname']['language']          = 'stock_opname_lang';
$config['module']['stock_opname']['table']             = 'tb_stock_opnames';
$config['module']['stock_opname']['model']             = 'Stock_Opname_Model';
$config['module']['stock_opname']['permission']        = array(
  'index'   => 'PIC STOCK,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'create'  => 'SUPERVISOR,VP FINANCE,SUPER ADMIN',
);
