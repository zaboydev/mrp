<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['annual_cost_centers']['visible']        = TRUE;
$config['module']['annual_cost_centers']['main_warehouse'] = TRUE;
$config['module']['annual_cost_centers']['parent']         = 'master';
$config['module']['annual_cost_centers']['label']          = 'Annual Cost Centers';
$config['module']['annual_cost_centers']['name']           = 'Annual Cost Centers';
$config['module']['annual_cost_centers']['route']          = 'Annual_Cost_Centers';
$config['module']['annual_cost_centers']['view']           = config_item('module_path') .'annual_cost_centers/';
$config['module']['annual_cost_centers']['language']       = 'account_payable_lang';
$config['module']['annual_cost_centers']['helper']         = 'capex_request_helper';
$config['module']['annual_cost_centers']['table']          = 'tb_annual_cost_centers';
$config['module']['annual_cost_centers']['model']          = 'Annual_Cost_Centers_Model';
$config['module']['annual_cost_centers']['permission']     = array(
  'index'   => 'ADMIN,SUPER ADMIN',
  'create'  => 'ADMIN,SUPER ADMIN',
  'import'  => 'ADMIN,SUPER ADMIN',
  'edit'    => 'ADMIN,SUPER ADMIN',
  'info'    => 'ADMIN,SUPER ADMIN',
  'save'    => 'ADMIN,SUPER ADMIN',
  'delete'  => 'ADMIN,SUPER ADMIN',
);
