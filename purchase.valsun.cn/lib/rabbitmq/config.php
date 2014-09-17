<?php
require_once __DIR__.'/vendor/autoload.php';

define('HOST', '112.124.41.121');
define('PORT', 5672);
define('USER', 'purchase');
define('PASS', 'purchase123');
define('VHOST', 'valsun_purchase');

//If this is enabled you can see AMQP output on the CLI
define('AMQP_DEBUG', false);
