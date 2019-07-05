<?php
/**
 * Created by PhpStorm.
 * User: imann
 * Date: 21/04/2016
 * Time: 21:06
 */
$config['item_detail_location'] = array(
        1 = 'shelf', // in stock (received)
        2 = 'aircraft', // in used (issued)
        3 = 'other', // out (issued)
    );

$config['item_detail_histories_status'] = array(
        1 = 'created',
        2 = 'modified',
        3 = 'imported from csv',
        4 = 'deleted',
        5 = 'received',
        6 = 'issued'
    );

$config['doc_receipt_status'] = array(
        1 = 'The above stores are in accordance with the terms of order as regard and are fit to use',
        2 = 'The above stores have been received damage or shortage report',
        3 = 'The stock record has been posted',
    );
