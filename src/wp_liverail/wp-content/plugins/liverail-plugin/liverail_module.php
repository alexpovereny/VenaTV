<?php

define("MVVLIVERAIL", "mvv_liverail_");

/* http://test.api.liverail.com/ 
  user: alex.povereny@gmail.com
  pass: alexvena2014 */


//delete_option('mvv_liverail_options'); 

//Значения по умолчанию 
$mvv_liverail_options = array();
$mvv_liverail_options['mvv_liverail_url'] = "http://api4.int.liverail.com";
//$mvv_liverail_options['mvv_liverail_test_url'] = "http://test.api.liverail.com";
$mvv_liverail_options['mvv_liverail_user'] = "alex.povereny@gmail.com";
$mvv_liverail_options['mvv_liverail_pass'] = "alexvena2014";

add_option('mvv_liverail_options', $mvv_liverail_options, '', 'no');
add_option('mvv_liverail_token', '', '', 'no');

//add_option('mvv_entity_id', $mvv_liverail_entity_id, '', 'no');
//add_option(MVVLIVERAIL . "token",'');
//echo get_option('mvv_liverail_options'); 
//$mvv_liverail_options_ = get_option('mvv_liverail_options');
//var_dump($mvv_liverail_options_['mvv_liverail_url']);


require_once dirname(__FILE__) . '/mvvliverail/mvvliverail-plugin.php';

