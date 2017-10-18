<?php

/**
 * Database Configuration
 *
 * All of your system's database configuration settings go in here.
 * You can see a list of the default settings in craft/app/etc/config/defaults/db.php
 */

return array(

    'server' => $_ENV['DATABASE']['HOST'],
    'database' => $_ENV['DATABASE']['NAME'],
    'user' => $_ENV['DATABASE']['USER'],
    'password' => $_ENV['DATABASE']['PASSWORD'],
    'tablePrefix' => 'craft'

);
