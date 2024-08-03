<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['doc_return']['visible']     = TRUE;
$config['module']['doc_return']['main_warehouse']   = FALSE;
$config['module']['doc_return']['parent']      = 'document';
$config['module']['doc_return']['label']       = 'Commercial Invoice';
$config['module']['doc_return']['name']        = 'doc_return';
$config['module']['doc_return']['route']       = 'doc_return';
$config['module']['doc_return']['view']        = config_item('module_path') .'doc_return/';
$config['module']['doc_return']['language']    = 'doc_return_lang';
$config['module']['doc_return']['table']       = 'tb_doc_returns';
$config['module']['doc_return']['model']       = 'Doc_Return_Model';
$config['module']['doc_return']['permission']  = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'create'    => 'PIC STOCK,SUPER ADMIN',
  'edit'      => 'PIC STOCK,SUPER ADMIN',
  'add_item'  => 'PIC STOCK,SUPER ADMIN',
  'del_item'  => 'PIC STOCK,SUPER ADMIN',
  'discard'   => 'PIC STOCK,SUPER ADMIN',
  'save'      => 'PIC STOCK,SUPER ADMIN',
  'receive'   => 'PIC STOCK,SUPER ADMIN',
  'show'      => 'PIC STOCK,SUPER ADMIN',
  'info'      => 'PIC STOCK,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPER ADMIN',
  'delete'    => 'PIC STOCK,SUPER ADMIN',
);
