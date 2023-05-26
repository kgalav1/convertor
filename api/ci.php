<?php
// file: CI.php
ob_start();
require_once 'index.php'; // adjust path accordingly
ob_get_clean();
return $CI;