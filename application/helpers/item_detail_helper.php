<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 17/04/2016
 * Time: 13:28
 */

if ( ! function_exists('vendor_info')) {
    /**
     * @param string $message
     * @param string $type
     * @param bool $dismissable
     */
    function vendor_info($vendor_id)
    {
        $CI =& get_instance();

        $CI->load->model('item_detail_model', 'item');

        $vendor = $CI->item->findVendorById($vendor_id, 'vendor_name');

        return $vendor->vendor_name;
    }
}
