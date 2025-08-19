<?php
// Lógica del buscador
require_once "../models/receptions.php";
require_once "../middleware/cambiar_id.php";

header('Content-Type: application/json');

$response = [];
$accion = (int)$_GET['accion'];
$id = desencriptarId($_GET['id']) ?? '';


if($accion === 2){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    $reparacion = index($search_query);
    
    $response = [
        "status" => true,
        "reparacion" => $reparacion,
    ];
}
if($accion === 3){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    $recepcion_det = show_detail($search_query,$id);
    $response = [
        "status" => true,
        "recepcionDet" => $recepcion_det,
    ];
}

if($accion === 5){
    $input = json_decode(file_get_contents("php://input"), true);
    $datos = $input['datos'] ?? [];
    $id = desencriptarId($input['id']) ?? null;
    
    function validarData($datos){
        foreach($datos as $item){
            $servicio = $item['servicios'];
        
            $pintura = in_array('pintura', $servicio) ? 1 : 0;
            $reparacion = in_array('reparacion', $servicio) ? 1 : 0;
            $certificacion = in_array('certificacion', $servicio) ? 1 : 0;
            $ninguno = in_array('ninguno', $servicio) ? 1 : 0;
            
            if($pintura === 0 && $reparacion === 0 && $certificacion === 0 && $ninguno === 0){
                return false;
            }
        }
        return true;
    }
    
    if(empty($id) || $id === "0" || $datos === []){
        $response = [
            "status" => true,
            "mensaje" => "Error, uno de los datos no esta siendo enviado"
        ];

    }else{
        $validar = validarData($datos);
        if (!$validar) {
            $response = [
                "status" => false,
                "mensaje" => "Los productos deben tener al menos un servicio"
            ];
            return;
        }
        
        $result = receptions($id, $datos);
        
        $response = [
            "status" => $result['status'],
            "mensaje" => $result['status']
                ? "Registros actualizados: " . $result['contador']
                : "No se pudieron actualizar los registros"
        ];
    }
   

}
if($accion === 6){
    $search_query = null;
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
    }
    $recepcion_det = show_detail_receptions($search_query,$id);
    $response = [
        "status" => true,
        "recepcionDet" => $recepcion_det,
    ];
}
if($accion === 7){
    $input = json_decode(file_get_contents("php://input"), true);
    $datos = $input['datos'] ?? [];
    $id = desencriptarId($input['id']) ?? 0;
  
    
    $ids = implode(',', array_fill(0, count($datos), '?'));
    $result = status_detail_receptions($ids,$datos);
    changeat_status($id);
    
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
echo json_encode($response);