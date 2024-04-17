<?php


header("Content-Type: application/json");

// return fixed/static values
header("Date: Sat, 13 Apr 2024 14:22:40 GMT");
header("X-Powered-By: PHP");

echo json_encode($_REQUEST, JSON_PRETTY_PRINT);