<?php
class Migration_Username_or_email_on_hold extends CI_Migration {

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
            'username_or_email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE
            ),
            'time' => array(
                'type' => 'DATETIME',
                'null' => FALSE
            )
        ));
        $this->dbforge->add_key('ai', TRUE);
        $this->dbforge->create_table('tb_auth_username_or_email_on_hold', TRUE, $attributes);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_username_or_email_on_hold');
    }
}