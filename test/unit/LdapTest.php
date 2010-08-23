<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
{}
$t = new lime_test(1);

$ldap = new ldap();
$connection = $ldap->ldapConnect();

$t->ok($connection, 'Connect to LDAP');

?>