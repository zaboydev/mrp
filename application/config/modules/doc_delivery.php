<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['doc_delivery']['visible']          = TRUE;
$config['module']['doc_delivery']['main_warehouse']   = FALSE;
$config['module']['doc_delivery']['parent']           = 'document';
$config['module']['doc_delivery']['label']            = 'Internal Delivery';
$config['module']['doc_delivery']['name']             = 'doc_delivery';
$config['module']['doc_delivery']['route']            = 'doc_delivery';
$config['module']['doc_delivery']['view']             = config_item('module_path') .'doc_delivery/';
$config['module']['doc_delivery']['language']         = 'doc_delivery_lang';
$config['module']['doc_delivery']['table']            = 'tb_receipts';
// $config['module']['doc_delivery']['table']            = 'tb_doc_deliveries';
$config['module']['doc_delivery']['model']            = 'Doc_Delivery_Model';
$config['module']['doc_delivery']['permission']       = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,OTHER,SUPER ADMIN',
  'add_item'  => 'PIC STOCK,SUPER ADMIN',
  'create'    => 'PIC STOCK,SUPER ADMIN',
  'edit'      => 'PIC STOCK,SUPER ADMIN',
  'save'      => 'PIC STOCK,SUPER ADMIN',
  'info'      => 'PIC STOCK,SUPER ADMIN',
  'delete'    => 'PIC STOCK,SUPER ADMIN',
  );
