<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['commercial_invoice']['visible']          = TRUE;
$config['module']['commercial_invoice']['main_warehouse']   = TRUE;
$config['module']['commercial_invoice']['parent']           = 'document';
$config['module']['commercial_invoice']['label']            = 'Commercial Invoice';
$config['module']['commercial_invoice']['name']             = 'commercial_invoice';
$config['module']['commercial_invoice']['route']            = 'commercial_invoice';
$config['module']['commercial_invoice']['view']             = config_item('module_path') .'commercial_invoice/';
$config['module']['commercial_invoice']['language']         = 'commercial_invoice_lang';
$config['module']['commercial_invoice']['helper']           = 'commercial_invoice_helper';
$config['module']['commercial_invoice']['table']            = 'tb_issuances';
$config['module']['commercial_invoice']['model']            = 'Commercial_Invoice_Model';
$config['module']['commercial_invoice']['permission']       = array(
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER',
  'index'     => 'PIC PROCUREMENT,PIC STOCK,SUPERVISOR,FINANCE,OTHER',
  'document'  => 'PIC STOCK,SUPERVISOR',
  'print'     => 'PIC STOCK,SUPERVISOR',
  'delete'    => 'SUPERVISOR',
);
