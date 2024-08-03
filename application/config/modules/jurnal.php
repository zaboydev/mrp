<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['jurnal']['visible']        = TRUE;
$config['module']['jurnal']['main_warehouse'] = TRUE;
$config['module']['jurnal']['parent']         = 'accounting';
$config['module']['jurnal']['label']          = 'General';
$config['module']['jurnal']['name']           = 'General';
$config['module']['jurnal']['route']          = 'jurnal';
$config['module']['jurnal']['view']           = config_item('module_path') .'jurnal/';
$config['module']['jurnal']['language']       = 'jurnal_lang';
$config['module']['jurnal']['helper']         = 'material_slip_helper';
$config['module']['jurnal']['table']          = 'tb_jurnal';
$config['module']['jurnal']['model']          = 'Jurnal_Model';
$config['module']['jurnal']['permission']     = array(
  'index'     => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'info'      => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'print'     => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'document'  => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN', //tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'import'    => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',//tambhan supervisor
);
