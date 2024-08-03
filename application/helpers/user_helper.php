<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 16/04/2016
 * Time: 0:29
 */
if ( ! function_exists('_user_data')) {
    function _user_data($field)
    {
        $CI =& get_instance();
        $user = $CI->ion_auth->user()->row();

        return $user->$field;
    }
}
