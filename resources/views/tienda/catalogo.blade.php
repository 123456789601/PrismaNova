@extends('layouts.app')
@section('title','Tienda')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-primary"><i class="bi bi-shop me-2"></i>Catálogo</h4>
            <p class="text-secondary small mb-0">Explora nuestros productos disponibles</p>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <div class="input-group shadow-sm rounded-pill overflow-hidden" style="max-width: 300px;">
                <span class="input-group-text border-0 bg-secondary bg-opacity-10 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input id="search" class="form-control border-0 bg-secondary bg-opacity-10" placeholder="Buscar producto...">
            </div>
            <a href="{{ route('tienda.carrito') }}" class="btn btn-primary rounded-pill px-4 shadow-lg transform-hover position-relative">
                <i class="bi bi-cart3 me-2"></i>Carrito 
                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light shadow-sm">0</span>
            </a>
        </div>
    </div>

    <div id="grid" class="row g-4"></div>
    
    <nav class="mt-5 d-flex justify-content-center">
        <ul class="pagination pagination-pill shadow-sm" id="pager"></ul>
    </nav>
</div>

<!-- Modal de Detalles del Producto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border border-light border-opacity-10 text-white shadow-lg" style="background: rgba(33, 37, 41, 0.95); backdrop-filter: blur(10px);">
            <div class="modal-header border-bottom border-light border-opacity-10">
                <h5 class="modal-title fw-bold text-primary" id="modalProductName"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="position-relative overflow-hidden rounded-4 shadow-sm">
                            <img id="modalProductImage" src="" class="img-fluid w-100 object-fit-cover" style="max-height: 400px; min-height: 300px;" alt="Producto">
                            <div class="position-absolute top-0 end-0 p-3">
                                <span id="modalProductStockBadge" class="badge bg-dark bg-opacity-75 backdrop-blur text-white border border-light border-opacity-25 rounded-pill px-3 py-2">
                                    <i class="bi bi-box-seam me-1"></i> Stock: <span id="modalProductStock"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h2 id="modalProductPrice" class="fw-bold text-success mb-0 display-6"></h2>
                                <small class="text-white-50">Precio unitario</small>
                            </div>
                        </div>
                        
                        <div class="bg-secondary bg-opacity-10 rounded-4 p-3 mb-4 flex-grow-1 border border-light border-opacity-10">
                            <h6 class="text-uppercase text-primary small fw-bold mb-2">Descripción</h6>
                            <p id="modalProductDescription" class="text-white-50 mb-0" style="white-space: pre-wrap; line-height: 1.6;"></p>
                        </div>
                        
                        <div class="mt-auto">
                            <button id="modalAddToCartBtn" class="btn btn-primary w-100 py-3 rounded-pill shadow-lg hover-scale fw-bold text-uppercase letter-spacing-1">
                                <i class="bi bi-cart-plus me-2 fs-5"></i>Agregar al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ROUTES = {
    productos: @json(parse_url(url('/api/productos'), PHP_URL_PATH)),
    carritoJson: @json(parse_url(route('tienda.carrito.json'), PHP_URL_PATH)),
    carritoAgregar: @json(parse_url(route('tienda.carrito.add'), PHP_URL_PATH)),
    login: @json(parse_url(route('login'), PHP_URL_PATH))
};

function getCookie(name){
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return '';
}
const token = ()=>{
    return document.querySelector('meta[name=csrf-token]')?.content || decodeURIComponent(getCookie('XSRF-TOKEN') || '');
};
const grid = document.getElementById('grid');
const pager = document.getElementById('pager');
const search = document.getElementById('search');
const IMG_PLACEHOLDER = "{{ asset('img/placeholder-producto.svg') }}";
let currentProducts = [];
let productModal = null;

document.addEventListener('DOMContentLoaded', () => {
    productModal = new bootstrap.Modal(document.getElementById('productModal'));
});

function showProductDetails(id) {
    const product = currentProducts.find(p => p.id_producto == id);
    if (!product) return;
    
    document.getElementById('modalProductName').textContent = product.nombre;
    
    const img = product.imagen_url ? product.imagen_url : IMG_PLACEHOLDER;
    const imgEl = document.getElementById('modalProductImage');
    imgEl.src = img;
    imgEl.onerror = function() { this.src = IMG_PLACEHOLDER; };
    
    const precio = product.precio_venta ? parseFloat(product.precio_venta).toFixed(2) : '0.00';
    document.getElementById('modalProductPrice').textContent = `{{ $configuracion['moneda'] ?? '$' }} ${precio}`;
    
    document.getElementById('modalProductDescription').textContent = product.descripcion || 'Sin descripción disponible.';
    document.getElementById('modalProductStock').textContent = product.stock ?? 0;
    
    // Configurar botón de agregar
    const btn = document.getElementById('modalAddToCartBtn');
    // Eliminar listeners anteriores clonando el botón
    const newBtn = btn.cloneNode(true);
    btn.parentNode.replaceChild(newBtn, btn);
    
    newBtn.onclick = () => {
        addToCart(product.id_producto, true); // Pass true to indicate it's from modal
    };
    
    productModal.show();
}


async function updateCount(){
    try{
        const res = await fetch(ROUTES.carritoJson, {
            credentials:'include',
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
        });
        const data = await res.json();
        document.getElementById('cart-count').textContent = data.count ?? 0;
    }catch(e){ document.getElementById('cart-count').textContent = 0; }
}

async function addToCart(id, fromModal = false){
    if(!id) {
        alert('Error: ID de producto no válido');
        return;
    }

    try {
        const res = await fetch(ROUTES.carritoAgregar, {
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-Requested-With':'XMLHttpRequest',
                'X-CSRF-TOKEN':token()
            },
            credentials:'include',
            body: JSON.stringify({id_producto:id})
        });

        if (res.status === 419) {
            try{
                const cookieNames = document.cookie.split(';').map(c => c.split('=')[0].trim()).filter(Boolean);
                console.error('CSRF 419', {
                    metaTokenLength: (document.querySelector('meta[name=csrf-token]')?.content || '').length,
                    cookieNames
                });
            }catch(_){}
            alert('Sesión expirada, recarga la página');
            window.location.reload();
            return;
        }
        if (res.status === 401) {
            alert('No estás autenticado. Inicia sesión de nuevo en esta misma URL.');
            window.location.href = ROUTES.login;
            return;
        }
        if (res.status === 403) {
            alert('No tienes permisos para agregar al carrito.');
            return;
        }

        const contentType = res.headers.get("content-type");
        let data = null;
        if (contentType && contentType.includes("application/json")) {
            data = await res.json();
        } else {
            const text = await res.text().catch(() => '');
            throw new Error(`Respuesta no válida del servidor (HTTP ${res.status}). ${text ? 'Revisa laravel.log' : ''}`.trim());
        }
        
        if(!res.ok || data.ok!==true){ 
            alert(data.error || 'No se pudo agregar el producto'); 
            return; 
        }
        
        document.getElementById('cart-count').textContent = data.count ?? 0;
        
        if(fromModal && productModal) {
            productModal.hide();
        }

        // Show success toast or feedback
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '1100';
        toast.innerHTML = `
            <div class="toast show align-items-center text-white bg-success border-0 shadow-lg rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle-fill me-2"></i>Producto agregado al carrito
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
        
    } catch(e) {
        console.error('Error addToCart:', e);
        alert('Error al agregar al carrito: ' + e.message);
    }
}

async function load(page=1,q=''){
    const url = new URL(ROUTES.productos, window.location.origin);
    url.searchParams.set('page', page);
    if(q) url.searchParams.set('search', q);
    
    try {
        const res = await fetch(url,{credentials:'include'});
        
        if (!res.ok) {
            throw new Error(`Error ${res.status}: ${res.statusText}`);
        }

        const data = await res.json();
        currentProducts = data.data; // Actualizar lista global de productos
        grid.innerHTML = '';
        
        if(!data.data || data.data.length === 0) {
            grid.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="opacity-50 mb-3">
                        <i class="bi bi-search display-1 text-muted"></i>
                    </div>
                    <h4 class="text-muted">No se encontraron productos</h4>
                    <p class="text-secondary">Intenta con otros términos de búsqueda</p>
                </div>
            `;
            pager.innerHTML = '';
            return;
        }
    
        data.data.forEach(p=>{
            const col = document.createElement('div');
            col.className='col-md-3 col-sm-6';
            const img = p.imagen_url ? p.imagen_url : IMG_PLACEHOLDER;
            const precio = p.precio_venta ? parseFloat(p.precio_venta).toFixed(2) : '0.00';
            
            col.innerHTML = `
                <div class="glass-card h-100 border-0 shadow-lg rounded-4 overflow-hidden transform-hover product-card d-flex flex-column" onclick="showProductDetails(${p.id_producto})" style="cursor: pointer;">
                    <div class="position-relative overflow-hidden group">
                        <img src="${img}" class="w-100 object-fit-cover" alt="${p.nombre}" 
                             style="height: 200px; transition: transform 0.3s ease;"
                             onerror="this.onerror=null;this.src='${IMG_PLACEHOLDER}';">
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-sm">
                                {{ $configuracion['moneda'] ?? '$' }} ${precio}
                            </span>
                        </div>
                    </div>
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <h5 class="fw-bold text-white mb-2 text-truncate" title="${p.nombre}">${p.nombre}</h5>
                        <p class="text-white-50 small flex-grow-1 mb-3 line-clamp-2">${p.descripcion || 'Sin descripción disponible'}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-light border-opacity-10">
                            <span class="text-white-50 small"><i class="bi bi-box-seam me-1"></i>Stock: ${p.stock ?? 0}</span>
                            <button onclick="event.stopPropagation(); addToCart(${p.id_producto})" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center hover-scale">
                                <i class="bi bi-cart-plus me-1"></i>Agregar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            grid.appendChild(col);
            
            // Add hover effect for image
            const card = col.querySelector('.product-card');
            const cardImg = col.querySelector('img');
            card.addEventListener('mouseenter', () => cardImg.style.transform = 'scale(1.05)');
            card.addEventListener('mouseleave', () => cardImg.style.transform = 'scale(1)');
        });
        pager.innerHTML = '';
        for(let i=1;i<=data.last_page;i++){
            const li=document.createElement('li');
            li.className='page-item'+(i===data.current_page?' active':'');
            const a=document.createElement('a');a.className='page-link';a.href='#';a.textContent=i;
            a.onclick=(e)=>{e.preventDefault();load(i,search.value.trim());};
            li.appendChild(a);pager.appendChild(li);
        }
    } catch (error) {
                console.error('Error loading products:', error);
                
                let errorDetails = error.message;
                // Try to see if we can get more info if it's a parsing error
                if (error.message.includes('JSON')) {
                     errorDetails += '<br><small class="text-white-50">Posible redirección o error de servidor (HTML recibido).</small>';
                }

                grid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <div class="text-danger mb-3">
                            <i class="bi bi-exclamation-triangle display-1"></i>
                        </div>
                        <h4 class="text-white">Error al cargar productos</h4>
                        <p class="text-white-50">Detalle: ${errorDetails}</p>
                        <button onclick="load()" class="btn btn-outline-light mt-2">Reintentar</button>
                    </div>
                `;
            }
}
search.addEventListener('input', ()=>load(1, search.value.trim()));
updateCount(); load();
</script>
@endsection
