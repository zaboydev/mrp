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
  'index'     => 'PIC PROCUREMENT,PIC STOCK,OTHER',
  'create'    => 'PIC STOCK',
  'edit'      => 'PIC STOCK',
  'add_item'  => 'PIC STOCK',
  'del_item'  => 'PIC STOCK',
  'discard'   => 'PIC STOCK',
  'save'      => 'PIC STOCK',
  'receive'   => 'PIC STOCK',
  'show'      => 'PIC STOCK',
  'info'      => 'PIC STOCK',
  'print'     => 'PIC STOCK',
  'delete'    => 'PIC STOCK',
);
