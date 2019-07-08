<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['adjustment']['visible']         = TRUE;
$config['module']['adjustment']['main_warehouse']  = FALSE;
$config['module']['adjustment']['parent']          = 'document';
$config['module']['adjustment']['label']           = 'Adjustment';
$config['module']['adjustment']['name']            = 'adjustment';
$config['module']['adjustment']['route']           = 'Adjustment';
$config['module']['adjustment']['view']            = config_item('module_path') .'stock_adjustment/';
$config['module']['adjustment']['language']        = 'stock_lang';
$config['module']['adjustment']['helper']          = 'material_slip_helper';
$config['module']['adjustment']['table']           = 'tb_stock_adjustment';
$config['module']['adjustment']['model']           = 'Stock_Adjustment_Model';
$config['module']['adjustment']['permission']      = array(
  'index'         => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'info'          => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  // 'mix'           => 'PIC STOCK,SUPERVISOR,VP FINANCE',
  'adjustment'    => 'FINANCE,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  // 'relocation'    => 'SUPERVISOR,VP FINANCE',
  // 'import'        => 'SUPERVISOR,VP FINANCE',
);
