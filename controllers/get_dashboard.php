<?php
require_once "../models/dashboard.php";

header('Content-Type: application/json');

$response = [];
$accion = (int)$_GET['accion'];

if($accion ===2){

    $info = index();
    
    $response = [
        "status" => true,
        "traslado" => $info["traslado"],
        "preparacion" => $info["preparacion"],
        "listos" => $info["listos"]
    ];
}

echo json_encode($response); 