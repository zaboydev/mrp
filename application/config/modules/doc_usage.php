<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['doc_usage']['visible']     = TRUE;
$config['module']['doc_usage']['main_warehouse']   = FALSE;
$config['module']['doc_usage']['parent']      = 'document';
$config['module']['doc_usage']['label']       = 'Material Slip';
$config['module']['doc_usage']['name']        = 'doc_usage';
$config['module']['doc_usage']['route']       = 'doc_usage';
$config['module']['doc_usage']['view']        = config_item('module_path') .'doc_usage/';
$config['module']['doc_usage']['language']    = 'doc_usage_lang';
$config['module']['doc_usage']['model']       = 'Doc_Usage_Model';
$config['module']['doc_usage']['table']       = 'tb_doc_usages';
$config['module']['doc_usage']['permission']  = array(
  'index'       => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'info'        => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'print'       => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'import'      => 'PIC STOCK,SUPER ADMIN',
  'save'        => 'PIC STOCK,SUPER ADMIN',
  'create'      => 'PIC STOCK,SUPER ADMIN',
  'add_item'    => 'PIC STOCK,SUPER ADMIN',
  'delete_item' => 'PIC STOCK,SUPER ADMIN',
  'delete'      => 'PIC STOCK,SUPER ADMIN',
  'edit'        => 'PIC STOCK,SUPER ADMIN',
  'show'        => 'PIC STOCK,SUPER ADMIN',
  );
