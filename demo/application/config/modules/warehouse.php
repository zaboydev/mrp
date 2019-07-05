<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['warehouse']['visible']         = TRUE;
$config['module']['warehouse']['main_warehouse']  = TRUE;
$config['module']['warehouse']['parent']          = 'master';
$config['module']['warehouse']['label']           = 'Base';
$config['module']['warehouse']['name']            = 'warehouse';
$config['module']['warehouse']['route']           = 'warehouse';
$config['module']['warehouse']['view']            = config_item('module_path') .'warehouse/';
$config['module']['warehouse']['language']        = 'warehouse_lang';
$config['module']['warehouse']['table']           = 'tb_master_warehouses';
$config['module']['warehouse']['model']           = 'Warehouse_Model';
$config['module']['warehouse']['permission']      = array(
  'index'   => 'ADMIN,SUPERVISOR',
  'create'  => 'ADMIN,SUPERVISOR',
  'edit'    => 'ADMIN,SUPERVISOR',
  'info'    => 'ADMIN,SUPERVISOR',
  'save'    => 'ADMIN,SUPERVISOR',
  'delete'  => 'ADMIN,SUPERVISOR',
  );
