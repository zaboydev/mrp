<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['shipping_document']['visible']          = TRUE;
$config['module']['shipping_document']['main_warehouse']   = FALSE;
$config['module']['shipping_document']['parent']           = 'document';
$config['module']['shipping_document']['label']            = 'Shipping Document';
$config['module']['shipping_document']['name']             = 'shipping_document';
$config['module']['shipping_document']['route']            = 'shipping_document';
$config['module']['shipping_document']['view']             = config_item('module_path') .'shipping_document/';
$config['module']['shipping_document']['language']         = 'shipping_document_lang';
$config['module']['shipping_document']['helper']           = 'shipping_document_helper';
$config['module']['shipping_document']['table']            = 'tb_issuances';
$config['module']['shipping_document']['model']            = 'Shipping_Document_Model';
$config['module']['shipping_document']['permission']       = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'info'      => 'PROCUREMENT,PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,VP FINANCE,FINANCE,SUPER ADMIN',
  'delete'    => 'SUPERVISOR,VP FINANCE,SUPER ADMIN',
);
