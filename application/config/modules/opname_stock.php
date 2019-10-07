<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['opname_stock']['visible']           = TRUE;
$config['module']['opname_stock']['main_warehouse']    = TRUE;
$config['module']['opname_stock']['parent']            = 'document';
$config['module']['opname_stock']['label']             = 'Opname Stock';
$config['module']['opname_stock']['name']              = 'opname_stock';
$config['module']['opname_stock']['route']             = 'Opname_Stock';
$config['module']['opname_stock']['view']              = config_item('module_path') .'stock_opname/';
$config['module']['opname_stock']['language']          = 'stock_opname_lang';
$config['module']['opname_stock']['table']             = 'tb_stock_opnames';
$config['module']['opname_stock']['model']             = 'Stock_Opname_Model';
$config['module']['opname_stock']['permission']        = array(
  'index'   			=> 'PROCUREMENT,PIC STOCK,PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'create_opname'  		=> 'SUPERVISOR,PIC STOCK,SUPER ADMIN',
  'index_unpublish'   	=> 'PIC STOCK,PIC PROCUREMENT,SUPERVISOR,FINANCE,VP FINANCE,SUPER ADMIN',
  'update_unpublish'  	=> 'SUPERVISOR,PIC STOCK,SUPER ADMIN',
  'save_unpublish'  	=> 'SUPERVISOR,PIC STOCK,SUPER ADMIN',
  'publish'				=> 'SUPERVISOR,SUPER ADMIN',
);
