<?php
class Migration_ci_sessions extends CI_Migration {

    public function up() {
        $attributes = array('ENGINE' => 'InnoDB');
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => FALSE
            ),

            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'null' => FALSE,
                'default' => '0'
            ),
            'data' => array(
                'type' => 'BLOB',
                'null' => FALSE
            )
        ));
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('timestamp');
        $this->dbforge->create_table('tb_auth_ci_sessions', TRUE, $attributes);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_ci_sessions');
    }
}