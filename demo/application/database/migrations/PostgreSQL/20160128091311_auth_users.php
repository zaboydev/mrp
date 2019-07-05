<?php
class Migration_auth_users extends CI_Migration {

    public function up() {

        $create_enum = "CREATE TYPE banned_bool AS ENUM ('0', '1')";
        $this->db->query($create_enum);

        $this->dbforge->add_field(array(
            'user_id' => array(
                'type' => 'BIGINT',
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
                'null' => FALSE
            ),
            'banned' => array(
                'type' => 'banned_bool',
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
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => NULL
            ),
            'passwd_modified_at' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => NULL
            ),
            'last_login' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
                'default' => NULL
            ),
            'created_at' => array(
                'type' => 'TIMESTAMP',
                'null' => FALSE,
            )
        ));
        $this->dbforge->add_field('modified_at TIMESTAMP');
        
        $this->dbforge->add_key('user_id', TRUE);
        $this->dbforge->create_table('tb_auth_users', TRUE);

        $username_key = 'CREATE UNIQUE INDEX unique_username ON tb_auth_users (username)';
        $email_key = 'CREATE UNIQUE INDEX unique_email ON tb_auth_users (email)';
        $this->db->query($username_key);
        $this->db->query($email_key);
    }

    public function down() {
        $this->dbforge->drop_table('tb_auth_users', TRUE);
        $drop_enum = 'DROP TYPE IF EXISTS banned_bool';
        $this->db->query($drop_enum);
    }
}