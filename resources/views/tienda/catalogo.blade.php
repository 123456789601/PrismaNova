@extends('layouts.app')
@section('title','Tienda')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Catálogo</h4>
    <div class="d-flex gap-2">
        <input id="search" class="form-control" placeholder="Buscar producto">
        <a href="{{ route('tienda.carrito') }}" class="btn btn-primary">Carrito <span id="cart-count" class="badge text-bg-light ms-1">0</span></a>
    </div>
</div>
<div id="grid" class="row g-3"></div>
<nav class="mt-3">
    <ul class="pagination" id="pager"></ul>
 </nav>
<script>
const token = document.querySelector('meta[name=csrf-token]').content;
const grid = document.getElementById('grid');
const pager = document.getElementById('pager');
const search = document.getElementById('search');
const IMG_PLACEHOLDER = "{{ asset('img/placeholder-producto.svg') }}";
async function updateCount(){
    try{
        const res = await fetch('{{ route('tienda.carrito.json') }}', {credentials:'include'});
        const data = await res.json();
        document.getElementById('cart-count').textContent = data.count ?? 0;
    }catch(e){ document.getElementById('cart-count').textContent = 0; }
}
async function addToCart(p){
    const res = await fetch('{{ route('tienda.carrito.add') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token},
        credentials:'include',
        body: JSON.stringify({id_producto:p.id_producto})
    });
    const data = await res.json();
    if(!res.ok || data.ok!==true){ alert(data.error||'No se pudo agregar'); return; }
    document.getElementById('cart-count').textContent = data.count ?? 0;
    alert('Producto agregado al carrito');
}
async function load(page=1,q=''){
    const url = new URL('/api/productos', window.location.origin);
    url.searchParams.set('page', page);
    if(q) url.searchParams.set('search', q);
    const res = await fetch(url,{credentials:'include'});
    const data = await res.json();
    grid.innerHTML = '';
    data.data.forEach(p=>{
        const col = document.createElement('div');
        col.className='col-md-3';
        col.innerHTML = `
            <div class="card h-100 shadow-sm">
                <img class="card-img-top" style="width:100%;height:180px;object-fit:cover;background:#f0f2f5" src="${IMG_PLACEHOLDER}" onerror="this.src='${IMG_PLACEHOLDER}'">
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">${p.nombre}</h6>
                    <div class="mt-auto d-flex justify-content-between align-items-center">
                        <span class="fw-bold">$ ${Number(p.precio_venta).toFixed(2)}</span>
                        <button class="btn btn-sm btn-primary">Agregar</button>
                    </div>
                </div>
            </div>`;
        const imgEl = col.querySelector('img');
        if (p.imagen_url) {
            // set after append to avoid flash of empty box on slow networks
            imgEl.src = p.imagen_url;
        }
        col.querySelector('button').onclick = ()=> addToCart(p);
        grid.appendChild(col);
    });
    pager.innerHTML = '';
    for(let i=1;i<=data.last_page;i++){
        const li=document.createElement('li');
        li.className='page-item'+(i===data.current_page?' active':'');
        const a=document.createElement('a');a.className='page-link';a.href='#';a.textContent=i;
        a.onclick=(e)=>{e.preventDefault();load(i,search.value.trim());};
        li.appendChild(a);pager.appendChild(li);
    }
}
search.addEventListener('input', ()=>load(1, search.value.trim()));
updateCount(); load();
</script>
@endsection
