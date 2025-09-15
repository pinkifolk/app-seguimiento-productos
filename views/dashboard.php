<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
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
<!--
nuevos dash 
fotografia documentacion y servicios en preparacion y servicios listos sku y unidades para servicios solamente.


-->
<div class="container">
    <div class="container text-center my-4">
        <div class="indicador">
            <div class="fila">
                <div class="indicador-estado indicador-listo"></div>
                <span>Operativo</span>
            </div>
            <div class="fila">
                <div class="indicador-estado indicador-no-listo"></div>
                <span>No Operativo</span>
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
          Total Productos Outlet
          <h2 id="totalOutlet"></h2>
          <p id="unidadesOutlet"></p>
        </div>
        <div class="col border px-5 py-4 card">
          Listo para comercialización
         <h2 id="totalVentas"></h2>
         <p id="unidadesVentas"></p>
        </div>
      </div>
    </div>
    
    <!--GRAFICOS -->
    <div class="container">
        <div class="row gap-2 my-3">
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Fotografia</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="fotos"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Documentación</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="documentacion"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5 >Preparados</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="servicios"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>En Preparación</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="listos"></canvas>
                  </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                  <div class="card-header bg-white">
                    <h5>Listos para comercialización</h5>
                  </div>
                  <div class="card-body">
                    <canvas id="venta"></canvas>
                  </div>
                </div>
            </div>
        </div>
    </div>
     
</div>
 <script>
    const totalOutlet = document.getElementById('totalOutlet')
    const unidadesOutlet = document.getElementById('unidadesOutlet')
    const totalVentas = document.getElementById('totalVentas')
    const unidadesVentas = document.getElementById('unidadesVentas')
    
    async function loadInfo() {
        try {
            const response = await fetch(`../controllers/get_dashboard.php?accion=2`)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const {outlet,fotos,preparacion,preparados,documentacion,venta} = await response.json()
            new Chart(document.getElementById('fotos'), {
              type: 'bar',
              data: {
                labels: ['Listos','Pendientes'],
                datasets: [{
                  data: fotos,
                  backgroundColor: ['#198754','#cb1111']
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: false,
                    position: 'top'
                  }
                },
                scales: {
                  y: { beginAtZero: true }
                }
              }
            });
            new Chart(document.getElementById('documentacion'), {
              type: 'bar',
              data: {
                labels: ['Listos','Pendientes'],
                datasets: [{
                  data: documentacion,
                  backgroundColor: ['#198754','#cb1111']
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: false,
                    position: 'top'
                  }
                },
                scales: {
                  y: { beginAtZero: true }
                }
              }
            });
            new Chart(document.getElementById('listos'), {
              type: 'bar',
              data: {
                labels: ['Limpieza','Pintura','Banco Pruebas'],
                datasets: [{
                    label: ['Productos'],
                    data: [preparacion[0][0],preparacion[0][1],preparacion[0][2]],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Unidades',
                    data: [preparacion[1][0],preparacion[1][1],preparacion[1][2]],  
                    backgroundColor: '#0d6efd'
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: false,
                    position: 'top'
                  }
                },
                scales: {
                  y: { beginAtZero: true }
                }
              }
            });
            new Chart(document.getElementById('servicios'), {
              type: 'bar',
              data: {
                labels: ['Preparados'],
                datasets: [{
                    label: ['Productos'],
                    data: [preparados[0][0]],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Unidades',
                    data: [preparados[0][1]],  
                    backgroundColor: '#0d6efd'
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: false,
                    position: 'top'
                  }
                },
                scales: {
                  y: { beginAtZero: true }
                }
              }
            });
            new Chart(document.getElementById('venta'), {
              type: 'bar',
              data: {
                labels: ['Listos para vender'],
                datasets: [{
                    label: ['Productos'],
                    data: [venta[0][0]],
                    backgroundColor: '#198754'
                },
                {
                    label: 'Unidades',
                    data: [venta[1][0]],  
                    backgroundColor: '#0d6efd'
                }]
              },
              options: {
                responsive: true,
                plugins: {
                  legend: {
                    display: false,
                    position: 'top'
                  }
                },
                scales: {
                  y: { beginAtZero: true }
                }
              }
            });
    
            
    
            totalOutlet.textContent = `${outlet[0]} (sku)`
            unidadesOutlet.textContent = `${Number(outlet[1]).toLocaleString('es-CL')} (unidades)`
            totalVentas.textContent = `${venta[0]} (sku)`
            unidadesVentas.textContent = `${Number(venta[1]).toLocaleString('es-CL')} (unidades)`
            
            
        } catch (error) {
            console.error('Error al cargar la informacion:', error)
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        loadInfo()
    })
  </script>
