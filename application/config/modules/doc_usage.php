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
  'index'       => 'PIC PROCUREMENT,PIC STOCK,OTHER',
  'info'        => 'PIC PROCUREMENT,PIC STOCK,OTHER',
  'print'       => 'PIC PROCUREMENT,PIC STOCK,OTHER',
  'import'      => 'PIC STOCK',
  'save'        => 'PIC STOCK',
  'create'      => 'PIC STOCK',
  'add_item'    => 'PIC STOCK',
  'delete_item' => 'PIC STOCK',
  'delete'      => 'PIC STOCK',
  'edit'        => 'PIC STOCK',
  'show'        => 'PIC STOCK',
  );
