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
  'index'   => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'create'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  'import'  => 'ADMIN,SUPERVISOR',
  'edit'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'info'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'save'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'delete'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  );
