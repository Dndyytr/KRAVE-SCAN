<?php

// Fix Symfony request base URL detection when executing from /api/index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Forward Vercel requests to normal Laravel public/index.php
require __DIR__.'/../public/index.php';
