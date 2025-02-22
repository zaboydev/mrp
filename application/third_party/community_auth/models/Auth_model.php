<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Community Auth - Auth_model Model
 *
 * Community Auth is an open source authentication application for CodeIgniter 3
 *
 * @package     Community Auth
 * @author      Robert B Gottier
 * @copyright   Copyright (c) 2011 - 2016, Robert B Gottier. (http://brianswebdesign.com/)
 * @license     BSD - http://www.opensource.org/licenses/BSD-3-Clause
 * @link        http://community-auth.com
 */

class Auth_model extends CI_Model
{
    /**
     * Check the user table to see if a user exists by username or email address.
     *
     * While this query is rather limited, you could easily join with
     * other custom tables, and return specific user profile data.
     *
     * @param $user_string
     * @return bool
     */
    public function get_auth_data( $user_string )
    {
        // Selected user table data
        $selected_columns = array(
            'username',
            'email',
            'auth_level',
            'passwd',
            'user_id',
            'banned',
            'person_name',
            'warehouse'
        );

        // User table query
        $query = $this->db->select( $selected_columns )
            ->from( config_item('user_table') )
            ->where( 'LOWER( username ) =', strtolower( $user_string ) )
            ->or_where( 'LOWER( email ) =', strtolower( $user_string ) )
            ->limit(1)
            ->get();

        if( $query->num_rows() == 1 )
            return $query->row();

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Update the user's user table record when they login
     *
     * @param $user_id
     * @param $login_time
     * @param $session_id
     */
    public function login_update( $user_id, $login_time, $session_id )
    {
        if( config_item('disallow_multiple_logins') === TRUE )
        {
            $this->db->where( 'user_id', $user_id )
                ->delete( config_item('auth_sessions_table') );
        }

        $data = array( 'last_login' => $login_time );

        $this->db->where( 'user_id' , $user_id )
            ->update( config_item('user_table') , $data );

        $data = array(
            'id'         => $session_id,
            'user_id'    => $user_id,
            'login_time' => $login_time,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->_user_agent()
        );

        $this->db->insert( config_item('auth_sessions_table') , $data );
    }

    // --------------------------------------------------------------

    /**
     * Return the user agent info for login update
     *
     * @return string
     */
    protected function _user_agent()
    {
        $this->load->library('user_agent');

        if( $this->agent->is_browser() ){
            $agent = $this->agent->browser() . ' ' . $this->agent->version();
        }else if( $this->agent->is_robot() ){
            $agent = $this->agent->robot();
        }else if( $this->agent->is_mobile() ){
            $agent = $this->agent->mobile();
        }else{
            $agent = 'Unidentified User Agent';
        }

        $platform = $this->agent->platform();

        return $platform
            ? $agent . ' on ' . $platform
            : $agent;
    }

    // -----------------------------------------------------------------------

    /**
     * Check user table and confirm there is a record where:
     *
     * 1) The user ID matches
     * 2) The login time matches
     *
     * If there is a matching record, return a specified subset of the record.
     *
     * @param $user_id
     * @param $login_time
     * @return bool
     */
    public function check_login_status( $user_id, $login_time )
    {
        // Selected user table data
        $selected_columns = array(
            'u.username',
            'u.email',
            'u.auth_level',
            'u.user_id',
            'u.banned',
            'u.person_name',
            'u.warehouse'
        );

        $this->db->select( $selected_columns )
            ->from( config_item('user_table') . ' u' )
            ->join( config_item('auth_sessions_table') . ' s', 'u.user_id = s.user_id' )
            ->where( 's.user_id', $user_id )
            ->where( 's.login_time', $login_time );

        // If the session ID was NOT regenerated, the session IDs should match
        if( is_null( $this->session->regenerated_session_id ) )
        {
            $this->db->where( 's.id', $this->session->session_id );
        }

        // If it was regenerated, we can only compare the old session ID for this request
        else
        {
            $this->db->where( 's.id', $this->session->pre_regenerated_session_id );
        }

        $this->db->limit(1);
        $query = $this->db->get();

        if( $query->num_rows() == 1 )
            return $query->row();

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Update a user's user record session ID if it was regenerated
     *
     * @param $user_id
     */
    public function update_user_session_id( $user_id )
    {
        if( ! is_null( $this->session->regenerated_session_id ) )
        {
            $this->db->where( 'user_id', $user_id )
                ->where( 'id', $this->session->pre_regenerated_session_id )
                ->update(
                    config_item('auth_sessions_table'),
                    array( 'id' => $this->session->regenerated_session_id )
                );
        }
    }

    // -----------------------------------------------------------------------

    /**
     * Clear user holds that have expired
     *
     */
    public function clear_expired_holds()
    {
        $expiration = date('Y-m-d H:i:s', time() - config_item('seconds_on_hold') );

        $this->db->delete( config_item('IP_hold_table'), array( 'time <' => $expiration ) );

        $this->db->delete( config_item('username_or_email_hold_table'), array( 'time <' => $expiration ) );
    }

    // --------------------------------------------------------------

    /**
     * Clear login errors that have expired
     *
     */
    public function clear_login_errors()
    {
        $expiration = date('Y-m-d H:i:s', time() - config_item('seconds_on_hold') );

        $this->db->delete( config_item('errors_table'), array( 'time <' => $expiration ) );
    }

    // --------------------------------------------------------------

    /**
     * Check that the IP address, username, or email address is not on hold.
     *
     * @param $recovery
     * @return bool
     */
    public function check_holds( $recovery )
    {
        $ip_hold = $this->check_ip_hold();

        $string_hold = $this->check_username_or_email_hold( $recovery );

        if( $ip_hold === TRUE OR $string_hold === TRUE )
            return TRUE;

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Check that the IP address is not on hold
     *
     * @return bool
     */
    public function check_ip_hold()
    {
        $ip_hold = $this->db->get_where(
            config_item('IP_hold_table'),
            array( 'ip_address' => $this->input->ip_address() )
        );

        if( $ip_hold->num_rows() > 0 )
            return TRUE;

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Check that the username or email address is not on hold
     *
     * @param $recovery
     * @return bool
     */
    public function check_username_or_email_hold( $recovery )
    {
        $posted_string = ( ! $recovery )
            ? $this->input->post( 'login_string' )
            : $this->input->post( 'email', TRUE );

        // Check posted string for basic validity.
        if( ! empty( $posted_string ) && strlen( $posted_string ) < 256 )
        {
            $string_hold = $this->db->get_where(
                config_item('username_or_email_hold_table'),
                array( 'username_or_email' => $posted_string )
            );

            if( $string_hold->num_rows() > 0 )
                return TRUE;
        }

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Insert a login error into the database
     *
     * @param $data
     */
    public function create_login_error( $data )
    {
        $this->db->set( $data )
            ->insert( config_item('errors_table') );
    }

    // --------------------------------------------------------------

    /**
     * Check login errors table to determine if a IP address, username,
     * or email address should be placed on hold.
     *
     * @param $string
     * @return mixed
     */
    public function check_login_attempts( $string )
    {
        $ip_address = $this->input->ip_address();

        // Check if this IP now has too many login attempts
        $count1 = $this->db->where( 'ip_address', $ip_address )
            ->count_all_results( config_item('errors_table') );

        if( $count1 == config_item('max_allowed_attempts') )
        {
            // Place the IP on hold
            $data = array(
                'ip_address' => $ip_address,
                'time'       => date('Y-m-d H:i:s')
            );

            $this->db->set( $data )
                ->insert( config_item('IP_hold_table') );
        }

        /**
         * If for some reason login attempts exceed
         * the max_allowed_attempts number, we have
         * the option of banning the user by IP address.
         */
        else if(
            $count1 > config_item('max_allowed_attempts') &&
            $count1 >= config_item('deny_access_at')
        )
        {
            /**
             * Send email to admin here ******************
             */

            if( config_item('deny_access_at') > 0 )
            {
                // Log the IP address in the denied_access database
                $data = array(
                    'ip_address'  => $ip_address,
                    'time'        => date('Y-m-d H:i:s'),
                    'reason_code' => '1'
                );

                $this->_insert_denial( $data );

                // Output white screen of death
                header('HTTP/1.1 403 Forbidden');
                die('<h1>Forbidden</h1><p>You don\'t have permission to access ANYTHING on this server.</p><hr><address>Go fly a kite!</address>');
            }
        }

        /**
         * Initialize variable to show total of failed
         * login attempts where username or email logged
         */
        $count2 = 0;

        // Check to see if this username/email-address has too many login attempts
        if( $string != '' )
        {
            $count2 = $this->db->where( 'username_or_email', $string )
                ->count_all_results( config_item('errors_table') );

            if( $count2 == config_item('max_allowed_attempts') )
            {
                // Place the username/email-address on hold
                $data = array(
                    'username_or_email' => $string,
                    'time'              => date('Y-m-d H:i:s')
                );

                $this->db->set( $data )
                    ->insert( config_item('username_or_email_hold_table') );
            }
        }

        return max( $count1, $count2 );
    }

    // --------------------------------------------------------------

    /**
     * Get all data from the denied access table,
     * or set the field parameter to retrieve a single field.
     *
     * @param bool $field
     * @return bool
     */
    public function get_deny_list( $field = FALSE )
    {
        if( $field !== FALSE )
            $this->db->select( $field );

        $query = $this->db->from( config_item('denied_access_table') )->get();

        if( $query->num_rows() > 0 )
            return $query->result();

        return FALSE;
    }

    // --------------------------------------------------------------

    /**
     * Add a record to the denied access table
     *
     * @param $data
     * @return bool
     */
    protected function _insert_denial( $data )
    {
        if( $data['ip_address'] == '0.0.0.0' )
            return FALSE;

        $this->db->set( $data )
            ->insert( config_item('denied_access_table') );

        $this->_rebuild_deny_list();
    }

    // --------------------------------------------------------------

    /**
     * Remove a record from the denied access table.
     * This method is not used by any action in Community Auth's
     * example controllers. It has been left here for convenience.
     *
     * @param $ips
     */
    protected function _remove_denial( $ips )
    {
        $i = 0;

        foreach( $ips as $ip)
        {
            if( $i == 0 ){
                $this->db->where('ip_address', $ip );
            }else{
                $this->db->or_where('ip_address', $ip );
            }

            $i++;
        }

        $this->db->delete( config_item('denied_access_table') );

        $this->_rebuild_deny_list();
    }

    // --------------------------------------------------------------

    /**
     * Rebuild the deny list in the local Apache configuration file
     *
     */
    private function _rebuild_deny_list()
    {
        // Get all of the IP addresses in the denied access database
        $query_result = $this->get_deny_list('ip_address');

        if( $query_result !== FALSE )
        {
            // Create the denial list to be inserted into the Apache config file
            $deny_list = "\n" . '<Limit GET POST>' . "\n" . 'order deny,allow';

            foreach( $query_result as $row )
            {
                $deny_list .= "\n" . 'deny from ' . $row->ip_address;
            }

            $deny_list .= "\n" . '</Limit>' . "\n";
        }
        else
        {
            $deny_list = "\n";
        }

        // Get the path to the Apache config file
        $htaccess = config_item('apache_config_file_location');

        $this->load->helper('file');

        // Store the file permissions so we can reset them after writing to the file
        $initial_file_permissions = fileperms( $htaccess );

        // Change the file permissions so we can read/write
        @chmod( $htaccess, 0644);

        // Read in the contents of the Apache config file
        $string = read_file( $htaccess );

        $pattern = '/(?<=# BEGIN DENY LIST --)(.|\n)*(?=# END DENY LIST --)/';

        // Within the string, replace the denial list with the new one
        $string = preg_replace( $pattern, $deny_list, $string );

        // Write the new file contents
        if ( ! write_file( $htaccess, $string ) )
        {
            die('Could not write to Apache configuration file');
        }

        // Change the file permissions back to what they were before the read/write
        @chmod( $htaccess, $initial_file_permissions );
    }

    // --------------------------------------------------------------

    /**
     * Remove the auth session record when somebody logs out
     *
     * @param $user_id
     * @param $session_id
     */
    public function logout( $user_id, $session_id )
    {
        $this->db->where( 'user_id' , $user_id )
            ->where( 'id', $session_id )
            ->delete( config_item('auth_sessions_table') );
    }

    // --------------------------------------------------------------

    /**
     * Garbage collection routine for old or orphaned auth sessions.
     * The auth sessions records are normally deleted if the user
     * logs out, but if they simply close the browser, the record
     * needs to be removed though garbage collection. This is subject
     * to settings you have for sessions in config/config.
     *
     */
    public function auth_sessions_gc()
    {
        // GC for database based sessions
        if( config_item('sess_driver') == 'database' )
        {
            // Immediately delete orphaned auth sessions
            $this->db->query('
				DELETE a
				FROM `' . config_item('auth_sessions_table') . '` a
				LEFT JOIN `' . config_item('sessions_table') . '` b
				ON  b.id = a.id
				WHERE b.id IS NULL
			');
        }

        // GC for sessions not expiring on browser close
        if( config_item('sess_expiration') != 0 )
        {
            $this->db->query("
				DELETE FROM " . config_item('auth_sessions_table') . "
				WHERE modified_at < current_date - interval '" . config_item('sess_expiration')/3600 . " hour'
            ");
        }
    }

    // -----------------------------------------------------------------------

}

/* End of file Auth_model.php */
/* Location: /community_auth/models/Auth_model.php */