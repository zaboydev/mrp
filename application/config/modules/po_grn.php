<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['po_grn']['visible']        = TRUE;
$config['module']['po_grn']['main_warehouse'] = TRUE;
$config['module']['po_grn']['parent']         = 'report';
$config['module']['po_grn']['label']          = 'PO X GRN';
$config['module']['po_grn']['name']           = 'PO X GRN';
$config['module']['po_grn']['route']          = 'po_grn';
$config['module']['po_grn']['view']           = config_item('module_path') .'po_grn/';
$config['module']['po_grn']['language']       = 'prl_poe_lang';
$config['module']['po_grn']['helper']         = 'material_slip_helper';
$config['module']['po_grn']['table']          = 'tb_purchase_orders';
$config['module']['po_grn']['model']          = 'Po_Grn_Model';
$config['module']['po_grn']['permission']     = array(
  'index'     => 'SUPER ADMIN',
  'info'      => 'SUPER ADMIN',
  'print'     => 'SUPER ADMIN',
  'document'  => 'SUPER ADMIN',//tambhan supervisor
  'payment'   => 'SUPER ADMIN',//tambhan supervisor
);
