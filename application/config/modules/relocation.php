<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['relocation']['visible']         = TRUE;
$config['module']['relocation']['main_warehouse']  = FALSE;
$config['module']['relocation']['parent']          = 'document';
$config['module']['relocation']['label']           = 'Relocation';
$config['module']['relocation']['name']            = 'relocation';
$config['module']['relocation']['route']           = 'Relocation';
$config['module']['relocation']['view']            = config_item('module_path') .'stock/';
$config['module']['relocation']['language']        = 'stock_lang';
$config['module']['relocation']['helper']          = 'material_slip_helper';
$config['module']['relocation']['table']           = 'tb_stocks';
$config['module']['relocation']['model']           = 'Stock_Model';
$config['module']['relocation']['permission']      = array(
  'index'         => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE',
  'info'          => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE',
  // 'mix'           => 'PIC STOCK,SUPERVISOR,VP FINANCE',
  // 'adjustment'    => 'FINANCE,SUPERVISOR,VP FINANCE',
  'relocation'    => 'SUPERVISOR,VP FINANCE',
  // 'import'        => 'SUPERVISOR,VP FINANCE',
);
