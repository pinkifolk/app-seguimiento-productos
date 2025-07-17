<?php
require_once "../models/services.php";
require_once "../middleware/cambiar_id.php";
header('Content-Type: application/json');

$response = [];
$accion = (int)$_GET['accion'];
$id = desencriptarId($_GET['id']) ?? '';

if($accion ===1){
    $input = json_decode(file_get_contents("php://input"), true);
    $datos = $input['datos'] ?? [];
    $titulo = $input['titulo'] ?? null;

    if(is_null($titulo) || $datos === []){
        $response = [
            "status" => true,
            "mensaje" => "Uno de los datos es nulo"
        ];
    }else{
        $result = create($titulo,$datos);
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

if($accion === 2){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    $servicios = index($search_query);
    
    $response = [
        "status" => true,
        "enServicio" => $servicios,
        ];
}
if($accion === 3){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    $servicios_det = show_detail($search_query,$id);
    $response = [
        "status" => true,
        "serviciosDet" => $servicios_det,
    ];
}
if($accion === 4){
    $result = delete_detail($id);
    if ($result) {
        $response = ['status' => true, 'mensaje' => 'Detalle elimnado correctamente'];
    } else {
        $response = ['status' => false, 'mensaje' => 'Registro no encontrado'];
    }
}

echo json_encode($response); 
