<?php
class Migration_denied_access extends CI_Migration {

    public function up() {
        $attributes = array('ENGINE' => 'InnoDB');
        
        $this->dbforge->add_field(array(
            'ai' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'null' => FALSE,
                'auto_increment' => TRUE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'time' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            ),
            'reason_code' => array(
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0
            )
        ));
        $this->dbforge->add_key('ai', TRUE);
        $this->dbforge->create_table('tb_auth_denied_access', TRUE, $attributes);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_denied_access');
    }
}