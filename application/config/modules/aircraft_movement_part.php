<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['aircraft_movement_part']['visible']         = TRUE;
$config['module']['aircraft_movement_part']['main_warehouse']  = TRUE;
$config['module']['aircraft_movement_part']['parent']          = 'aircraft';
$config['module']['aircraft_movement_part']['label']           = 'Aircraft Movement Part';
$config['module']['aircraft_movement_part']['name']            = 'aircraft_movement_part';
$config['module']['aircraft_movement_part']['route']           = 'aircraft_movement_part';
$config['module']['aircraft_movement_part']['view']            = config_item('module_path') .'pesawat/movement_part';
$config['module']['aircraft_movement_part']['language']        = 'kurs_lang';
$config['module']['aircraft_movement_part']['table']           = 'tb_master_pesawat';
$config['module']['aircraft_movement_part']['model']           = 'Aircraft_Movement_Part_Model';
$config['module']['aircraft_movement_part']['permission']      = array(
    'create'  => 'PPC,MECHANIC,ADMIN,SUPER ADMIN,ENGINEER',
    'edit'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'info'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'save'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'delete'  => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'index'  	=> 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
);
