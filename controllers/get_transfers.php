<?php
// Lógica del buscador
require_once "../models/transfers.php";
require_once "../middleware/cambiar_id.php";
header('Content-Type: application/json');

$response = [];
$accion = (int)$_GET['accion'];
$id = desencriptarId($_GET['id']) ?? 0;

if($accion ===1){
    $input = json_decode(file_get_contents("php://input"), true);
    $datos = $input['datos'] ?? [];
    $fecha_llegada = $input['date'] ?? null;
    
    if(is_null($fecha_llegada) || $datos === []){
        $response = [
            "status" => true,
            "mensaje" => "Uno de los datos es nulo"
        ];
    }else{
        $ids = [];
        $duplicados = [];
    
        foreach ($datos as $row) {
            if (!is_array($row) || count($row) < 2) {
                continue; 
            }
            $id = $row[0];
            if (in_array($id, $ids)) {
                $duplicados[] = $id;
            } else {
                $ids[] = $id;
            }
        }
    if (!empty($duplicados)) {
        $response = [
            "status" => false,
            "mensaje" => "Existen IDs duplicados en el archivo",
            "duplicados" => $duplicados
        ];
    }else{
        $result = create($fecha_llegada,$datos);
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
}

if($accion === 2){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    $traspaso = index($search_query);
    
    $response = [
        "status" => true,
        "traslado" => $traspaso,
    ];
}
if($accion === 3){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    
    $traspaso_det = show_detail($search_query,$id);
    
    $response = [
        "status" => true,
        "trasladoDet" => $traspaso_det,
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
if($accion === 5){
    $result = confirm($id);
    
    if ($result) {
        $response = ['status' => true, 'mensaje' => 'Traslado confirmado correctamente'];
    } else {
        $response = ['status' => false, 'mensaje' => 'Registro no encontrado'];
    }
}

echo json_encode($response);








