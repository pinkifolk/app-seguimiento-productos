<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: index.php");
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
</style>
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
          <button type="button" data-bs-dismiss="modal" aria-label="Cerrar"class="btn btn-danger">No</button>
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
<!-- Modal descripcion -->
<div class="modal fade" id="modalDescription" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
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
        </div>
        <div class="modal-footer">
          <button type="button" data-bs-dismiss="modal" aria-label="Cerrar" class="btn btn-danger">Cancelar</button>
          <button type="button" id="formDescripcion" class="btn btn-success">Confirmar</button>
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
    async function productosMultimedia(productoId){
        try{
            const response = await fetch(`../controllers/get_products.php?accion=3&id=${productoId}`)
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
        loadProducts()
        
    }
    async function productosFicha(productoId,descuento,ficha){
        try{
            const response = await fetch(`../controllers/get_products.php?accion=4&id=${productoId}&descuento=${descuento}&ficha=${ficha}`)
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
        loadProducts()
        
    }
    async function productosDescripcion(productoId,descripcion){
        try{
            const response = await fetch(`../controllers/get_products.php?accion=5&id=${productoId}&descripcion=${descripcion}`)
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
        loadProducts()
        
    }
    
    async function loadProducts(query = '') {
        try {
            const response = await fetch(`../controllers/get_products.php?accion=2&search_query=${encodeURIComponent(query)}`)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }
            const {products} = await response.json()
            
            productsTableBody.innerHTML = ''

            if (products.length === 0) {
                productsTableBody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron productos.</td></tr>'
                return
            }

            products.forEach((product) => {
                let readyImageHtml =''
                let readyDocHtml =''
                let readyDescriptionHtml = ''
                console.log(product)
                if(product.imagen === 1){
                    readyImageHtml =`<div class="ready">
                                    <i class="fa-solid fa-circle-check fa-xl" style="color: rgb(25, 135, 84);"></i>
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
                                            data-descuento="${product.descuento ?? "0"}">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </button>
                                </div>
                                
                                `
                }
                if(product.descripcion === 1){
                     readyDescriptionHtml =`<div class="ready">
                                    <i class="fa-solid fa-circle-check fa-xl" style="color: rgb(25, 135, 84);"></i>
                                </div>
                                `
                }else{
                    readyDescriptionHtml = `<div class="documents"><i class="fa-regular fa-keyboard fa-xl"></i></div>
                                <div class="image-overlay">
                                    <button type="button"
                                            class="change-image-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalDescription"
                                            data-id="${product.id_registro}"
                                            data-descripcion="${product.especificaciones}">
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
                        <td>${product.marca.charAt(0).toUpperCase() + product.marca.slice(1).toLowerCase()}</td>
                        <td>${product.categoria.charAt(0).toUpperCase() + product.categoria.slice(1).toLowerCase()}</td>
                        <td>${product.stock}</td>
                    </tr>
                `
                productsTableBody.insertAdjacentHTML('beforeend', row)
            })
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
        let descripcion = boton.getAttribute('data-descripcion')
        let descuento = boton.getAttribute('data-descuento')
        document.querySelector('textarea').value = descripcion
        document.querySelector('#descuento').value = parseInt(descuento) === 0 ?'Seleccione el descuento' : descuento
        document.querySelector('#formDoc').setAttribute('data-producto-id', id)
    })
    modalDescription.addEventListener('show.bs.modal', function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        let descripcion = boton.getAttribute('data-descripcion')
        document.querySelector('textarea').value = descripcion
        document.querySelector('#formDescripcion').setAttribute('data-producto-id', id)
    })
    modalImagen.addEventListener('show.bs.modal', function(event){
        let boton = event.relatedTarget
        let id = boton.getAttribute('data-id')
        document.querySelector('#formImagen').setAttribute('data-producto-id', id)
    })
    formImagen.addEventListener('click', function(event){
        let boton = event.target
        let id = boton.getAttribute('data-producto-id')
        productosMultimedia(id)
        const bsModal = bootstrap.Modal.getInstance(modalImagen)
        bsModal.hide()
    })
    formDoc.addEventListener('click', function(event){
        let boton = event.target
        let id = boton.getAttribute('data-producto-id')
        let descuento = document.querySelector('#descuento')
        let ficha = document.querySelector('#ficha')
        
        productosFicha(id,descuento.value,ficha.checked)
        const bsModal = bootstrap.Modal.getInstance(modalDetalles)
        bsModal.hide()

    })
    formDescripcion.addEventListener('click', function(event){
        let boton = event.target
        let id = boton.getAttribute('data-producto-id')
        let descripcion = document.querySelector('textarea')
        
        productosDescripcion(id,descripcion.value)
        const bsModal = bootstrap.Modal.getInstance(modalDescripcion)
        bsModal.hide()

    })

    
    
</script>



