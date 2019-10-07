<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['mixing']['visible']         = TRUE;
$config['module']['mixing']['main_warehouse']  = FALSE;
$config['module']['mixing']['parent']          = 'document';
$config['module']['mixing']['label']           = 'Mixing';
$config['module']['mixing']['name']            = 'mixing';
$config['module']['mixing']['route']           = 'Mixing';
$config['module']['mixing']['view']            = config_item('module_path') .'stock/';
$config['module']['mixing']['language']        = 'stock_lang';
$config['module']['mixing']['helper']          = 'material_slip_helper';
$config['module']['mixing']['table']           = 'tb_stocks';
$config['module']['mixing']['model']           = 'Stock_Model';
$config['module']['mixing']['permission']      = array(
  'index'         => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'info'          => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'mix'           => 'PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'mixing_document'           => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,SUPER ADMIN',
  'cancel_mixing'           => 'SUPERVISOR,SUPER ADMIN',
  // 'adjustment'    => 'FINANCE,SUPERVISOR,VP FINANCE',
  // 'relocation'    => 'SUPERVISOR,VP FINANCE',
  // 'import'        => 'SUPERVISOR,VP FINANCE',
);
