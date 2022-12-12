<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['aircraft_component_plan']['visible']         = TRUE;
$config['module']['aircraft_component_plan']['main_warehouse']  = TRUE;
$config['module']['aircraft_component_plan']['parent']          = 'aircraft';
$config['module']['aircraft_component_plan']['label']           = 'Aircraft Component Plan';
$config['module']['aircraft_component_plan']['name']            = 'aircraft_component_plan';
$config['module']['aircraft_component_plan']['route']           = 'aircraft_component_plan';
$config['module']['aircraft_component_plan']['view']            = config_item('module_path') .'pesawat/component_plan';
$config['module']['aircraft_component_plan']['language']        = 'kurs_lang';
$config['module']['aircraft_component_plan']['table']           = 'tb_master_pesawat';
$config['module']['aircraft_component_plan']['model']           = 'Aircraft_Component_Plan_Model';
$config['module']['aircraft_component_plan']['permission']      = array(
    'create'    => 'PPC,MECHANIC,ADMIN,SUPER ADMIN,ENGINEER',
    'edit'      => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'info'      => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'save'      => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'delete'    => 'PPC,ADMIN,SUPER ADMIN',
    'index'  	=> 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
);
