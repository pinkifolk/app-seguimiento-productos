<?php
require_once "../models/products.php";
require_once "../middleware/cambiar_id.php";


header('Content-Type: application/json');

$response = [];

$accion = (int)$_GET['accion'];
$producto_id = desencriptarId($_GET['id']) ?? null;
$descripcion = $_GET['descripcion'] ?? '';
$descuento = $_GET['descuento'] ?? '';
$precio = $_GET['precio'] ?? '';
$ficha = $_GET['ficha'] ?? false;
$imagen = $_GET['imagen'] ?? 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;

if($accion ===2){
    $search_query = "";
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    
    $products = index($search_query,$page,$limit);
    
    $response = [
        "status" => true,
        "products" => $products
        ];
}

if($accion === 3){
    if(empty($producto_id) ||  $producto_id === "0"){
        $response = [
                "status" => false,
                "mensaje" => "Id enviado no valido"
            ];
    }else{
        $result = multimedia($producto_id,$imagen);
    
        if($result){
            $response = [
                "status" => true,
                "mensaje" => "Registro actualizado correctamente"
                ];
        }else{
            $response = [
                "status" => false,
                "mensaje" => "No se pudo modificar el registro"
            ];
        }
    }
    
}

if($accion === 4){
    if(empty($producto_id) || empty($descuento) || empty($ficha) || empty($descripcion) || empty($precio)){
        $response = [
                "status" => false,
                "mensaje" => "Uno de los datos es null"
            ];
    }else{
        $result = documentation($producto_id,$descuento,$ficha,$descripcion,$precio);
            if($result){
                $response = [
                    "status" => true,
                     "mensaje" => "Registro actualizado correctamente",
                     "ss" => $result
                    ];
            }else{
                $response = [
                    "status" => false,
                     "mensaje" => "No se pudo modificar el registro",
                     "ss" => $result
                    ];
            }
    }
    
    
}

echo json_encode($response);
