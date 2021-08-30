<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['deliver']['visible']         = TRUE;
$config['module']['deliver']['main_warehouse']  = TRUE;
$config['module']['deliver']['parent']          = 'master';
$config['module']['deliver']['label']           = 'Deliver To';
$config['module']['deliver']['name']            = 'deliver';
$config['module']['deliver']['route']           = 'deliver';
$config['module']['deliver']['view']            = config_item('module_path') .'deliver/';
$config['module']['deliver']['language']        = 'warehouse_lang';
$config['module']['deliver']['table']           = 'tb_delivery_to';
$config['module']['deliver']['model']           = 'Deliver_Model';
$config['module']['deliver']['permission']      = array(
  'index'   => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'create'  => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'edit'    => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'info'    => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'save'    => 'PROCUREMENT,ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
  'delete'  => 'ADMIN,SUPERVISOR,VP FINANCE,SUPER ADMIN,PIC PROCUREMENT',
);
