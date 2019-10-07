<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['budgeting']['visible']        = TRUE;
$config['module']['budgeting']['main_warehouse'] = TRUE;
$config['module']['budgeting']['parent']         = 'budget';
$config['module']['budgeting']['label']          = 'Budgeting';
$config['module']['budgeting']['name']           = 'Budgeting';
$config['module']['budgeting']['route']          = 'budgeting';
$config['module']['budgeting']['view']           = config_item('module_path') .'budgeting/';
$config['module']['budgeting']['language']       = 'budget_cot_lang';
$config['module']['budgeting']['helper']         = 'material_slip_helper';
$config['module']['budgeting']['table']          = 'tb_budget';
$config['module']['budgeting']['model']          = 'Budgeting_Model';
$config['module']['budgeting']['permission']     = array(
  'index'     => 'PROCUREMENT,PIC PROCUREMENT,FINANCE,VP FINANCE,CHIEF OF MAINTANCE,SUPER ADMIN',
  'approve'   => 'PROCUREMENT,SUPERVISOR,CHIEF OF MAINTANCE,SUPER ADMIN',//tambhan supervisor
  'import'		=> 'PROCUREMENT,SUPER ADMIN'
);
