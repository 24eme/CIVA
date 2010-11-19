<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.

$autorisee_ip = array();
$autorisee_ip[] = '127.0.0.1';
$autorisee_ip[] = '::1';
for($i = 1; $i <= 255; $i++) {
    $autorisee_ip[] = 'CHA.NG.ME.'.$i;
}
if (!in_array(@$_SERVER['REMOTE_ADDR'], $autorisee_ip))
{
  die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('civawebservice', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
