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
<div class="container">
    <!--SERVICIOS POR COLORES-->
    <div class="container text-center my-4">
      <div class="row gap-3">
        <div class="col card border bg-danger py-4">
          52
        </div>
        <div class="col card border bg-warning py-4">
          10
        </div>
        <div class="col card border bg-success py-4">
          100
        </div>
      </div>
      <h5>Etapa 1</h5>
    </div>
    <!--TOTAL CODIFICADOS-->
    <div class="container">
      <div class="row gap-4">
        <div class="col border px-5 py-4 card">
          Codificados (Encontrados)
          <h2>400</h2>
        </div>
        <div class="col border px-5 py-4 card">
          No codificados
         <h2>630</h2>
        </div>
      </div>
    </div>
    
    <!--GRAFICOS -->
    <div class="container">
        <div class="row gap-1 my-3">
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Trasladados etapa 1</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="trasladados"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Productos en Servicios</h5>
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
        labels: ['Quilicura', 'Coronel del canto'],
        datasets: [{
          data: [300, 150],
          backgroundColor: ['#0d6efd', '#198754']
        }]
      }
    });
    new Chart(document.getElementById('listos'), {
      type: 'pie',
      data: {
        labels: ['Producto A', 'Producto B', 'Producto C'],
        datasets: [{
          data: [300, 150, 100],
          backgroundColor: ['#0d6efd', '#198754', '#ffc107']
        }]
      }
    });
    new Chart(document.getElementById('servicios'), {
      type: 'pie',
      data: {
        labels: ['Producto A', 'Producto B', 'Producto C'],
        datasets: [{
          data: [300, 150, 100],
          backgroundColor: ['#0d6efd', '#198754', '#ffc107']
        }]
      }
    });
  </script>
