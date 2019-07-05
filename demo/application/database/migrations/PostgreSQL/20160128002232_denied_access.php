<?php
class Migration_denied_access extends CI_Migration {

    public function up() {
        
        $this->dbforge->add_field(array(
            'ai' => array(
                'type' => 'SERIAL'
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => FALSE
            ),
            'time' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE
            ),
            'reason_code' => array(
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0
            )
        ));
        $this->dbforge->add_key('ai', TRUE);
        $this->dbforge->create_table('tb_auth_denied_access', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_denied_access', TRUE);
    }
}