<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['usage_jurnal']['visible']        = TRUE;
$config['module']['usage_jurnal']['main_warehouse'] = TRUE;
$config['module']['usage_jurnal']['parent']         = 'accounting';
$config['module']['usage_jurnal']['label']          = 'Journal';
$config['module']['usage_jurnal']['name']           = 'Journal';
$config['module']['usage_jurnal']['route']          = 'usage_jurnal';
$config['module']['usage_jurnal']['view']           = config_item('module_path') .'usage_jurnal/';
$config['module']['usage_jurnal']['language']       = 'jurnal_lang';
$config['module']['usage_jurnal']['helper']         = 'material_slip_helper';
$config['module']['usage_jurnal']['table']          = 'tb_jurnal';
$config['module']['usage_jurnal']['model']          = 'Usage_Jurnal_Model';
$config['module']['usage_jurnal']['permission']     = array(
  'index'     => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'info'      => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'print'     => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'document'  => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN', //tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',
  'import'    => 'FINANCE,CHIEF OF FINANCE,VP FINANCE,SUPER ADMIN',//tambhan supervisor
);
