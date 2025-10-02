CREATE TABLE app_productos_multimedia (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT(10) UNSIGNED UNIQUE,
    imagen BOOLEAN,
    ficha BOOLEAN,
	descripcion BOOLEAN,
	especificacion VARCHAR(255) NULL DEFAULT NULL,
	precio INT NOT NULL,
	descuento INT NOT NULL,
	prod_equivalente INT NOT NULL,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
) ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE app_traslados (
	id INT PRIMARY KEY AUTO_INCREMENT,
    fecha_llegada DATE NULL,
    estado TINYINT UNSIGNED NOT NULL DEFAULT 0
)ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE app_traslados_det (
	id INT PRIMARY KEY AUTO_INCREMENT,
    traslado_id INT UNSIGNED,
    producto_id INT NOT NULL,
    cantidad INT,
    FOREIGN KEY (traslado_id) REFERENCES app_traslados(id)
)ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE app_servicios_prod (
	id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(50),
    fecha_creacion DATE NULL,
	fecha_recepcion DATE NULL,
    fecha_termino DATE NULL,
    estado TINYINT UNSIGNED NOT NULL DEFAULT 0
)ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE app_servicios_prod_det (
	id INT PRIMARY KEY AUTO_INCREMENT,
    servicios_prod_id INT UNSIGNED,
    producto_id INT NOT NULL,
    cantidad INT,
    limpieza BOOLEAN DEFAULT 0,
    pintura BOOLEAN DEFAULT 0,
    banco_pruebas BOOLEAN DEFAULT 0,
	fecha_inicio DATETIME DEFAULT NULL,
	fecha_termino DATATIME DEFAULT NULL,	
	ninguno BOOLEAN DEFAULT 0,
	usuario_id INT NOT NULL,
	encargado VARCHAR(3) NULL,
    estado BOOLEAN DEFAULT 0,
    FOREIGN KEY (servicios_prod_id) REFERENCES app_servicios_prod(id)
)ENGINE=MyISAM CHARSET=utf8 COLLATE=utf8_spanish_ci;
