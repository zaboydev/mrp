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
