<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Community Auth - Database Tables Config
 *
 * Community Auth is an open source authentication application for CodeIgniter 3
 *
 * @package     Community Auth
 * @author      Robert B Gottier
 * @copyright   Copyright (c) 2011 - 2016, Robert B Gottier. (http://brianswebdesign.com/)
 * @license     BSD - http://www.opensource.org/licenses/BSD-3-Clause
 * @link        http://community-auth.com
 */

// USER RELATED TABLES
$config['user_table']                   = 'tb_auth_users';

// LOGIN ERROR RELATED TABLES
$config['errors_table']                 = 'tb_auth_login_errors';
$config['IP_hold_table']                = 'tb_auth_ips_on_hold';
$config['username_or_email_hold_table'] = 'tb_auth_username_or_email_on_hold';
$config['denied_access_table']          = 'tb_auth_denied_access';

// SESSION TABLES
$config['sessions_table']               = 'tb_auth_ci_sessions';
$config['auth_sessions_table']          = 'tb_auth_sessions';

/* End of file db_tables.php */
/* Location: /community_auth/config/db_tables.php */