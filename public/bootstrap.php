<?php

define('RUNNING_IN_CONSOLE', 0);
putenv('DATABASE_HOST=mysql');
putenv('DATABASE_PORT=33067');

require __DIR__ . '/../database/database.php';

require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/api.php';

