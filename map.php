<?php

$page_settings = array(
  'title' => '',
  'type' => 'map',
  'css' => array(
    'duru',
    'styles',
    'map'
    ),
  'js' => array(
    'jquery',
    'notification',
    'map/map.standard'
    )
);

require_once(getcwd().'/includes/init.php');

if($user_settings['per'] == 2){
  array_push($page_settings['js'],'map/map.admin');
}

require_once(incRoot().'layout/page.class.php');

$page = new Page($page_settings);

require_once(incRoot().'map/map.class.php');

$map = new MappMe($user_settings,$mm_db);

$page->finHead();

$map->buildMap(); ?>
    