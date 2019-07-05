<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['shipping_document_receipt']['visible']          = FALSE;
$config['module']['shipping_document_receipt']['main_warehouse']   = FALSE;
$config['module']['shipping_document_receipt']['parent']           = 'document';
$config['module']['shipping_document_receipt']['label']            = 'Received From Shipping Document';
$config['module']['shipping_document_receipt']['name']             = 'shipping_document_receipt';
$config['module']['shipping_document_receipt']['route']            = 'shipping_document_receipt';
$config['module']['shipping_document_receipt']['view']             = config_item('module_path') .'shipping_document_receipt/';
$config['module']['shipping_document_receipt']['language']         = 'shipping_document_receipt_lang';
$config['module']['shipping_document_receipt']['helper']           = 'shipping_document_receipt_helper';
$config['module']['shipping_document_receipt']['table']            = 'tb_issuances';
$config['module']['shipping_document_receipt']['model']            = 'Shipping_Document_Receipt_Model';
$config['module']['shipping_document_receipt']['permission']       = array(
  'index'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE',
  'document'  => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE',
  'info'      => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE',
  'send_back'     => 'SUPERVISOR',
  // 'delete'    => 'PIC STOCK',
);
