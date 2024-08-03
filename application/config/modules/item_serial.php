<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['item_serial']['visible']     = FALSE;
$config['module']['item_serial']['main_warehouse']   = TRUE;
$config['module']['item_serial']['parent']      = 'master';
$config['module']['item_serial']['label']       = 'Item Serial Number';
$config['module']['item_serial']['name']        = 'item_serial';
$config['module']['item_serial']['route']       = 'item_serial/';
$config['module']['item_serial']['view']        = config_item('module_path') .'item_serial/';
$config['module']['item_serial']['language']    = 'item_serial_lang';
$config['module']['item_serial']['table']       = 'tb_master_item_serials';
$config['module']['item_serial']['model']       = 'Item_Serial_Model';
$config['module']['item_serial']['permission']  = array(
  'index'     => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'create'    => 'ADMIN,PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'show'      => 'ADMIN,PIC STOCK,PIC PROCUREMENT,OTHER,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'edit'      => 'ADMIN,PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'import'    => 'ADMIN,PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  'delete'    => 'PIC STOCK,SUPERVISOR,VP FINANCE,SUPER ADMIN',
  );
