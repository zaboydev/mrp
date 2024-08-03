<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['pr_po']['visible']        = TRUE;
$config['module']['pr_po']['main_warehouse'] = TRUE;
$config['module']['pr_po']['parent']         = 'report';
$config['module']['pr_po']['label']          = 'PR X PO';
$config['module']['pr_po']['name']           = 'PR X PO';
$config['module']['pr_po']['route']          = 'pr_po';
$config['module']['pr_po']['view']           = config_item('module_path') .'pr_po/';
$config['module']['pr_po']['language']       = 'prl_poe_lang';
$config['module']['pr_po']['helper']         = 'material_slip_helper';
$config['module']['pr_po']['table']          = 'tb_purchase_orders';
$config['module']['pr_po']['model']          = 'Pr_Po_Model';
$config['module']['pr_po']['permission']     = array(
  'index'     => 'PROCUREMENT,SUPER ADMIN',
  'info'      => 'PROCUREMENT,SUPER ADMIN',
  'print'     => 'PROCUREMENT,SUPER ADMIN',
  'document'  => 'PROCUREMENT,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'PROCUREMENT,SUPER ADMIN',//tambhan supervisor
);
