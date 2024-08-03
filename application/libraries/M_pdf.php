<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class M_pdf {
    function m_pdf()
    {
        log_message('Debug', 'mPDF class is loaded.');
    }

    function load($m="en-GB-x", $f="A4", $fs=0, $ff="", $ml=12, $mr=12, $mt=50, $mb=12, $mh=9, $mf=6, $o='P')
    {
        include_once APPPATH.'/third_party/mpdf/mpdf.php';

        return new mPDF($m, $f, $fs, $ff, $ml, $mr, $mt, $mb, $mh, $mf, $o);
    }
}
