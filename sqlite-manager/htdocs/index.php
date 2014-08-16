<?php

pm_Context::init( 'sqlite-manager' );

$application = new pm_Application();
$application->run();
