<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['stock']['visible']         = TRUE;
$config['module']['stock']['main_warehouse']  = FALSE;
$config['module']['stock']['parent']          = 'document';
$config['module']['stock']['label']           = 'Current Stock';
$config['module']['stock']['name']            = 'stock';
$config['module']['stock']['route']           = 'stock';
$config['module']['stock']['view']            = config_item('module_path') .'stock/';
$config['module']['stock']['language']        = 'stock_lang';
$config['module']['stock']['helper']          = 'material_slip_helper';
$config['module']['stock']['table']           = 'tb_stocks';
$config['module']['stock']['model']           = 'Stock_Model';
$config['module']['stock']['permission']      = array(
  'index'         => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE',
  'info'          => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE',
  // 'mix'           => 'PIC STOCK,SUPERVISOR,VP FINANCE',
  // 'adjustment'    => 'FINANCE,SUPERVISOR,VP FINANCE',
  // 'relocation'    => 'SUPERVISOR,VP FINANCE',
  'import'        => 'SUPERVISOR,VP FINANCE',
);
