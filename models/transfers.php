<?php
require_once "../database/conn.php";
require_once "../middleware/cambiar_id.php";


function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}

function index($search_query){
    $query ="";
    if($search_query){
        $query ="SELECT ATR.*,DATE_FORMAT(ATR.fecha_llegada, '%d-%m-%Y') formato_fecha, IFNULL(SUM(AD.cantidad), 0) AS unidades FROM app_traslados ATR LEFT JOIN app_traslados_det AD ON AD.traslado_id = ATR.id WHERE ATR.fecha_llegada LIKE? GROUP BY ATR.id ORDER BY ATR.id DESC";
    }else{
        $query ="SELECT ATR.*, DATE_FORMAT(ATR.fecha_llegada, '%d-%m-%Y') formato_fecha, IFNULL(SUM(AD.cantidad), 0) AS unidades FROM app_traslados ATR LEFT JOIN app_traslados_det AD ON AD.traslado_id = ATR.id GROUP BY ATR.id ORDER BY ATR.id DESC";
    }
    $stmt = conn()->prepare($query);
    $param = '%'. $search_query . '%';
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $traspaso = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $traspaso[] = $row;
    }
    $stmt->close();
    return $traspaso;
}

function create($fecha_llegada,$datos){
    
    $query ="INSERT INTO app_traslados (fecha_llegada) VALUES (?)";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("s", $fecha_llegada);
    $stmt->execute();
    $ultimo_id = conn()->insert_id;
    $stmt->close();
    
    $valores = [];
    $parametros = [];
    $tipos = ''; 
    
    foreach ($datos as $index => $fila) {
        if ($index === 0) continue;
        $valores[] = "(?, ?, ?)";
        $parametros[] = $ultimo_id;
        $parametros[] = $fila[0]; 
        $parametros[] = $fila[1];
    
        $tipos .= 'iii'; 
    }
    if (empty($valores)) return false;
    
    $query = "INSERT INTO app_traslados_det (traslado_id, producto_id, cantidad) VALUES " . implode(',', $valores);

    $stmt = conn()->prepare($query);
    if (!$stmt) return false;
    $stmt->bind_param($tipos, ...$parametros);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
function show_detail ($search_query, $id){
    $query ="SELECT ATD.id, P.id producto_id, P.cod_unificado, M.descripcion marca, C.descripcion categoria, ATD.cantidad, ATR.estado FROM app_traslados_det ATD LEFT JOIN app_traslados ATR ON ATR.id=ATD.traslado_id LEFT JOIN productos P ON P.id=ATD.producto_id LEFT JOIN categorias C ON C.id=P.categoria_id LEFT JOIN marcas M ON M.id=P.marca_id WHERE ATR.id=?";
    
    $params = [$id];
    $types = "s";
    
    if ($search_query !== null && $search_query !== '') {
        $query .= " AND ATD.producto_id LIKE ?";
        $types .= "s";
        $params[] = "$search_query%";
    }
    
    $query .= " ORDER BY ATD.id";
    
    $stmt = conn()->prepare($query);
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $traspaso_det = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $traspaso_det[] = $row;
    }
    $stmt->close();
    return $traspaso_det;
    
}
function delete_detail($id){
    $query ="DELETE FROM app_traslados_det WHERE id=?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    return $success;
}
function confirm ($id){
    $query ="UPDATE app_traslados SET estado=1 WHERE id=?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    return $success;
}