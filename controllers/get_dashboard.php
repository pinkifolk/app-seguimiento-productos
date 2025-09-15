<?php
require_once "../models/dashboard.php";

header('Content-Type: application/json');

$response = [];
$accion = (int)$_GET['accion'];

if($accion ===2){

    $info = index();
    
    $response = [
        "status" => true,
        
        "outlet" => $info["outlet"],
        "fotos" => $info["fotos"],
        "preparacion" => $info["preparacion"],
        "preparados" => $info["preparados"],
        "documentacion" =>$info["documentacion"],
        "venta" =>$info["venta"]
    ];
}

echo json_encode($response); 