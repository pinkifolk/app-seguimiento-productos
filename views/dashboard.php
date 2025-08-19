<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: index.php");
    exit();
}
$titulo = 'App seguimiento | Dashboard';
ob_start();
$contenido = ob_get_clean();
include '../layout/layout.php';
?>
<style>
    .contenedor-estados{
        padding: 4px;
        position: relative;
        display: flex;
        justify-content: space-between;
    }
    .linea{
        width: 90%;
        height: 25px;
        background: #cccccc;
        position: absolute;
        z-index: -2;
        top:40%;
        left: 6%;
        
    }
    .estado{
        width: 150px;
        height: 150px;
        border-radius: 100%;
        background: white;
        display: flex;              
        justify-content: center;   
        align-items: center;      
        text-align: center;
    }
    .estado span{
        font-weight: bold;
    }
    .listo{
        border:15px solid #198754;
    }
    .no-listo{
        border:15px solid #dc3545;
    }
    .indicador{
        display: flex;
        flex-direction: row;
        gap:10px;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .indicador-estado{
        width: 20px;
        height: 20px;
        border-radius: 100%;
    }
    .fila {
      display: flex;
      align-items: center;
      gap: 8px; 
    }
    .indicador-listo{
        border:5px solid #198754;
    }
    .indicador-no-listo{
        border:5px solid #dc3545;
    }
</style>
<div class="container">
    <!--SERVICIOS POR COLORES-->
    <!--<div class="container text-center my-4">
      <div class="row gap-3">
        <div class="col card border bg-success py-4 text-white">
            Servicio de Limpieza
            <span>Operativo</span>
        </div>
        
        <div class="col card border bg-success py-4 text-white">
            Servicio de Pintura
            <span>Operativo</span>
        </div>
        <div class="col card border bg-success py-4 text-white">
              Servicio de Fotografia
              <span>Operativo</span>
        </div>
        <div class="col card border bg-danger py-4 text-white">
              Banco de pruebas
              <span>No habilitado</span>
        </div>
      </div>
    </div>-->
    <div class="container text-center my-4">
        <div class="indicador">
            <div class="fila">
                <div class="indicador-estado indicador-listo"></div>
                <span>Terminado</span>
            </div>
            <div class="fila">
                <div class="indicador-estado indicador-no-listo"></div>
                <span>No Terminado</span>
            </div>
        </div>
        <div class="contenedor-estados">
            <div class="linea"></div>
            <div class="estado listo">
                <span class="textos">Servicio de Limpieza</span>
            </div>
            <div class="estado listo">
                <span>Servicio de Pintura</span>
            </div>
            <div class="estado listo">
                <span>Servicio de Fotografia</span>
            </div>
            <div class="estado no-listo">
                <span>Banco de pruebas</span>
            </div>
        </div> 
    </div>
    <div class="container text-center my-4"> 
        
      <h5 class="h1">ETAPA 1</h5>
        <!--productos en preparacion que no pasan por banco de pruebas 
        1 total de producto para comercialización
        2 total de producto listo para comercialización
        -->
        <div class="row gap-4">
        <div class="col border px-5 py-4 card">
          Total para comercialización
          <h2>357(sku)</h2>
          <p>7090(unidades)</p>
        </div>
        <div class="col border px-5 py-4 card">
          Total listo para comercialización
         <h2>422 (sku)</h2>
         <p>2498 (unidades)</p>
        </div>
      </div>
    </div>
    <!--TOTAL CODIFICADOS-->
    <!--<div class="container">
      <div class="row gap-4">
        <div class="col border px-5 py-4 card">
          Codificados (Encontrados)
          <h2>357(sku)</h2>
          <p>7090(unidades)</p>
        </div>
        <div class="col border px-5 py-4 card">
          No codificados
         <h2>422 (sku)</h2>
         <p>2498 (unidades)</p>
        </div>
      </div>
    </div>-->
    
    <!--GRAFICOS -->
    <div class="container">
        <div class="row gap-1 my-3">
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Trasladados a Quilicura</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="trasladados"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Productos en Preparación</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="listos"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5 >Listo para comercialización</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="servicios"></canvas>
                  </div>
                </div>
            </div>
        </div>
    </div>
     
</div>
 <script>
    new Chart(document.getElementById('trasladados'), {
      type: 'pie',
      data: {
        labels: ['Quilicura'],
        datasets: [{
          data: [ 100],
          backgroundColor: ['#0d6efd']
        }]
      }
    });
    new Chart(document.getElementById('listos'), {
      type: 'pie',
      data: {
        labels: ['Limpieza', 'Pintura', 'Fotografia','Banco de prueba'],
        datasets: [{
          data: [ 300, 150, 100,200],
          backgroundColor: ['#0d6efd', '#198754', '#ffc107','#cb1111']
        }]
      }
    });
    new Chart(document.getElementById('servicios'), {
      type: 'pie',
      data: {
        labels: ['Listos para vender', 'En Preparación'],
        datasets: [{
          data: [300, 150],
          backgroundColor: ['#198754', '#0d6efd']
        }]
      }
    });
  </script>
