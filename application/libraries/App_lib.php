<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 15/04/2016
 * Time: 23:28
 */
class App_lib
{
    protected $CI;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct()
    {
        // Assign the CodeIgniter super-object
        $this->CI =& get_instance();
    }

    function _render_page($view, $data=null, $returnhtml=false)
    {
        $this->viewdata = (empty($data)) ? $this->data: $data;

        $view_html = $this->CI->load->view($view, $this->viewdata, $returnhtml);

        if ($returnhtml) return $view_html;
    }
}
