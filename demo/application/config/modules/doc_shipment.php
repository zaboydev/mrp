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
