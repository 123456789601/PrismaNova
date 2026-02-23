@extends('layouts.app')
@section('title','Carrito')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Carrito</h4>
    <a href="{{ route('tienda.catalogo') }}" class="btn btn-secondary">Seguir comprando</a>
 </div>
<div class="table-responsive">
    <table class="table" id="cart-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
<div class="d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
        <label class="form-label mb-0">Método de pago</label>
        <select id="metodo" class="form-select" style="width:auto">
            <option value="efectivo">Efectivo</option>
            <option value="tarjeta">Tarjeta</option>
            <option value="transferencia">Transferencia</option>
        </select>
    </div>
    <div>
        <span class="me-3 fw-bold">Total: $ <span id="total">0.00</span></span>
        <button id="checkout" class="btn btn-primary">Finalizar compra</button>
    </div>
</div>
<script>
const PLACEHOLDER_IMG = "{{ asset('img/placeholder-producto.svg') }}";
const token = document.querySelector('meta[name=csrf-token]').content;
let currentItems = [];
async function loadCart(){
    const tbody = document.querySelector('#cart-table tbody');
    tbody.innerHTML = '';
    const res = await fetch('{{ route('tienda.carrito.json') }}',{credentials:'include'});
    const data = await res.json();
    currentItems = data.items || [];
    (currentItems).forEach((i)=>{
        const tr=document.createElement('tr');
        tr.innerHTML = `
            <td class="d-flex align-items-center gap-2">
                <img src="${i.imagen||PLACEHOLDER_IMG}" alt="" style="height:40px;width:40px;object-fit:cover" onerror="this.src='${PLACEHOLDER_IMG}'">
                ${i.nombre}
            </td>
            <td>$ ${Number(i.precio).toFixed(2)}</td>
            <td>
                <div class="input-group input-group-sm" style="width:140px">
                    <button class="btn btn-outline-secondary minus" type="button">−</button>
                    <input type="number" min="1" value="${i.cantidad}" class="form-control text-center qty">
                    <button class="btn btn-outline-secondary plus" type="button">+</button>
                </div>
            </td>
            <td>$ ${Number(i.subtotal || (i.precio*i.cantidad)).toFixed(2)}</td>
            <td><button class="btn btn-sm btn-danger">Quitar</button></td>
        `;
        tr.querySelector('.qty').onchange = async (e)=>{
            const cant = Math.max(1, parseInt(e.target.value||1));
            await fetch('{{ route('tienda.carrito.update') }}', {
                method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, credentials:'include',
                body: JSON.stringify({id_producto:i.id_producto, cantidad:cant})
            });
            await loadCart();
        };
        tr.querySelector('.minus').onclick = async ()=>{
            const cant = Math.max(1, (i.cantidad||1)-1);
            await fetch('{{ route('tienda.carrito.update') }}', {
                method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, credentials:'include',
                body: JSON.stringify({id_producto:i.id_producto, cantidad:cant})
            });
            await loadCart();
        };
        tr.querySelector('.plus').onclick = async ()=>{
            const cant = (i.cantidad||1)+1;
            await fetch('{{ route('tienda.carrito.update') }}', {
                method:'PATCH', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token}, credentials:'include',
                body: JSON.stringify({id_producto:i.id_producto, cantidad:cant})
            });
            await loadCart();
        };
        tr.querySelector('button.btn-danger').onclick = async ()=>{
            await fetch('{{ url('tienda/carrito/item') }}/'+i.id_producto, {
                method:'DELETE', headers:{'X-CSRF-TOKEN':token}, credentials:'include'
            });
            await loadCart();
        };
        tbody.appendChild(tr);
    });
    document.getElementById('total').textContent = Number(data.total||0).toFixed(2);
}
async function checkout(){
    if(!currentItems.length){ alert('El carrito está vacío'); return; }
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
    if(!res.ok){ alert(data.error||'No se pudo completar la compra'); return; }
    alert('Compra realizada correctamente');
    window.location.href = "{{ route('mis-compras.index') }}";
}
document.getElementById('checkout').onclick=checkout;
loadCart();
</script>
@endsection
