<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-client-key, x-client-token, x-client-secret, Authorization");
header('Content-Type: application/json');

echo '{"products":[{"nid":1,"title":"text1"},{"nid":2,"title":"other prod"}]}';
