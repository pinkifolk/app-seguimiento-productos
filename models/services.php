<?php
require_once "../database/conn.php";
require_once "../middleware/cambiar_id.php";


function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}
function index($search_query){
    $param = '';
    $query ="SELECT ASP.*,DATE_FORMAT(ASP.fecha_creacion, '%d-%m-%Y %H:%i') formato_fecha, DATE_FORMAT(ASP.fecha_termino, '%d-%m-%Y %H:%i') formato_termino, DATE_FORMAT(ASP.fecha_recepcion, '%d-%m-%Y %H:%i') formato_recepcion, IFNULL(SUM(ASD.cantidad), 0) AS unidades, ROUND(SUM(CASE WHEN ASD.estado = 2 THEN 1 ELSE 0 END) * 100 / COUNT(ASD.id), 2) avences FROM app_servicios_prod ASP LEFT JOIN app_servicios_prod_det ASD ON ASD.servicios_prod_id = ASP.id";
    
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
    $servicios = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $servicios[] = $row;
    }
    $stmt->close();
    return $servicios;
}
function create($titulo,$datos){
    $query ="INSERT INTO app_servicios_prod (titulo, fecha_creacion) VALUES (?,NOW())";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("s", $titulo);
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
    $query = "INSERT INTO app_servicios_prod_det (servicios_prod_id, producto_id, cantidad) VALUES " . implode(',', $valores);

    $stmt = conn()->prepare($query);
    if (!$stmt) return false;
    $stmt->bind_param($tipos, ...$parametros);
    $success = $stmt->execute();
    $stmt->close();

    return $success;
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
    
    $servicios_det = [];
    while ($row = $result->fetch_assoc()) {
        $row['id'] = encriptarId($row['id']);
        $servicios_det[] = $row;
    }
    $stmt->close();
    return $servicios_det;
}
function delete_detail($id){
    $query ="DELETE FROM app_servicios_prod_det WHERE id=?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0 ? true : false;
    $stmt->close();
    return $success;
}
