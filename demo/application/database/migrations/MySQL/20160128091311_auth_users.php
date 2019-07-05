<?php
class Migration_auth_users extends CI_Migration {

    public function up() {
        $attributes = array('ENGINE' => 'InnoDB');
        
        $this->dbforge->add_field(array(
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'username' => array(
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => TRUE,
                'default' => 'NULL'
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
            ),
            'auth_level' => array(
                'type' => 'TINYINT',
                'constraint' => 2,
                'unsigned' => TRUE,
                'null' => FALSE
            ),
            'banned' => array(
                'type' => 'ENUM("0","1")',
                'null' => FALSE,
                'default' => '0',
            ),
            'passwd' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => FALSE
            ),
            'passwd_recovery_code' => array(
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => TRUE,
                'default' => 'NULL'
            ),
            'passwd_recovery_date' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => NULL
            ),
            'passwd_modified_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => NULL
            ),
            'last_login' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
                'default' => NULL
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => FALSE,
            )
        ));
        $this->dbforge->add_field('modified_at TIMESTAMP');
        
        $this->dbforge->add_key('user_id', TRUE);
        $this->dbforge->create_table('tb_auth_users', TRUE, $attributes);

        $username_key = 'ALTER TABLE tb_auth_users ADD UNIQUE (username)';
        $email_key = 'ALTER TABLE tb_auth_users ADD UNIQUE (email)';

        $this->db->query($username_key);
        $this->db->query($email_key);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_users');
    }
}