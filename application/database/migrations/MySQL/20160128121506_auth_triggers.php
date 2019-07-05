<?php
class Migration_auth_triggers extends CI_Migration {

    public function up() {
        $create_trigger = 'DROP TRIGGER IF EXISTS ca_passwd_trigger; '
                        . 'CREATE TRIGGER ca_passwd_trigger '
                        . 'BEFORE UPDATE ON tb_auth_users '
                        . 'FOR EACH ROW '
                        . 'BEGIN '
                        . 'IF ((NEW.passwd <=> OLD.passwd) = 0) THEN '
                        . 'SET NEW.passwd_modified_at = NOW(); '
                        . 'END IF; '
                        . 'END;';
        $this->db->query($create_trigger);
    }

    public function down() {
        $drop_trigger = 'DROP TRIGGER IF EXISTS ca_passwd_trigger';
        $this->db->query($drop_trigger);
    }
}