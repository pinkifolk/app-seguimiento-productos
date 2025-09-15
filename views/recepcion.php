<?php
session_start();
include "conn.php";
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

$titulo = 'App seguimiento | Recepción de Productos';
ob_start();


$contenido = ob_get_clean();
include '../layout/layout.php';

?>
<style>
        .image-container {
            position: relative;
            width: 50px;
            height: 50px;
            display: inline-block;
        }
        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .status-pill{
            text-align: center;
            border-radius: 12px;
            padding-block: 2px;
            padding-inline: 11px;
            display: inline-block;
            font-size: 11px;
            width: 100%;
        }
        .pill-color-pending{
            background-color: rgb(255 0 0 / 74%);
            border: solid rgb(255 0 0 / 20%)
        }
        .pill-color-proccess{
            background-color: rgb(255 221 0 / 74%);
            border: solid rgb(255 221 0 / 20%);
        }
        .pill-color-finished{
            background-color: rgb(179 219 65);
            border: solid rgb(179 219 65);
        }
        .detail { 
            border: 1px solid #ccc; 
            padding: 10px; 
            min-height: 100px; 
            max-height: 400px; 
            overflow-y: auto;  
            overflow-x: auto;  
            border: 1px solid #CCC;
            
        }
        .detail table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .detail th, .detail td { border: 1px solid #ddd; padding: 2px; text-align: left; }
        .detail th { background-color: #f2f2f2; }
        .magnifying{
            position: absolute;
        }
        .detail-scroll-container{
            max-height: 400px; 
            overflow-y: auto;  
            overflow-x: hidden; 
        }
        .loader{
            background: #dee2e6;
            width: 20px;
            height: 7px;
            display: flex;
            align-items: center;
            border-radius: 7px;
        }
        @keyframes spin {
          0% {
            transform: rotate(0deg);
          }
          100% {
            transform: rotate(360deg);
          }
        }
        
        .spinner-animation {
          animation: spin 5s linear infinite;
        }
        
</style>

<!--
hablitar la opcion para que un producto no tenga un servicio y este listo para venderse
habilitar fecha de recepcion y id del usuario de la recepcion
marcar todos y nungunos poara marcarlos todos de una vez
agregar fechas en recepcion


nuevos
agregar fecha por linea  check
validar duplicados check
ver el detalle cuando ya este terminado  chek

<button data-product-id='${item.id}' class="btn btn-outline-secondary w-100"><i class="fa-solid fa-gear spinner-animation"></i></button>`
-->

<div class="container py-4">
    <h1 class="mb-2">Recepción de productos</h1>
    <form class="d-flex py-4" role="search" onsubmit="return false;">
        <input class="form-control me-3 py-2" type="search" placeholder="Buscar por fecha" aria-label="Search" id="search_input"/>
    </form>
    <div class="table-responsive card detail-scroll-containe">
        <table class="table table-hover" >
            <thead class="table">
                <tr>
                    <th>N°</th>
                    <th>Fecha solicitud</th>
                    <th>Unidades</th>
                    <th>Items</th>
                    <th>Fecha Recepción</th>
                    <th>Fecha Termino</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="products_table_body">
                
            </tbody>
        </table>
    </div>
</div>
<!--modal detalle de reparaciones-->
<div class="modal fade" id="recepcionModal" tabindex="-1" aria-labelledby="repairModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repairModalLabel">Detalle de la solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>Ingrese los servicios que usted considere necesario para cada producto:</p>
                 <form class="d-flex py-4" role="search" onsubmit="return false;">
                    <input class="form-control py-2" type="search" placeholder="Buscar por id" aria-label="Search" id="searchModal" name="searchModal">
                </form>
                <div class="table-responsive card detail-scroll-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Id</th>
                                <th>Código Unificado</th>
                                <th>Cantidad</th>
                                <th>Limpieza</th>
                                <th>Pintura</th>
                                <th>Banco Pruebas</th>
                                <th>
                                    Todos
                                    <input type="checkbox" id="checkAllTodos" class="form-check-input ms-1">
                                </th>
                                <th>
                                    Ninguno
                                    <input type="checkbox" id="checkAllNinguno" class="form-check-input ms-1">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="selectedProductsTableBody" class="align-middle">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmarRecepcion">Recepcionar</button>
            </div>
        </div>
    </div>
</div>
<!--modal para terminar la reparacion-->
<div class="modal fade" id="processoRecepcion" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processModalLabel">Confirmación reparacion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                 <form class="d-flex py-4" role="search" onsubmit="return false;">
                    <input class="form-control py-2" type="search" placeholder="Buscar por id" aria-label="Search" id="searchProcessModal" name="searchProcessModal">
                </form>
                <div class="table-responsive card detail-scroll-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Id</th>
                                <th>Código Unificado</th>
                                <th>Cantidad</th>
                                <th>Limpieza</th>
                                <th>Pintura</th>
                                <th>Banco Pruebas</th>
                                <th>Inicio</th>
                                <th>Termino</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="selectedProductsReceptionTableBody" class="align-middle">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4" id="confirmarProceso">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<!--modal para ver lo terminado-->
<div class="modal fade" id="verTerminada" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showModalLabel">Reparación Terminada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                 <form class="d-flex py-4" role="search" onsubmit="return false;">
                    <input class="form-control py-2" type="search" placeholder="Buscar por id" aria-label="Search" id="searchProcessModal" name="searchProcessModal">
                </form>
                <div class="table-responsive card detail-scroll-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Id</th>
                                <th>Código Unificado</th>
                                <th>Cantidad</th>
                                <th>Limpieza</th>
                                <th>Pintura</th>
                                <th>Banco Pruebas</th>
                                <th>Inicio</th>
                                <th>Termino</th>
                            </tr>
                        </thead>
                        <tbody id="selectedProductsShowTableBody" class="align-middle">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success px-4" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const searchInput = document.getElementById('search_input')
    const searchModal = document.getElementById('searchModal')
    const searchProcessModal = document.getElementById('searchProcessModal')
    const productsTableBody = document.getElementById('products_table_body')
    const recepcionModal = document.getElementById('recepcionModal')
    const processoRecepcion = document.getElementById('processoRecepcion')
    const verTerminada = document.getElementById('verTerminada')
    const selectedProductsTableBody = document.getElementById('selectedProductsTableBody')
    const selectedProductsReceptionTableBody = document.getElementById('selectedProductsReceptionTableBody')
    const selectedProductsShowTableBody = document.getElementById('selectedProductsShowTableBody')
    const confirmarRecepcion = document.getElementById('confirmarRecepcion')
    const confirmarProceso = document.getElementById('confirmarProceso')
    
    async function loadProducts(query = '') {
        
        try {
            const response = await fetch(`../controllers/get_receptions.php?accion=2&search_query=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const {reparacion} = await response.json()
            
            productsTableBody.innerHTML = ''

            if (reparacion.length === 0) {
                productsTableBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron productos para reparar.</td></tr>'
                return
            }

            reparacion.forEach((product, index) => {
                if(product.estado === 0){
                    statusColorHtml =`<span class='status-pill pill-color-pending'>Pendiente</span>`
                    statusActionHtml =`<i class="fa-solid fa-receipt fa-lg ps-4" title="Pendiente de Recepción" role="button" data-bs-toggle="modal" data-id="${product.id}" data-bs-target="#recepcionModal"></i>`
                
                }else if(product.estado === 1){
                    statusColorHtml =`<span class='status-pill pill-color-proccess'>Recepcionado</span>`
                    statusActionHtml =`<i class="fa-solid fa-clock fa-lg ps-4" role="button" data-id="${product.id}" data-bs-toggle="modal" data-bs-target="#processoRecepcion"></i>`
                    
                }else{
                    statusColorHtml =`<span class='status-pill pill-color-finished'>Terminado</span>`
                    statusActionHtml =`<i class="fa-solid fa-circle-check text-success fa-lg ps-4" title="Recepcion terminada" role="button" data-id="${product.id}" data-bs-toggle="modal" data-bs-target="#verTerminada"></i>`
                }
                const row = `
                    <tr class="align-middle">
                        <td>${index+1}</td>
                        <td>${product.formato_fecha}</td>
                        <td>${product.unidades}</td>
                        <td>${product.items}</td>
                        <td>${product.formato_recepcion ? product.formato_recepcion : ''}</td>
                        <td>${product.formato_termino ? product.formato_termino : ''}</td>
                        <td>
                             ${statusColorHtml}
                        </td>
                        <td>
                            ${statusActionHtml}
                        </td>
                    </tr>
                `
                productsTableBody.insertAdjacentHTML('beforeend', row)
            })
        } catch (error) {
            console.error('Error al cargar productos:', error)
            productsTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar los productos.</td></tr>'
        }
    }
    async function recepcionarProductos(data, id){
         try{
            const response = await fetch(`../controllers/get_receptions.php?accion=5`,{
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ datos: data, id: id })
            })
            if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }
            const result = await response.json()
            return result
            
        }catch(error){
            console.error(`Error al crear la recepcion:`, error)
        }
        
    }
    async function buscarDetalle(productId,query =''){
        try {
            const response = await fetch(`../controllers/get_receptions.php?accion=3&id=${productId}&search_query=${encodeURIComponent(query)}`)
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
    
            const data = await response.json()
            let rowsHtml = ''
            const detalleArray = data.recepcionDet
            if (detalleArray && detalleArray.length > 0) {
                detalleArray.forEach((item, index) => {
                    rowsHtml += `
                        <tr>
                        <td data-id='${item.id}'>${index+1}</td>
                        <td>${item.producto_id}</td>
                        <td>${item.cod_unificado}</td>
                        <td>${item.cantidad}</td>
                        <td>
                            <input type='checkbox' class='form-check-input ms-4 service-pintura' data-product-id='${item.id}'>
                        </td>
                        <td>
                            <input type='checkbox' class='form-check-input ms-4 service-reparacion' data-product-id='${item.id}'>
                        </td>
                        <td>
                            <input type='checkbox' class='form-check-input ms-1 service-certificacion' data-product-id='${item.id}'>
                        </td>
                        <td>
                           <input type='checkbox' class='form-check-input ms-1 service-todos' data-product-id='${item.id}'>
                        </td>
                        <td>
                           <input type='checkbox' class='form-check-input ms-1 service-ninguno' data-product-id='${item.id}'>
                        </td>
                    </tr>
                    `
                })
                return rowsHtml
            }
            
        } catch (error) {
            console.error("Error al cargar los detalles del producto:", error)
            toastr.error('No se pudieron cargar los detalles.')
        }
    }
    async function cambiarEstado(data,id){
        try{
            const response = await fetch(`../controllers/get_receptions.php?accion=7`,{
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ datos: data,id:id })
            })
            if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }
            const result = await response.json()
            return result
            
        }catch(error){
            console.error(`Error al crear la recepcion:`, error)
        }
        
    }
    async function procesarDetalle(productId,query =''){
        try {
            const response = await fetch(`../controllers/get_receptions.php?accion=6&id=${productId}&search_query=${encodeURIComponent(query)}`)
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
    
            const data = await response.json()
            let rowsHtml = ''
            const detalleArray = data.recepcionDet
            if (detalleArray && detalleArray.length > 0) {
                detalleArray.forEach((item, index) => {
                    rowsHtml += `
                    <tr>
                        <td data-id='${item.id}'>${index+1}</td>
                        <td>${item.producto_id}</td>
                        <td>${item.cod_unificado}</td>
                        <td>${item.cantidad}</td>
                        <td>
                            ${item.limpieza ? '<i class="fa-solid fa-check ms-4" style="color: #27c507;"></i>' : '<i class="fa-solid fa-x ms-4" style="color: #eb0f0f;"></i>'}
                        </td>
                        <td>
                            ${item.pintura ? '<i class="fa-solid fa-check ms-4" style="color: #27c507;"></i>' : '<i class="fa-solid fa-x ms-4" style="color: #eb0f0f;"></i>'}
                        </td>
                        <td>
                            ${item.banco_pruebas ? '<i class="fa-solid fa-check ms-4" style="color: #27c507;"></i>' : '<i class="fa-solid fa-x ms-4" style="color: #eb0f0f;"></i>'}
                        </td>
                        <td>
                            ${item.formato_inicio ? item.formato_inicio  : '' }
                        </td>
                        <td>
                            ${item.formato_termino ? item.formato_termino  : '' }
                        </td>
                        <td>
                             ${item.estado == 0 
                                ? `<button type='button' class="btn-estado btn btn-outline-secondary w-100" data-estado="${item.estado}" onclick="start('${item.id}')" title="Iniciar reparación"><i class="fa-solid fa-play"></i></button>` 
                                : item.estado == 1
                                    ? `<button type='button' id="btn-finish" class="btn-estado btn btn-outline-secondary w-100" data-estado="${item.estado}" onclick="finish('${item.id}')"  title="Reparación en proceso"><i class="fa-solid fa-gear spinner-animation"></i></button>`
                                    : `<button type='button' class="btn-estado btn btn-outline-success w-100" data-estado="${item.estado}" title="Reparación Terminada" disabled><i class="fa-solid fa-trophy"></i></button>`}

                             
                             
                        </td>
                    </tr>
                    `
                })
                return rowsHtml
            }
            
        } catch (error) {
            console.error("Error al cargar los detalles del producto:", error)
            toastr.error('No se pudieron cargar los detalles.')
        }
    }
    async function start (id){
        const btn = document.getElementById('confirmarProceso')
        const productId = btn.dataset.id
        
        try{
            const response = await fetch(`../controllers/get_receptions.php?accion=8&id=${id}`)
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const data = await response.json();
            if (data.status) {
              const rowsHtml = await procesarDetalle(productId, '');
                if(rowsHtml && rowsHtml.trim() !== ''){
                    
                    if (selectedProductsReceptionTableBody) {
                        selectedProductsReceptionTableBody.innerHTML = rowsHtml;
                    }
                    
                }else{
                    selectedProductsReceptionTableBody.innerHTML = ''
                    toastr.info('No hay registros para su busqueda.')
                }
                const btnFinish = document.getElementById('btn-finish')
                btnFinish.disabled = true
        
                setTimeout(() =>{
                    btnFinish.disabled=false
                },60 * 1000)
                
               DisableIf()
            } else {
              toastr.error('No se pudo actualizar el registro.');
            }
        }
        catch(error){
            console.error("Error al cambiar estado:", error)
            toastr.error('No se pudo cambiar el estado.')
        }
    }
    async function finish (id){
        const btn = document.getElementById('confirmarProceso')
        const productId = btn.dataset.id
        try{
            const response = await fetch(`../controllers/get_receptions.php?accion=9&id=${id}`)
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const data = await response.json();
            if (data.status) {
              const rowsHtml = await procesarDetalle(productId, '');
                if(rowsHtml && rowsHtml.trim() !== ''){
                    
                    if (selectedProductsReceptionTableBody) {
                        selectedProductsReceptionTableBody.innerHTML = rowsHtml;
                    }
                    
                }else{
                    selectedProductsReceptionTableBody.innerHTML = ''
                    toastr.info('No hay registros para su busqueda.')
                }
            } else {
              toastr.error('No se pudo actualizar el registro.');
            }
            
        }
        catch(error){
            console.error("Error al cambiar estado:", error)
            toastr.error('No se pudo cambiar el estado.')
        }
    }
    function DisableIf(){
        const botones = document.querySelectorAll('.btn-estado')
        const enProceso = Array.from(botones).some(btn => btn.dataset.estado == "1")
        if(enProceso){
           botones.forEach(btn =>{
               if(btn.dataset.estado != "1"){
                    btn.disabled = true
               }    
           }) 
        }
    }
    
    let searchModalTimeout
    searchModal.addEventListener('input', (event)=>{
        let boton = event.target
        let productId = boton.getAttribute('data-id')
        clearTimeout(searchModalTimeout)
        searchModalTimeout = setTimeout(async ()=>{
            const query= searchModal.value.trim()
           
            const rowsHtml = await buscarDetalle(productId, query);
            if(rowsHtml && rowsHtml.trim() !== ''){
                
                if (selectedProductsTableBody) {
                    selectedProductsTableBody.innerHTML = rowsHtml;
                }
                
            }else{
                selectedProductsTableBody.innerHTML = ''
                toastr.info('No hay registros para su busqueda.')
            }
            
        },300)
    })
    let searchModalProsesoTimeout
    searchProcessModal.addEventListener('input', (event)=>{
        let boton = event.target
        let productId = boton.getAttribute('data-id')
        clearTimeout(searchModalProsesoTimeout)
        searchModalProsesoTimeout = setTimeout(async ()=>{
            const query= searchProcessModal.value.trim()
           
            const rowsHtml = await procesarDetalle(productId, query);
            if(rowsHtml && rowsHtml.trim() !== ''){
                
                if (selectedProductsReceptionTableBody) {
                    selectedProductsReceptionTableBody.innerHTML = rowsHtml;
                }
                
            }else{
                selectedProductsReceptionTableBody.innerHTML = ''
                toastr.info('No hay registros para su busqueda.')
            }
        },300)
    })
    
    let searchTimeout
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout)
        searchTimeout = setTimeout(() => {
            const query = searchInput.value.trim()
            loadProducts(query)
        }, 300) 
    })

    document.addEventListener('DOMContentLoaded', () => {
        loadProducts()
    })
    

    recepcionModal.addEventListener('show.bs.modal', async function(event) {
        let boton = event.relatedTarget
        let productId = boton.getAttribute('data-id')
        confirmarRecepcion.setAttribute('data-id',productId)
        searchModal.setAttribute('data-id',productId)
        searchProcessModal.setAttribute('data-id',productId)
        
        
        if (selectedProductsTableBody) {
            selectedProductsTableBody.innerHTML = ''
        }
        
        let rowsHtml = await buscarDetalle(productId)
        
        if (selectedProductsTableBody) {
            selectedProductsTableBody.innerHTML = rowsHtml
        } else {
            console.error("El elemento 'selectedProductsTableBody' no fue encontrado en el DOM del modal.")
        }
        
    })

    processoRecepcion.addEventListener('show.bs.modal', async function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        document.querySelector('#searchProcessModal').setAttribute('data-id',id)
        confirmarProceso.setAttribute('data-id',id)
        if (selectedProductsReceptionTableBody) {
            selectedProductsReceptionTableBody.innerHTML = ''
        }
        let rowsHtml = await procesarDetalle(id)
        
        if (selectedProductsReceptionTableBody) {
            selectedProductsReceptionTableBody.innerHTML = rowsHtml
        } else {
            console.error("El elemento 'selectedProductsTableBody' no fue encontrado en el DOM del modal.")
        }
        DisableIf()
    })
    verTerminada.addEventListener('show.bs.modal', async function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        if (selectedProductsShowTableBody) {
            selectedProductsShowTableBody.innerHTML = ''
        }
        
        let rowsHtml = await procesarDetalle(id)
        const temp = document.createElement("tbody")
        temp.innerHTML = rowsHtml
        temp.querySelectorAll("tr").forEach(tr =>{
            tr.lastElementChild.remove()
        })
        
        if (selectedProductsShowTableBody) {
            selectedProductsShowTableBody.innerHTML = temp.innerHTML
        } else {
            console.error("El elemento 'selectedProductsTableBody' no fue encontrado en el DOM del modal.")
        }
    })
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('service-todos')) {
            const productId = e.target.getAttribute('data-product-id');
            const isChecked = e.target.checked;
    
            document.querySelector(`.service-pintura[data-product-id="${productId}"]`).checked = isChecked;
            document.querySelector(`.service-reparacion[data-product-id="${productId}"]`).checked = isChecked;
            document.querySelector(`.service-certificacion[data-product-id="${productId}"]`).checked = isChecked;
        }
    })
    
    confirmarRecepcion.addEventListener('click', function(event) {
        const boton = event.target
        const id = boton.getAttribute('data-id')
        const detalleProductosCheck = []
        let validarTodoslosChecks = true
        let serviciosSeleccionados = false
        selectedProductsTableBody.querySelectorAll('tr').forEach(row => {
            const idCelda = row.querySelector('td[data-id]')
            const productoId = idCelda ? idCelda.getAttribute('data-id') : null
            const item = row.querySelector('td:first-child')
            const index = item ? item.textContent : null
            
            
            const ServiciosSeleccionadosDeProductos = []
            const serviciosCheckbox = row.querySelectorAll('input[type="checkbox"][class*="service-"]')
            
            serviciosCheckbox.forEach(checkbox => {
               if (checkbox.checked) {

                   const servicio = Array.from(checkbox.classList).find(cls => cls.startsWith('service-'))?.replace('service-', '')
                        if (servicio) {
                            ServiciosSeleccionadosDeProductos.push(servicio);
                        }
                }

            })
            if (ServiciosSeleccionadosDeProductos.length === 0) {
                validarTodoslosChecks = false
                toastr.error(`Para el Item: ${index}, debe seleccionar al menos un servicio.`)
                return
            } else {
                serviciosSeleccionados = true
                detalleProductosCheck.push({
                    id: productoId,
                    servicios: ServiciosSeleccionadosDeProductos
                })
            }
        })
         if (validarTodoslosChecks && detalleProductosCheck.length > 0) {
            const recepcionar = recepcionarProductos(detalleProductosCheck,id) 
            toastr.success('Recepción realizada correctamente')
            const bsModal = bootstrap.Modal.getInstance(recepcionModal)
            bsModal.hide()
            loadProducts()
            
        } else {
            toastr.error('Faltan servicios en algunos productos')
            console.warn('Faltan servicios en algunos productos')
        }
    })
    confirmarProceso.addEventListener('click', async function(event){
        let boton = event.target
        const idCabecera = boton.getAttribute('data-id')
        const seleccionados = []

        document.querySelectorAll('input.proceso:checked:not(:disabled)').forEach(checkbox => {
            const id = checkbox.getAttribute('data-product-id')
            seleccionados.push(id)
        })  
        const result = await cambiarEstado(seleccionados,idCabecera)
        toastr.success('Actualización realizada correctamente')
        const bsModal = bootstrap.Modal.getInstance(processoRecepcion)
        bsModal.hide()
        loadProducts()

    })
    processoRecepcion.addEventListener('hidden.bs.modal', function(){
        searchModal.value = ''
        searchProcessModal.value =''
    })
    recepcionModal.addEventListener('hidden.bs.modal', function(){
        searchModal.value = ''
        searchProcessModal.value =''
    })
    document.addEventListener("change", function(e) {
  
      if (e.target.matches(".service-pintura, .service-reparacion, .service-certificacion, .service-todos")) {
        const row = e.target.closest("tr")
        const ninguno = row.querySelector(".service-ninguno")
        if (e.target.checked) {
          ninguno.checked = false
        }
      }
    
      if (e.target.matches(".service-ninguno")) {
        const row = e.target.closest("tr")
        if (e.target.checked) {
          row.querySelectorAll(".service-pintura, .service-reparacion, .service-certificacion, .service-todos")
            .forEach(cb => cb.checked = false)
        }
      }
      if (e.target.id === 'checkAllTodos') {
        const checked = e.target.checked
        document.querySelectorAll('tbody .service-todos').forEach(rowTodos => {
          rowTodos.checked = checked
          const row = rowTodos.closest('tr')
          row.querySelectorAll('.service-pintura, .service-reparacion, .service-certificacion')
             .forEach(c => c.checked = checked)
             if (checked) {
          const ninguno = row.querySelector('.service-ninguno')
          if (ninguno) ninguno.checked = false
            }
        });
        const headerNinguno = document.getElementById('checkAllNinguno')
        if (checked && headerNinguno) headerNinguno.checked = false
        return
      }
      if (e.target.id === 'checkAllNinguno') {
        const checked = e.target.checked
        document.querySelectorAll('tbody .service-ninguno').forEach(rowTodos => {
          rowTodos.checked = checked
          const row = rowTodos.closest('tr')
          row.querySelectorAll('.service-pintura, .service-todos, .service-reparacion, .service-certificacion')
             .forEach(c => c.checked = false)
        });
        const headerTodos = document.getElementById('checkAllTodos')
         if (headerTodos) headerTodos.checked = false
        return
       }
    });


    
    
</script>

