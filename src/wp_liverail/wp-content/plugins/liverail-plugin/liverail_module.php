<?php

/*
 * Plugin Name: LiveRail API Plugin
  Description:  Test
  Embed a JW Player 6 for HTML5 into your WordPress articles. Integrates your Limelight VPS video content into Wordpress.Version: 1.0
  Author: Valeriya Melnik.
  Author URI: http://localhost/wp_liverail_git/src/wp_liverail

 * 
 */
/*
  By default the JW Player plugin uses custom filters to replace the shortcodes. This
  allows for granular control of how the shortcode is replaced in different page types.
  E.g.: Wordpress normally strips out shortcodes from excerpts, which means that you
  cannot show players in pages where a theme uses the "the_excerpt" function. However
  if you are making video blog and also want to show players in e.g. your search results
  or category pages, you can still do so because of our custom players.
  If you run into problems with other filters you can change this setting and see if it
  solves your problems ;)
 */
define("JWPLIMELIGHT_USE_CUSTOM_SHORTCODE_FILTER", true);

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

