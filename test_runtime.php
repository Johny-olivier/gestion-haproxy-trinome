<?php

require_once 'app/Services/HAProxyRuntime.php';

$runtime = new HAProxyRuntime();

echo "=== ETAT GENERAL ===\n";
print_r($runtime->getDashboard());

echo "\n=== SERVEURS ===\n";
print_r($runtime->getServers());
