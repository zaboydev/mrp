<?php
class Migration_auth_triggers extends CI_Migration {

    public function up() {

        $create_null_safe_cmp = 'CREATE OR REPLACE FUNCTION null_safe_cmp (varchar, varchar) RETURNS int '
                              . 'IMMUTABLE LANGUAGE SQL AS $$ '
                              . 'SELECT CASE '
                              . 'WHEN $1 IS NULL AND $2 IS NULL THEN 1 '
                              . 'WHEN ($1 IS NULL AND $2 IS NOT NULL)'
                              . 'OR ($1 IS NOT NULL AND $2 IS NULL) THEN 0 '
                              . 'ELSE CASE WHEN $1 = $2 THEN 1 ELSE 0 END '
                              . 'END;'
                              . '$$;';

        $create_procedure = 'CREATE OR REPLACE FUNCTION ca_passwd_modified() RETURNS trigger '
                          . 'LANGUAGE PLPGSQL AS $$ '
                          . 'BEGIN '
                          . 'IF (null_safe_cmp(NEW.passwd, OLD.passwd) = 0) THEN '
                          . 'NEW.passwd_modified_at := current_timestamp; '
                          . 'END IF;'
                          . 'RETURN NEW;'
                          . 'END;'
                          . '$$;';

        
        
        $drop_trigger = 'DROP TRIGGER IF EXISTS ca_passwd_trigger ON tb_auth_users';
        $create_trigger = 'CREATE TRIGGER ca_passwd_trigger '
                        . 'BEFORE UPDATE ON tb_auth_users '
                        . 'FOR EACH ROW EXECUTE PROCEDURE ca_passwd_modified()';


        $this->db->query($drop_trigger);
        $this->db->query($create_null_safe_cmp);
        $this->db->query($create_procedure);
        $this->db->query($create_trigger);
    }

    public function down() {
        $drop_trigger = 'DROP TRIGGER IF EXISTS ca_passwd_trigger ON tb_auth_users';
        $this->db->query($drop_trigger);
    }
}