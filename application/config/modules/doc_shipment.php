<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['doc_shipment']['visible']     = TRUE;
$config['module']['doc_shipment']['main_warehouse']   = FALSE;
$config['module']['doc_shipment']['parent']      = 'document';
$config['module']['doc_shipment']['label']       = 'Shipping Document';
$config['module']['doc_shipment']['name']        = 'doc_shipment';
$config['module']['doc_shipment']['route']       = 'doc_shipment';
$config['module']['doc_shipment']['view']        = config_item('module_path') .'doc_shipment/';
$config['module']['doc_shipment']['language']    = 'doc_shipment_lang';
$config['module']['doc_shipment']['table']       = 'tb_doc_shipments';
$config['module']['doc_shipment']['model']       = 'Doc_Shipment_Model';
$config['module']['doc_shipment']['permission']  = array(
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
