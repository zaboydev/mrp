<?php
class Migration_ips_on_hold extends CI_Migration {

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
            )

        ));
        $this->dbforge->add_key('ai', TRUE);
        $this->dbforge->create_table('tb_auth_ips_on_hold', TRUE);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_ips_on_hold', TRUE);
    }
}