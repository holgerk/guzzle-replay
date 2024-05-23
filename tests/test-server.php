<?php


header("Content-Type: application/json");

// return fixed/static values
header("X-Powered-By: PHP");

echo json_encode($_REQUEST, JSON_PRETTY_PRINT);