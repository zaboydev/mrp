<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 17/04/2016
 * Time: 13:28
 */

if ( ! function_exists('_count_in_stock')) {
    /**
     * @param string $message
     * @param string $type
     * @param bool $dismissable
     */
    function _count_in_stock($item_id)
    {
        $CI =& get_instance();

        $CI->load->model('item_model', 'item');

        return $CI->item->count_item_in_stock($item_id);
    }
}
