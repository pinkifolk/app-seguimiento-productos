<?php
session_start();
include "conn.php";
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}
$date = date('Y-m-d');

$titulo = 'App seguimiento | Traslado de Productos';
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
            width: 50%;
            display: inline-block;
            font-size: 11px;
        }
        .pill-color-pending{
            background-color: rgb(255 221 0 / 74%);
            border: solid rgb(255 221 0 / 64%);
        }
        .pill-color-received{
            background-color: rgb(179 219 65);
            border: solid rgb(171 213 51);
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
        .detail-scroll-container{
            max-height: 400px; 
            overflow-y: auto;  
            overflow-x: hidden; 
        }
        
</style>
<div class="container py-4">
    <h1 class="mb-2">Traslado de productos</h1>
    <div class="d-flex justify-content-end">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addToTransit">
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>
    <form class="d-flex py-4" role="search" onsubmit="return false;">
        <input class="form-control py-2" type="search" placeholder="Buscar fecha" aria-label="Search" id="search_input"/>
    </form>
    <div class="table-responsive card">
        <table class="table table-hover" >
            <thead class="table">
                <tr>
                    <th>N°</th>
                    <th>Fecha Llegada</th>
                    <th>Unidades</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="traslado_table_body">
                
            </tbody>
        </table>
    </div>
</div>
<!--modal para agregar-->
<div class="modal fade" id="addToTransit" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Nuevo traslado de productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
               <form>
                <div class="mb-3">
                  <label for="fecha_llegada" class="form-label">Fecha Llegada <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="fechaLlegada" name="fecha_llegada" value="<?php echo $date ?>">
                </div>
                <div class="mb-3">
                        <label for="fileTransit" class="d-flex flex-column justify-content-center align-items-center p-2 text-center"
                   style="border: 2px dashed #ccc; min-height: 100%; border-radius:10px; cursor:pointer; transition: background-color 0.3s;">
                            <i class="fa-solid fa-cloud-arrow-up fa-2xl mb-3 mt-3"></i>
                            <p class="mb-1">Carga masiva del detalle</p>
                            <p class="mb-0 text-muted" style="font-size: 10px;font-weight: 500;font-style: italic;">El archivo debe contener ID, CANTIDAD </p>
                            <p class="mb-0 text-muted" style="font-size: 10px;font-weight: bold;font-style: italic;">Puedes descargar el archivo de ejemple <a href="../resources/Maestro de cargas masivas.xlsx">aqui</a></p>
                            <input id="fileTransit" name="fileTransit" type="file" accept=".xlsx, .xls" class="d-none" required>
                        </label>
                </div>
                <div class="mb-3">
                  Verificar detalle  <span class="text-danger">*</span>
                  <div class="detail" id="detail"></div>
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4" id="confirmCreacion">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<!--modal detalle traslado-->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detalle de la solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form class="d-flex py-4" role="search" onsubmit="return false;">
                    <input class="form-control py-2" type="search" placeholder="Buscar id o codigo unificado" aria-label="Search" id="searchModal" name="searchModal">
                </form>
                <div class="table-responsive card detail-scroll-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Id</th>
                                <th>Código Unificado</th>
                                <th>Marca</th>
                                <th>Categoría</th>
                                <th>Cantidad</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody id="selectedProductsTableBody" class="align-middle">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" id="aceptarTraslado">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--modal para confirmar traslado-->
<div class="modal fade" id="confirmTransfer" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmación de traslado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              <h5 class="text-center px-3">¿Confirma que los productos ya estan en la bodega Quilicura?</h5>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4" id="confirmTraslado">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const searchInput = document.getElementById('search_input')
    const searchModal = document.getElementById('searchModal')
    const confirmCreacion = document.getElementById('confirmCreacion')
    const aceptarTraslado = document.getElementById('aceptarTraslado')
    const confirmTraslado = document.getElementById('confirmTraslado')
    const trasladoTableBody = document.getElementById('traslado_table_body')
    const modalDetalles = document.getElementById('detailModal')
    const modalTransit = document.getElementById('addToTransit')
    const modalConfirmarTraslado = document.getElementById('confirmTransfer')
    const confirmModal = document.getElementById('confirmTransfer')
    const selectedProductsTableBody = document.getElementById('selectedProductsTableBody')
    const fileInput = document.getElementById('fileTransit')
    const outputDiv = document.getElementById('detail')
    const fechaSolicitud = document.getElementById('fechaLlegada')
    let dataDelExcel = null
    
    toastr.options = {
    "closeButton": true, 
    "debug": false, 
    "newestOnTop": true, 
    "progressBar": true, 
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null, 
    "showDuration": "300", 
    "hideDuration": "1000",
    "timeOut": "5000", 
    "extendedTimeOut": "1000",
    "showEasing": "swing", 
    "hideEasing": "linear", 
    "showMethod": "fadeIn", 
    "hideMethod": "fadeOut" 
    }
    
    async function loadProducts(query = '') {
        try {
            const response = await fetch(`../controllers/get_transfers.php?accion=2&search_query=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const {traslado} = await response.json()
            
            trasladoTableBody.innerHTML = ''

            if (traslado.length === 0) {
                trasladoTableBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron traslados para mostrar.</td></tr>'
                return
            }

            traslado.forEach((product, index) => {
                let estado = product.estado === 0 ? 'En transito' : 'Quilicura'
                const row = `
                    <tr class="align-middle">
                        <td>${index+1}</td>
                        <td>${product.formato_fecha ? product.formato_fecha : ''}</td>
                        <td>${product.unidades}</td>
                        <td>
                            <span class='status-pill ${estado === 'En transito' ? 'pill-color-pending' : 'pill-color-received'}'>${estado}</span>
                        </td>
                         <td>
                            <i class="fa-solid fa-receipt fa-lg" role="button" data-bs-toggle="modal" data-id="${product.id}" data-bs-target="#detailModal"></i>
                            <i 
                            class="fa-solid ${estado === 'En transito' ? 'fa-truck-fast' : 'fa-circle-check text-success'} fa-lg ps-4" 
                            role="button" 
                            title="${estado === 'En transito' ? 'Confirmar' : 'Traslado confirmado'}" 
                            ${estado === 'En transito' ? 'data-bs-toggle="modal" data-bs-target="#confirmTransfer"' : ''}
                            data-id="${product.id}"></i>
                        </td>
                    
                    </tr>
                `
                trasladoTableBody.insertAdjacentHTML('beforeend', row)
            })
        } catch (error) {
            console.error('Error al cargar productos:', error)
            productsTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar los productos.</td></tr>'
        }
    }
    
    async function crearTraslado(fecha,data){
        try{
            const response = await fetch(`../controllers/get_transfers.php?accion=1`,{
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ datos: data, date: fecha })
            })
            if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }
            const result = await response.json()
            return result
            
        }catch(error){
            console.error(`Error al crear el traslado:`, error)
        }
    }
    
    async function eliminarDetalle(id){
        try {
            const response = await fetch(`../controllers/get_transfers.php?accion=4&id=${id}`)
            if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }
            const result = await response.json()
            return result
        } catch (error) {
            console.error(`Error al eliminar el detalle: ${id}`, error)
 
        }
        
    }
    async function confirmarLlegada(id){
        try{
            const response = await fetch(`../controllers/get_transfers.php?accion=5&id=${id}`)
            if(!response.ok){
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const result = await response.json()
            return result
            
        }catch(error){
            console.error(`Error al confirmar la llegada: ${id}`, error)
        }
    }
    
    async function buscarDetalle(productId,query =''){
        try {
            const response = await fetch(`../controllers/get_transfers.php?accion=3&id=${productId}&search_query=${encodeURIComponent(query)}`)
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
    
            const data = await response.json()
            let rowsHtml = ''
            const detalleArray = data.trasladoDet
            if (detalleArray && detalleArray.length > 0) {
                detalleArray.forEach((item, index) => {
                    rowsHtml += `
                        <tr>
                            <td>${index+1}</td>
                            <td>${item.producto_id}</td>
                            <td>${item.cod_unificado}</td>
                            <td>${item.marca}</td>
                            <td>${item.categoria}</td>
                            <td>
                                ${item.cantidad}
                            </td>
                            <td>
                                ${item.estado !== 1 ? `
                              <i 
                                class="fa-solid fa-trash fa-lg delete-detail-item"
                                role="button"
                                data-item-id="${item.id}"
                              ></i>
                            ` : ''}
                            </td>
                        </tr>
                    `
                })
                return rowsHtml
            }else{
                toastr.info('No hay registros para su busqueda.')
                return rowsHtml =''
            }
            
        } catch (error) {
            console.error("Error al cargar los detalles del producto:", error)
            toastr.error('No se pudieron cargar los detalles.', 'Error')
        }
    }
    
    modalConfirmarTraslado.addEventListener('show.bs.modal', function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        document.querySelector('#confirmTraslado').setAttribute('data-id',id)
    })
    modalDetalles.addEventListener('show.bs.modal', async function(event){
        let boton = event.relatedTarget
        let productId = boton.getAttribute('data-id')
        document.querySelector('#searchModal').setAttribute('data-id',productId)
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
    
    aceptarTraslado.addEventListener('click', function(){
        const bsModal = bootstrap.Modal.getInstance(modalDetalles)
        bsModal.hide()
        
    })
    
    confirmTraslado.addEventListener('click', async function(event){
        const boton = event.target
        const id = boton.getAttribute('data-id')
        const respuesta = await confirmarLlegada(id)
        if(respuesta.status){
            toastr.success(respuesta.mensaje)
        }else{
            toastr.error(respuesta.mensaje)
        }
        const bsModal = bootstrap.Modal.getInstance(confirmModal)
        bsModal.hide()
        loadProducts()
    })
    document.addEventListener('DOMContentLoaded', function() {
    
    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0]

        if (!file) {
            toastr.error('Por favor, selecciona un archivo.', 'Error')
            return
        }

        const fileExtension = file.name.split('.').pop().toLowerCase()
        if (fileExtension !== 'xlsx' && fileExtension !== 'xls') {
            toastr.error('Por favor, selecciona un archivo Excel (.xlsx o .xls).', 'Error')
            return
        }

        const reader = new FileReader()

        reader.onload = function(e) {
            const data = e.target.result

            try {
                const workbook = XLSX.read(data, { type: 'array' })

                const firstSheetName = workbook.SheetNames[0]
                const worksheet = workbook.Sheets[firstSheetName]

        
                dataDelExcel= XLSX.utils.sheet_to_json(worksheet, { header: 1 })
                

               
                const htmlTable = XLSX.utils.sheet_to_html(worksheet)

        
                outputDiv.innerHTML = `
     
                    ${htmlTable}
                `

            } catch (error) {
                console.error("Error al leer o parsear el archivo Excel:", error)
                outputDiv.innerHTML = '<p style="color: red;">Error al procesar el archivo Excel. Asegúrate de que es un archivo válido.</p>'
            }
        }

        reader.onerror = function() {
            outputDiv.innerHTML = '<p style="color: red;">Error al leer el archivo.</p>'
            console.error("Error al leer el archivo:", reader.error)
        }

        reader.readAsArrayBuffer(file)
    })
})
    modalDetalles.addEventListener('click', async function(event) {
        const target = event.target
        if (target.classList.contains('delete-detail-item')) {
            const itemId = target.getAttribute('data-item-id')
            // hacer el fetch para eliminar 
            const respuesta = await eliminarDetalle(itemId)
            const rowToRemove = document.querySelector(`#selectedProductsTableBody tr td i[data-item-id="${itemId}"]`).closest('tr')
            if (respuesta.status) {
                if(rowToRemove){
                    rowToRemove.remove()
                    toastr.success(respuesta.mensaje)
                }
                
            }else{
                 toastr.error(respuesta.mensaje)
            }
        }
        loadProducts()
    })
    confirmCreacion.addEventListener('click', async function(){
        const valorFecha = fechaLlegada.value;

        if (!valorFecha) {
            toastr.error('La fecha llegada es obligatoria');
            return;
        }
         if (!fileInput.files || fileInput.files.length === 0) {
            toastr.error('Debe adjuntar un archivo');
            return;
        }
        if(dataDelExcel === null){
            toastr.error('Error con la carga del archivo');
            return;
        }
        
        const result = await crearTraslado(valorFecha,dataDelExcel)
        
        if(!result.status){
            toastr.error(result.mensaje);
        }
        toastr.success(result.mensaje);
        const bsModal = bootstrap.Modal.getInstance(modalTransit)
        bsModal.hide()
        loadProducts()
        
    })
    modalTransit.addEventListener('hidden.bs.modal', function () {
        fileInput.value = ''
        fileInput.type = ''  
        fileInput.type = 'file'
        dataDelExcel = []
        outputDiv.innerHTML = ''
        
    })
    modalDetalles.addEventListener('hidden.bs.modal', function(){
        searchModal.value= ''
    })
    
</script>

