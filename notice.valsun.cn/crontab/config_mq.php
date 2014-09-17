<?php
require_once __DIR__.'/../lib/rabbitmq/autoload.php';
define('HOST', '115.29.188.246');
define('PORT', 5672);
define('USER', 'valsun_power');
define('PASS', 'power%123');
define('VHOST', 'power');

//If this is enabled you can see AMQP output on the CLI
define('AMQP_DEBUG', false);
?>