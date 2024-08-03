<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['category']['visible']     = TRUE;
$config['module']['category']['main_warehouse']   = TRUE;
$config['module']['category']['parent']      = 'master';
$config['module']['category']['label']       = 'Item Category';
$config['module']['category']['name']        = 'item_category';
$config['module']['category']['route']       = 'item_category';
$config['module']['category']['view']        = config_item('module_path') .'item_category/';
$config['module']['category']['language']    = 'item_category_lang';
$config['module']['category']['table']       = 'tb_master_item_categories';
$config['module']['category']['model']       = 'Item_Category_Model';
$config['module']['category']['permission']  = array(
  'index'   => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'create'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'import'  => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'edit'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'info'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'save'    => 'PROCUREMENT,ADMIN,PIC PROCUREMENT,FINANCE,SUPER ADMIN',
  'delete'  => 'ADMIN,FINANCE,SUPER ADMIN',
  );
