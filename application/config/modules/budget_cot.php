<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['budget_cot']['visible']        = TRUE;
$config['module']['budget_cot']['main_warehouse'] = TRUE;
$config['module']['budget_cot']['parent']         = 'budget';
$config['module']['budget_cot']['label']          = 'Budget COT';
$config['module']['budget_cot']['name']           = 'Budget COT';
$config['module']['budget_cot']['route']          = 'budget_cot';
$config['module']['budget_cot']['view']           = config_item('module_path') .'budget_cot/';
$config['module']['budget_cot']['language']       = 'budget_cot_lang';
$config['module']['budget_cot']['helper']         = 'material_slip_helper';
$config['module']['budget_cot']['table']          = 'tb_budget_cot';
$config['module']['budget_cot']['model']          = 'Budget_Cot_Model';
$config['module']['budget_cot']['permission']     = array(
  'index'     => 'PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE',
  'info'      => 'PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE',
  'print'     => 'PIC PROCUREMENT,SUPERVISOR,FINANCE,OTHER,VP FINANCE,CHIEF OF MAINTANCE',
  'document'  => 'PIC PROCUREMENT,SUPERVISOR,CHIEF OF MAINTANCE',//tambhan supervisor
  'payment'   => 'FINANCE,CHIEF OF MAINTANCE',
  'import'    => 'SUPERVISOR,VP FINANCE,CHIEF OF MAINTANCE',//tambhan supervisor
);
