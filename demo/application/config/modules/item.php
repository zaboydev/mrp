<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['item']['visible']     = TRUE;
$config['module']['item']['main_warehouse']   = TRUE;
$config['module']['item']['parent']      = 'master';
$config['module']['item']['label']       = 'Item';
$config['module']['item']['name']        = 'item';
$config['module']['item']['route']       = 'item';
$config['module']['item']['view']        = config_item('module_path') .'item/';
$config['module']['item']['language']    = 'item_lang';
$config['module']['item']['table']       = 'tb_master_items';
$config['module']['item']['model']       = 'Item_Model';
$config['module']['item']['permission']  = array(
  'index'   => 'ADMIN,PIC PROCUREMENT,PIC STOCK,SUPERVISOR',
  'show'    => 'ADMIN,PIC STOCK,PIC PROCUREMENT,OTHER,SUPERVISOR',
  'create'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  'edit'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'info'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'save'    => 'ADMIN,PIC STOCK,SUPERVISOR',
  'import'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  'delete'  => 'ADMIN,PIC STOCK,SUPERVISOR',
  );
