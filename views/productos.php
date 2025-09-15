<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: ../index.php");
    exit();
}

$titulo = 'App seguimiento | Productos';
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
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .image-container:hover .image-overlay {
            opacity: 1;
        }
        .change-image-btn {
            color: white;
            border: none;
            background: transparent;
            padding: 0;
        }
        .change-image-btn:hover {
            color: #fff;
            transform: scale(1.1);
        }
        .ready{
            background: white;
            width: 50px;
            height: 50px;
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .documents{
            background: white;
            width: 50px;
            height: 50px;
            position: absolute;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .switch {
          position: relative;
          display: inline-block;
          width: 50px; 
          height: 25px; 
        }
        .switch input {
          opacity: 0;
          width: 0;
          height: 0;
        }
        
        
        .slider {
          position: absolute;
          cursor: pointer;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background-color: #dc3545;
          -webkit-transition: .4s;
          transition: .4s;
        }
        .slider:before {
          position: absolute;
          content: "";
          height: 22px; 
          width: 22px; 
          left: 2px;
          bottom: 1px; 
          background-color: white; 
          -webkit-transition: .4s;
          transition: .4s;
        }
        input:checked + .slider {
          background-color: #198754; 
        }
        input:checked + .slider:before {
          -webkit-transform: translateX(23px); 
          -ms-transform: translateX(23px);
          transform: translateX(23px);
        }
        .slider.round {
          border-radius: 34px;
        }
        .slider.round:before {
          border-radius: 50%; 
        }
        .pagination .page-item.active .page-link {
        background-color: #b8b8b8;
        border-color: #ffffff;
        color: #ffffff; 
    }
</style>
<!--paginador y revertir la accion del los modades

cargar planilla y el boton de check mantenerlo y el del lado muestra toda la info no editable
agregar descripcion precio y descuento a una tabla y la equivalencia del producto
-->
<div class="container py-4">
    <h1 class="mb-4">Productos</h1>
    <span>Seguimiento de productos</span>
    <form class="d-flex py-4" role="search" onsubmit="return false;">
        <input class="form-control me-3 py-2" type="search" placeholder="Buscar por código, marca, id, especificaciones" aria-label="Search" id="search_input"/>
    </form>
    <div class="table-responsive card">
        <table class="table table-hover" >
            <thead class="table">
                <tr>
                    <th>id</th>
                    <th>Producto</th>
                    <th>Detalles</th>
                    <th>Codigo Unificado</th>
                    <th>Marca</th>
                    <th>Categoria</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody id="products_table_body">
                
            </tbody>
        </table>
        <nav aria-label="paginacion" class="d-flex justify-content-center">
          <ul class="pagination">
          </ul>
        </nav>
    </div>
</div>
<!-- Modal imagen -->
<div class="modal fade" id="modalImagen" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Imagen producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body d-flex justify-content-center">
            <h5>¿Tienes la imagen de este producto?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" id="formImagenNo" class="btn btn-danger">No</button>
          <button type="button" id="formImagen" class="btn btn-success">Si</button>
        </div>
    </div>
  </div>
</div>
<!-- Modal Documentacion 
dividir la descripcion de la ficha y del descuento 
-->
<div class="modal fade" id="modalDoc" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Detalles</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label><span class="text-danger">*</span>
              <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="precio" class="form-label">Precio</label><span class="text-danger">*</span>
              <input type="number" class="form-control" id="precio" name="precio"></textarea>
            </div>
            <div class="mb-3">
              <label for="descuento" class="form-label">Descuento</label><span class="text-danger">*</span>
                  <select class="form-select" id="descuento" name="descuento" aria-label="Descuento">
                    <option values="0" selected>Seleccione el descuento</option>
                      <option value="15">15%</option>
                      <option value="20">20%</option>
                      <option value="25">25%</option>
                      <option value="30">30%</option>
                      <option value="35">35%</option>
                      <option value="40">40%</option>
                      <option value="45">45%</option>
                      <option value="50">50%</option>
                      <option value="55">55%</option>
                      <option value="60">60%</option>
                      <option value="65">65%</option>
                      <option value="70">70%</option>
                  </select>
            </div>
            <div class="mb-3">
                ¿Tienes la ficha del producto?
                <label class="switch">
                  <input type="checkbox" id="ficha">
                  <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" data-bs-dismiss="modal" aria-label="Cerrar" class="btn btn-danger">Cancelar</button>
          <button type="button" id="formDoc" class="btn btn-success">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    const modalDetalles = document.getElementById('modalDoc')
    const modalImagen = document.getElementById('modalImagen')
    const modalDescripcion = document.getElementById('modalDescription')
    const searchInput = document.getElementById('search_input')
    const productsTableBody = document.getElementById('products_table_body')
    const formImagen = document.getElementById('formImagen')
    const formImagenNo = document.getElementById('formImagenNo')
    const formDoc = document.getElementById('formDoc')
    const formDescripcion = document.getElementById('formDescripcion')
    
    
    
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
    function renderPagination(total, currentPage, limit) {
        const totalPages = Math.ceil(total / limit)
        const pagination = document.querySelector('.pagination')
        const search = document.getElementById('search_input').value || ''
        pagination.innerHTML = ''
    
        pagination.innerHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link text-black" href="#" data-page="${currentPage - 1}" data-search="${search}">Anterior</a>
            </li>
        `
        for (let i = 1; i <= totalPages; i++) {
            pagination.innerHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link text-black" href="#" data-page="${i}">${i}</a>
                </li>
            `
        }
        
        pagination.innerHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link text-black" href="#" data-page="${currentPage + 1}" data-search="${search}">Siguiente</a>
            </li>
        `
        pagination.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault()
                const page = parseInt(e.target.dataset.page)
                const search = document.getElementById('search_input').value
                if (!isNaN(page)) {
                    loadProducts(search, page)
                }
            })
        })
    }
    async function productosMultimedia(productoId,estado){
        try{
            const response = await fetch(`../controllers/get_products.php?accion=3&id=${productoId}&imagen=${estado}`)
            if(!response.ok){
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const {status} = await response.json()
            
            if(!status){
                toastr.error('No se pudo actulizar el registro.')
            }
           
            toastr.success('Registro actualizado.')
            
        }catch(error){
          console.error('Error al cargar productos:', error)  
        }
        loadProducts(document.getElementById('search_input').value)
        
    }
    async function productosFicha(productoId,descripcion,precio,descuento,ficha){
        try{
            const response = await fetch(`../controllers/get_products.php?accion=4&id=${productoId}&descripcion=${descripcion}&precio=${precio}&descuento=${descuento}&ficha=${ficha}`)
            if(!response.ok){
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const {status, mensaje} = await response.json()
            
            if(!status){
                toastr.error(mensaje)
                return
            }
           
            toastr.success(mensaje)
            
        }catch(error){
          console.error('Error al cargar productos:', error)  
        }
        loadProducts(document.getElementById('search_input').value)
        
    }
    
    async function loadProducts(query = '', page = 1) {
        try {
            const response = await fetch(`../controllers/get_products.php?accion=2&search_query=${encodeURIComponent(query)}&page=${page}`)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const data = await response.json()
            const {products, total, page: currentPage, limit} = data.products
            
            productsTableBody.innerHTML = ''

            if (products.length === 0) {
                productsTableBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron productos.</td></tr>'
                return
            }

            products.forEach((product) => {
                let readyImageHtml =''
                let readyDocHtml =''
                let readyDescriptionHtml = ''
                if(product.imagen === 1){
                    readyImageHtml =`<div class="ready">
                                    <i class="fa-solid fa-circle-check fa-xl" style="color: rgb(25, 135, 84);"></i>
                                    <div class="image-overlay" title="${product.especificaciones}">
                                        <button type="button"
                                                class="change-image-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalImagen"
                                                data-id="${product.id_registro}">
                                            <i class="fas fa-camera fa-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                `
                }else{
                     readyImageHtml =`<img src="https://b2b.provaltec.cl/admin/multimedia/no_disponible.png" alt="${product.especificaciones}" title="${product.especificaciones}" class="img-thumbnail rounded-circle">
                                <div class="image-overlay" title="${product.especificaciones}">
                                    <button type="button"
                                            class="change-image-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalImagen"
                                            data-id="${product.id_registro}">
                                        <i class="fas fa-camera fa-lg"></i>
                                    </button>
                                </div>
                                `
                }
                if(product.ficha === 1){
                     readyDocHtml =`<div class="ready">
                                    <i class="fa-solid fa-circle-check fa-xl" style="color: rgb(25, 135, 84);"></i>
                                    <div class="image-overlay">
                                        <button type="button"
                                                class="change-image-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalDoc"
                                                data-id="${product.id_registro}"
                                                data-descuento="${product.descuento ?? "0"}"
                                                data-descripcion="${product.especificaciones}"
                                                data-precio="${product.precio ?? ""}"
                                                data-equivalente="${product.prod_equivalente ?? ""}"
                                                data-ficha="${product.ficha ?? 0}">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </button>
                                    </div>
                                </div>
                                `
                    
                }else{
                    readyDocHtml =`<div class="documents"><i class="fa-solid fa-list fa-xl"></i></div>
                                <div class="image-overlay">
                                    <button type="button"
                                            class="change-image-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDoc"
                                            data-id="${product.id_registro}"
                                            data-descuento="${product.descuento ?? "0"}"
                                            data-descripcion="${product.especificaciones}"
                                            data-precio="${product.precio ?? ""}"
                                            data-equivalente="${product.prod_equivalente ?? ""}"
                                            data-ficha="${product.ficha ?? 0}">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </button>
                                </div>
                                
                                `
                }   
                const row = `
                    <tr class="align-middle">
                        <td>${product.id}</td>
                        <td>
                            <div class="image-container">
                                ${readyImageHtml}
                            </div>
                        </td>
                        <td>
                            <div class="image-container">
                                ${readyDocHtml}
                            </div>
                            <div class="image-container">
                                ${readyDescriptionHtml}
                            </div>
                        </td>
                        <td>${product.cod_unificado}</td>
                        <td>${product.marca}</td>
                        <td>${product.categoria}</td>
                        <td>${product.stock}</td>
                    </tr>
                `
                productsTableBody.insertAdjacentHTML('beforeend', row)
            })
            
            renderPagination(total, currentPage, limit)
            
        } catch (error) {
            console.error('Error al cargar productos:', error)
            productsTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar los productos.</td></tr>'
        }
    }
    function getProductDataFromRow(rowElement) {
        const cells = rowElement.querySelectorAll('td');
        return {
            id: cells[1].textContent, 
            cod_unificado: cells[4].textContent, 
            marca: cells[5].textContent, 
            categoria: cells[6].textContent,
            stock: cells[7].textContent,
        }
    }

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
    

    modalDetalles.addEventListener('show.bs.modal', function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        let descuento = boton.getAttribute('data-descuento')
        let descripcion = boton.getAttribute('data-descripcion')
        let precio = boton.getAttribute('data-precio')
        let equivalencia = boton.getAttribute('data-equivalente')
        let ficha = boton.getAttribute('data-ficha')
        document.querySelector('#descuento').value = parseInt(descuento) === 0 ?'Seleccione el descuento' : descuento
        document.querySelector('#formDoc').setAttribute('data-producto-id', id)
        document.querySelector('textarea').value = descripcion
        document.querySelector('#precio').value = precio
        document.querySelector('#ficha').checked = ficha === "1"
        
        
        
        let tieneFicha = boton.closest('.ready') !== null

        let inputs = modalDetalles.querySelectorAll('input, textarea, select, button#formDoc')
    
        if (tieneFicha) {
            inputs.forEach(el => el.setAttribute('disabled', 'true'))
        } else {
            inputs.forEach(el => el.removeAttribute('disabled'))
        }
    })
    modalImagen.addEventListener('show.bs.modal', function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        document.querySelectorAll('#formImagen, #formImagenNo').forEach(el => el.setAttribute('data-producto-id', id))
    })
    formImagen.addEventListener('click', function(event){
        let boton = event.target
        let id = boton.getAttribute('data-producto-id')
        productosMultimedia(id,1)
        const bsModal = bootstrap.Modal.getInstance(modalImagen)
        bsModal.hide()
    })
    formImagenNo.addEventListener('click', function(event){
        let boton = event.target
        let id = boton.getAttribute('data-producto-id')
        productosMultimedia(id,0)
        const bsModal = bootstrap.Modal.getInstance(modalImagen)
        bsModal.hide()
    })
    formDoc.addEventListener('click', function(event){
        let boton = event.target
        let id = boton.getAttribute('data-producto-id')
        let descripcion = document.querySelector('textarea')
        let precio = document.querySelector('#precio')
        let descuento = document.querySelector('#descuento')
        let ficha = document.querySelector('#ficha')
        
        productosFicha(id,descripcion.value,precio.value,descuento.value,ficha.checked)
        const bsModal = bootstrap.Modal.getInstance(modalDetalles)
        bsModal.hide()

    })
    
    

    
    
</script>



