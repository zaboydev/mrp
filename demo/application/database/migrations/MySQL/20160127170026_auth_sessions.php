<?php
class Migration_auth_sessions extends CI_Migration {

    public function up() {
        $attributes = array('ENGINE' => 'InnoDB');
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => FALSE
            ),
            'user_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'login_time' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => NULL
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'user_agent' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => TRUE,
                'default' => NULL
            )
        ));

        $this->dbforge->add_field('modified_at TIMESTAMP');
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('tb_auth_sessions', TRUE, $attributes);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_sessions');
    }
}