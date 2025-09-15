<?php
require_once "../database/conn.php";
require_once "../middleware/cambiar_id.php";


function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}
function index($search_query){
    $param = '';
    
    $query ="SELECT ASP.*,DATE_FORMAT(ASP.fecha_creacion, '%d-%m-%Y %H:%i') formato_fecha, DATE_FORMAT(ASP.fecha_termino, '%d-%m-%Y %H:%i') formato_termino, DATE_FORMAT(ASP.fecha_recepcion, '%d-%m-%Y %H:%i') formato_recepcion, IFNULL(SUM(ASD.cantidad), 0) AS unidades, COUNT(ASD.id) items FROM app_servicios_prod ASP LEFT JOIN app_servicios_prod_det ASD ON ASD.servicios_prod_id = ASP.id";
    
    if (isset($_GET['search_query']) && !empty($_GET['search_query'])) {
        $search_query = $_GET['search_query'];
        $param = '%' . $search_query . '%';
        $query .=" WHERE ASP.titulo LIKE ? OR ASP.fecha_creacion LIKE ?";
    }
    
    $query .= " GROUP BY ASP.id ORDER BY ASP.id DESC";
    
    $stmt = conn()->prepare($query);
    
    if ($search_query !== null) {
        $stmt->bind_param("ss", $param, $param);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reparacion = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $reparacion[] = $row;
    }
    $stmt->close();
    return $reparacion;
}
function show_detail ($search_query, $id){
    $query ="SELECT ASD.id, P.id producto_id, P.cod_unificado, M.descripcion marca, C.descripcion categoria, ASD.cantidad, ASP.estado FROM app_servicios_prod_det ASD LEFT JOIN app_servicios_prod ASP ON ASP.id=ASD.servicios_prod_id LEFT JOIN productos P ON P.id=ASD.producto_id LEFT JOIN categorias C ON C.id=P.categoria_id LEFT JOIN marcas M ON M.id=P.marca_id WHERE ASP.id=?";
    
    $params = [$id];
    $types = "s";
    
    if ($search_query !== null && $search_query !== '') {
        $query .= " AND ASD.producto_id LIKE ?";
        $types .= "s";
        $params[] = "$search_query%";
    }
    
    $query .= " ORDER BY ASD.id";
    
    $stmt = conn()->prepare($query);
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recepcion_det = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $recepcion_det[] = $row;
    }
    $stmt->close();
    return $recepcion_det;
}
function receptions ($id, $datos){
    $query ="UPDATE app_servicios_prod SET estado=1, fecha_recepcion=NOW() WHERE id = ?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    
    $contador++;
    foreach ($datos as $item){
        $id_registro = desencriptarId($item['id']);
        $servicio = $item['servicios'];
        
        $pintura = in_array('pintura', $servicio) ? 1 : 0;
        $reparacion = in_array('reparacion', $servicio) ? 1 : 0;
        $certificacion = in_array('certificacion', $servicio) ? 1 : 0;
        $ninguno = in_array('ninguno', $servicio) ? 1 : 0;
        
        if($ninguno === 1){
            $query = "UPDATE app_servicios_prod_det SET limpieza=?, pintura=?, banco_pruebas=?, ninguno=?, fecha_inicio=NOW(), fecha_termino=NOW(), estado=2 WHERE id=?";
        }else{
            $query = "UPDATE app_servicios_prod_det SET limpieza=?, pintura=?, banco_pruebas=?, ninguno=? WHERE id=?";
        }
        $stmt = conn()->prepare($query);
        
        $stmt->bind_param("iiiii",$pintura,$reparacion,$certificacion,$ninguno,$id_registro);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            $contador++;
        }
        $stmt->close();
        
    }
    return [
        "status" => $success,
        "contador" => $contador
              
        ];
}
function show_detail_receptions($search_query,$id) {
    $query ="SELECT ASD.id, P.id producto_id, P.cod_unificado, ASD.cantidad, ASD.limpieza, ASD.pintura, ASD.banco_pruebas, ASD.estado,DATE_FORMAT(ASD.fecha_termino, '%d-%m-%Y %H:%i') formato_termino,DATE_FORMAT(ASD.fecha_inicio, '%d-%m-%Y %H:%i') formato_inicio FROM app_servicios_prod_det ASD LEFT JOIN app_servicios_prod ASP ON ASP.id=ASD.servicios_prod_id LEFT JOIN productos P ON P.id=ASD.producto_id  WHERE ASP.id=?";
    
    $params = [$id];
    $types = "s";
    
    if ($search_query !== null && $search_query !== '') {
        $query .= " AND ASD.producto_id LIKE ?";
        $types .= "s";
        $params[] = "$search_query%";
    }
    
    $query .= " ORDER BY ASD.id";
    
    $stmt = conn()->prepare($query);
    
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    
    $recepcion_det = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $recepcion_det[] = $row;
    }
    return $recepcion_det;
}
function status_detail_receptions($ids,$datos){
    
    $query ="UPDATE app_servicios_prod_det SET estado=1, fecha_termino=NOW() WHERE id IN ($ids) AND estado = 0";
    $stmt = conn()->prepare($query);
    $types = str_repeat('i', count($datos));
    $params = [];
    $params[] = &$types;
    foreach ($datos as $key => $value) {
        $params[] = &$datos[$key];
    }

    call_user_func_array([$stmt, 'bind_param'], $params);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    
    return $success;
}
function changeat_status($id) {
    $query ="SELECT COUNT(*) AS total, SUM(CASE WHEN estado = 2 THEN 1 ELSE 0 END) AS activos FROM app_servicios_prod_det WHERE servicios_prod_id=?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    $total = (int)$data['total'];
    $activos = (int)$data['activos'];
    
    if($total === $activos){
        $query ="UPDATE app_servicios_prod SET estado=2, fecha_termino=NOW() WHERE id=?";
        $stmt = conn()->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}
function init_process($id){
    $query ="UPDATE app_servicios_prod_det SET estado=1, fecha_inicio=NOW() WHERE id=?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    return $success;
}
function end_process($id){
    $query ="UPDATE app_servicios_prod_det SET estado=2, fecha_termino=NOW() WHERE id=?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    return $success;
}