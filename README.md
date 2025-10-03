# APP Seguimiento

Este proyecto es una aplicación desarrollada en PHP para gestionar y dar seguimiento a tareas, proyectos o actividades. Permite organizar información, registrar avances y facilitar la colaboración entre usuarios. Su objetivo principal es llevar un seguimiento de los productos que necesitan algún tipo de mantención antes de ser comercializados.

Este proyecto es complementario a un sistema por lo que debe tener como base un maestro de productos y un maestro de usuarios.

## Estructura del proyecto
La estructura se basa en el patron de diseño MVC y usando la convención snake case 

├── root/ 
  ├── auth 
  ├── controllers
  ├── database
  ├── layout
  ├── middleware
  ├── model
  ├── resources
  ├── views
  ├── index

## Instalación y configuración

### Requisitos
- PHP (versión recomendada: 7.4 o superior)
- Servidor web (Apache, Nginx, etc.)
- Base de datos MySQL/MariaDB

### Instalación
Clona el repositorio en tu ambiente local 
```sh
git clone https://github.com/pinkifolk/app-seguimiento-productos.git
```
Configura la conexión a la base de datos 
- root/
  - database
    - conn.php  
```php
  private $host = "localhost";
  private $user = "usuarios";
  private $pass = "clave";
  private $db = "basededatos";
```
Ejecuta la creacion de tablas necesarias desde la consola o como mas te acomode
```mysql
  mysql -u [tu_usuario] -p [tu_base] < database/table.sql
```
Levanta la aplicación con tu entorno de desarrollo preferido





