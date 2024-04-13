<?php


header("Content-Type: application/json");

// return a fixed date
header("Date: Sat, 13 Apr 2024 14:22:40 GMT");

echo json_encode($_REQUEST);