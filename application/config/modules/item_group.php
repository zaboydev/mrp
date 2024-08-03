<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['group']['visible']     = TRUE;
$config['module']['group']['main_warehouse']   = TRUE;
$config['module']['group']['parent']      = 'master';
$config['module']['group']['label']       = 'Item Group';
$config['module']['group']['name']        = 'item_group';
$config['module']['group']['route']       = 'item_group';
$config['module']['group']['view']        = config_item('module_path') .'item_group/';
$config['module']['group']['language']    = 'item_group_lang';
$config['module']['group']['table']       = 'tb_master_item_groups';
$config['module']['group']['model']       = 'Item_Group_Model';
$config['module']['group']['permission']  = array(
  'index'   => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'create'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'import'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'info'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'save'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,FINANCE,SUPER ADMIN',
  );
