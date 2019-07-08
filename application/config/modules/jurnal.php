<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['jurnal']['visible']        = TRUE;
$config['module']['jurnal']['main_warehouse'] = TRUE;
$config['module']['jurnal']['parent']         = 'accounting_report';
$config['module']['jurnal']['label']          = 'Purchase Jurnal';
$config['module']['jurnal']['name']           = 'Rurchase Jurnal';
$config['module']['jurnal']['route']          = 'jurnal';
$config['module']['jurnal']['view']           = config_item('module_path') .'jurnal/';
$config['module']['jurnal']['language']       = 'jurnal_lang';
$config['module']['jurnal']['helper']         = 'material_slip_helper';
$config['module']['jurnal']['table']          = 'tb_jurnal';
$config['module']['jurnal']['model']          = 'Jurnal_Model';
$config['module']['jurnal']['permission']     = array(
  'index'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'info'      => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'print'     => 'PIC STOCK,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'document'  => 'PIC STOCK,SUPERVISOR,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'import'    => 'PIC STOCK,SUPERVISOR,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
);
