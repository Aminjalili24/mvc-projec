<?php

require_once('../config/app.php');
require_once('../config/database.php');


require_once('../routes/api.php');
require_once('../routes/web.php');

$routing = new \System\Router\Routing();

$routing->run();
