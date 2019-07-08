<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['doc_receipt']['visible']     = TRUE;
$config['module']['doc_receipt']['main_warehouse']   = TRUE;
$config['module']['doc_receipt']['parent']      = 'document';
$config['module']['doc_receipt']['label']       = 'Goods Received Note';
$config['module']['doc_receipt']['name']        = 'doc_receipt';
$config['module']['doc_receipt']['route']       = 'doc_receipt';
$config['module']['doc_receipt']['view']        = config_item('module_path') .'doc_receipt/';
$config['module']['doc_receipt']['language']    = 'doc_receipt_lang';
$config['module']['doc_receipt']['table']       = 'tb_doc_receipts';
$config['module']['doc_receipt']['model']       = 'Doc_Receipt_Model';
$config['module']['doc_receipt']['permission']  = array(
  'index'       => 'PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'info'        => 'PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'print'       => 'PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'import'      => 'PIC STOCK,SUPER ADMIN',
  'save'        => 'PIC STOCK,SUPER ADMIN',
  'create'      => 'PIC STOCK,SUPER ADMIN',
  'add_item'    => 'PIC STOCK,SUPER ADMIN',
  'delete_item' => 'PIC STOCK,SUPER ADMIN',
  'delete'      => 'PIC STOCK,SUPER ADMIN',
  'edit'        => 'PIC STOCK,SUPER ADMIN',
  'show'        => 'PIC STOCK,SUPER ADMIN',
  );
