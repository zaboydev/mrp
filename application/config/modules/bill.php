<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['bill']['visible']         = TRUE;
$config['module']['bill']['main_warehouse']  = TRUE;
$config['module']['bill']['parent']          = 'master';
$config['module']['bill']['label']           = 'Bill To';
$config['module']['bill']['name']            = 'bill';
$config['module']['bill']['route']           = 'bill';
$config['module']['bill']['view']            = config_item('module_path') .'bill/';
$config['module']['bill']['language']        = 'warehouse_lang';
$config['module']['bill']['table']           = 'tb_bill_to';
$config['module']['bill']['model']           = 'Bill_Model';
$config['module']['bill']['permission']      = array(
  'index'   => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'create'  => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'edit'    => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'info'    => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'save'    => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'delete'  => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
);
