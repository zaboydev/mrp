<?php
class Migration_login_errors extends CI_Migration {

    public function up() {
        
        $this->dbforge->add_field(array(
            'ai' => array(
                'type' => 'SERIAL',
            ),
            'username_or_email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'time' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE
            )
        ));
        $this->dbforge->add_key('ai', TRUE);
        $this->dbforge->create_table('tb_auth_login_errors', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_login_errors', TRUE);
    }
}