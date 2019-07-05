<?php
class Migration_auth_sessions extends CI_Migration {

    public function up() {
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'VARCHAR',
                'constraint' => 40,
                'null' => FALSE
            ),
            'user_id' => array(
                'type' => 'BIGINT',
                'null' => FALSE
            ),
            'login_time' => array(
                'type' => 'TIMESTAMP',
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
            ),
            'modified_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => 'now()'
                
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('tb_auth_sessions', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_sessions', TRUE);
    }
}