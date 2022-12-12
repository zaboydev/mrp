<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['aircraft_robbing_part']['visible']         = TRUE;
$config['module']['aircraft_robbing_part']['main_warehouse']  = TRUE;
$config['module']['aircraft_robbing_part']['parent']          = 'aircraft';
$config['module']['aircraft_robbing_part']['label']           = 'Aircraft Robbing Part';
$config['module']['aircraft_robbing_part']['name']            = 'aircraft_robbing_part';
$config['module']['aircraft_robbing_part']['route']           = 'aircraft_robbing_part';
$config['module']['aircraft_robbing_part']['view']            = config_item('module_path') .'pesawat/robbing_part';
$config['module']['aircraft_robbing_part']['language']        = 'kurs_lang';
$config['module']['aircraft_robbing_part']['table']           = 'tb_aircraft_robbing_parts';
$config['module']['aircraft_robbing_part']['model']           = 'Aircraft_Robbing_Part_Model';
$config['module']['aircraft_robbing_part']['permission']      = array(
    'create'    => 'PPC,MECHANIC,ADMIN,SUPER ADMIN,ENGINEER',
    'edit'      => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'info'      => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'save'      => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'delete'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'index'  	=> 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'install'   => 'PPC,MECHANIC,ADMIN,SUPER ADMIN,ENGINEER',
);
