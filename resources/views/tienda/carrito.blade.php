@extends('layouts.app')
@section('title','Carrito')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-cart3 me-2"></i>Carrito de Compras</h4>
            <p class="text-secondary small mb-0">Revisa tus productos antes de finalizar</p>
        </div>
        <a href="{{ route('tienda.catalogo') }}" class="btn btn-outline-light rounded-pill px-3 shadow-sm hover-scale">
            <i class="bi bi-arrow-left me-1"></i>Seguir comprando
        </a>
    </div>

    <div id="cart-empty" class="alert alert-info d-none border-0 rounded-4 overflow-hidden mb-4 shadow-sm bg-info bg-opacity-10 text-white">
        <div class="d-flex justify-content-between align-items-center p-2">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-white bg-opacity-10 p-3 me-3 text-info border border-info border-opacity-25">
                    <i class="bi bi-cart-x fs-3"></i>
                </div>
                <div>
                    <strong class="fs-5 d-block text-info">Tu carrito está vacío</strong>
                    <span class="opacity-75 text-info">Explora el catálogo y agrega productos para empezar tu compra.</span>
                </div>
            </div>
            <div>
                <a href="{{ route('tienda.catalogo') }}" class="btn btn-info text-white fw-bold rounded-pill px-4 shadow-sm transform-hover">Ir al catálogo</a>
            </div>
        </div>
    </div>

    <div class="glass-card overflow-hidden" id="cart-container">
        <div class="card-body p-4">
            <div class="table-responsive rounded-4 shadow-sm border border-light border-opacity-10 overflow-hidden">
                <table class="table align-middle mb-0 text-white" id="cart-table">
                    <thead class="bg-primary bg-opacity-10 text-white fw-bold text-uppercase small">
                        <tr>
                            <th class="ps-4 py-3 border-bottom border-light border-opacity-10">Producto</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Precio</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Cantidad</th>
                            <th class="py-3 border-bottom border-light border-opacity-10">Subtotal</th>
                            <th class="text-end pe-4 py-3 border-bottom border-light border-opacity-10"></th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0"></tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-secondary bg-opacity-10 border-top border-light border-opacity-10 p-4">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3 p-3 bg-secondary bg-opacity-10 rounded-4 border border-light border-opacity-10 shadow-sm">
                        <label class="form-label mb-0 fw-bold text-white-50 text-uppercase small"><i class="bi bi-credit-card me-2"></i>Método de pago:</label>
                        <select id="metodo" class="form-select rounded-pill border-0 shadow-sm bg-secondary bg-opacity-25 text-white" style="width:200px">
                            <option value="efectivo" class="text-dark">Efectivo</option>
                            <option value="tarjeta" class="text-dark">Tarjeta</option>
                            <option value="transferencia" class="text-dark">Transferencia</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-flex align-items-center justify-content-md-end gap-3">
                        <div class="fs-4 fw-bold text-primary">Total: {{ $configuracion['moneda'] ?? '$' }} <span id="total">0.00</span></div>
                        <button id="checkout" class="btn btn-primary rounded-pill px-4 shadow-sm btn-lg hover-scale">
                            <i class="bi bi-bag-check-fill me-1"></i>Finalizar compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const PLACEHOLDER_IMG = "{{ asset('img/placeholder-producto.svg') }}";
const token = document.querySelector('meta[name=csrf-token]').content;
let currentItems = [];

async function loadCart(){
    const tbody = document.querySelector('#cart-table tbody');
    tbody.innerHTML = '';
    
    try {
        const res = await fetch('{{ route('tienda.carrito.json') }}',{credentials:'include'});
        const data = await res.json();
        currentItems = data.items || [];
        
        const emptyAlert = document.getElementById('cart-empty');
        const cartContainer = document.getElementById('cart-container');
        
        if(!currentItems.length){
            emptyAlert.classList.remove('d-none');
            cartContainer.classList.add('d-none');
        }else{
            emptyAlert.classList.add('d-none');
            cartContainer.classList.remove('d-none');
        }
        
        (currentItems).forEach((i)=>{
            const tr=document.createElement('tr');
            tr.className = 'hover-bg-white-10 transition-all';
            tr.innerHTML = `
                <td class="ps-4 border-bottom border-light border-opacity-10">
                    <div class="d-flex align-items-center gap-3">
                        <img src="${i.imagen ? '{{ asset('storage') }}/'+i.imagen : PLACEHOLDER_IMG}" alt="" style="height:48px;width:48px;object-fit:cover" class="rounded-3 shadow-sm" onerror="this.src='${PLACEHOLDER_IMG}'">
                        <span class="fw-bold text-white">${i.nombre}</span>
                    </div>
                </td>
                <td class="fw-bold text-white-50 border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} ${Number(i.precio).toFixed(2)}</td>
                <td class="border-bottom border-light border-opacity-10">
                    <div class="input-group input-group-sm rounded-pill overflow-hidden bg-secondary bg-opacity-25 border border-light border-opacity-10" style="width:140px">
                        <button class="btn btn-link text-white-50 border-0 minus hover-scale" type="button"><i class="bi bi-dash"></i></button>
                        <input type="number" min="1" value="${i.cantidad}" class="form-control text-center border-0 qty fw-bold bg-transparent text-white">
                        <button class="btn btn-link text-white-50 border-0 plus hover-scale" type="button"><i class="bi bi-plus"></i></button>
                    </div>
                </td>
                <td class="fw-bold text-primary border-bottom border-light border-opacity-10">{{ $configuracion['moneda'] ?? '$' }} ${Number(i.subtotal || (i.precio*i.cantidad)).toFixed(2)}</td>
                <td class="text-end pe-4 border-bottom border-light border-opacity-10">
                    <button class="btn btn-sm btn-outline-danger rounded-pill shadow-sm border-0 hover-scale remove-item" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            
            // Event Listeners
            tr.querySelector('.qty').onchange = async (e)=>{
                const cant = Math.max(1, parseInt(e.target.value||1));
                await updateItem(i.id_producto, cant);
            };
            tr.querySelector('.minus').onclick = async ()=>{
                const cant = Math.max(1, (i.cantidad||1)-1);
                await updateItem(i.id_producto, cant);
            };
            tr.querySelector('.plus').onclick = async ()=>{
                const cant = (i.cantidad||1)+1;
                await updateItem(i.id_producto, cant);
            };
            tr.querySelector('.remove-item').onclick = async ()=>{
                if(confirm('¿Eliminar este producto del carrito?')) {
                    await removeItem(i.id_producto);
                }
            };
            
            tbody.appendChild(tr);
        });
        document.getElementById('total').textContent = Number(data.total||0).toFixed(2);
    } catch(e) {
        console.error("Error loading cart:", e);
    }
}

async function updateItem(id, quantity) {
    await fetch('{{ route('tienda.carrito.update') }}', {
        method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, credentials:'include',
        body: JSON.stringify({id_producto:id, cantidad:quantity})
    });
    await loadCart();
}

async function removeItem(id) {
    await fetch('{{ url('tienda/carrito/item') }}/'+id, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':token}, credentials:'include'
    });
    await loadCart();
}

async function checkout(){
    if(!currentItems.length){ alert('El carrito está vacío'); return; }
    
    // Add loading state
    const btn = document.getElementById('checkout');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
    
    try {
        const body = {
            metodo_pago: document.getElementById('metodo').value,
            items: currentItems.map(i=>({id_producto:i.id_producto,cantidad:i.cantidad}))
        };
        const res = await fetch('/api/ventas',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},
            credentials:'include',
            body: JSON.stringify(body)
        });
        const data = await res.json();
        
        if(!res.ok){ 
            throw new Error(data.error || 'No se pudo completar la compra'); 
        }
        
        Swal.fire({
            icon: 'success',
            title: '¡Compra Exitosa!',
            text: 'Tu pedido ha sido registrado correctamente.',
            confirmButtonColor: '#4f46e5'
        }).then(() => {
            window.location.href = "{{ route('mis-compras.index') }}";
        });
        
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message,
            confirmButtonColor: '#ef4444'
        });
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

document.getElementById('checkout').onclick=checkout;
loadCart();
</script>
@endsection
