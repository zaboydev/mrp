<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['aircraft_mapping_part']['visible']         = TRUE;
$config['module']['aircraft_mapping_part']['main_warehouse']  = TRUE;
$config['module']['aircraft_mapping_part']['parent']          = 'aircraft';
$config['module']['aircraft_mapping_part']['label']           = 'Aircraft Mapping Part';
$config['module']['aircraft_mapping_part']['name']            = 'aircraft_mapping_part';
$config['module']['aircraft_mapping_part']['route']           = 'aircraft_mapping_part';
$config['module']['aircraft_mapping_part']['view']            = config_item('module_path') .'pesawat/mapping_part';
$config['module']['aircraft_mapping_part']['language']        = 'kurs_lang';
$config['module']['aircraft_mapping_part']['table']           = 'tb_aircraft_mapping_parts';
$config['module']['aircraft_mapping_part']['model']           = 'Aircraft_Mapping_Part_Model';
$config['module']['aircraft_mapping_part']['permission']      = array(
    'create'  => 'PPC,MECHANIC,ADMIN,SUPER ADMIN,ENGINEER',
    'edit'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'info'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'save'    => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'delete'  => 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
    'index'  	=> 'PPC,MECHANIC,ADMIN,ENGINEER,SUPER ADMIN',
);
