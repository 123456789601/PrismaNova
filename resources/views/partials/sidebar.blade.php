<div class="list-group list-group-flush">
    @if(Route::has('dashboard'))
        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
    @endif
    @php($rol = auth()->user()->rol ?? null)
    @if($rol === 'admin' && Route::has('usuarios.index'))
        <a href="{{ route('usuarios.index') }}" class="list-group-item list-group-item-action">Usuarios</a>
    @endif
    @if(in_array($rol, ['admin','cajero']) && Route::has('clientes.index'))
        <a href="{{ route('clientes.index') }}" class="list-group-item list-group-item-action">Clientes</a>
    @endif
    @if(in_array($rol, ['admin','bodeguero']) && Route::has('proveedores.index'))
        <a href="{{ route('proveedores.index') }}" class="list-group-item list-group-item-action">Proveedores</a>
    @endif
    @if(in_array($rol, ['admin','bodeguero']) && Route::has('categorias.index'))
        <a href="{{ route('categorias.index') }}" class="list-group-item list-group-item-action">Categorías</a>
    @endif
    @if(in_array($rol, ['admin','bodeguero']) && Route::has('productos.index'))
        <a href="{{ route('productos.index') }}" class="list-group-item list-group-item-action">Productos</a>
    @endif
    @if(in_array($rol, ['admin','bodeguero']) && Route::has('compras.index'))
        <a href="{{ route('compras.index') }}" class="list-group-item list-group-item-action">Compras</a>
    @endif
    @if(in_array($rol, ['admin','cajero']) && Route::has('ventas.index'))
        <a href="{{ route('ventas.index') }}" class="list-group-item list-group-item-action">Ventas</a>
    @endif
    @if(in_array($rol, ['admin','cajero']) && Route::has('caja.index'))
        <a href="{{ route('caja.index') }}" class="list-group-item list-group-item-action">Caja</a>
    @endif
    @if($rol === 'admin' && Route::has('reportes.index'))
        <a href="{{ route('reportes.index') }}" class="list-group-item list-group-item-action">Reportes</a>
        @if(Route::has('reportes.sync'))
            <a href="{{ route('reportes.sync') }}" class="list-group-item list-group-item-action">Sync de Inventario</a>
        @endif
    @endif
    @if(Route::has('perfil'))
        <a href="{{ route('perfil') }}" class="list-group-item list-group-item-action">Mi Perfil</a>
    @endif
    @if(($rol === 'cliente') && Route::has('mis-compras.index'))
        <a href="{{ route('mis-compras.index') }}" class="list-group-item list-group-item-action">Mis Compras</a>
    @endif
    @if(($rol === 'cliente') && Route::has('tienda.catalogo'))
        <a href="{{ route('tienda.catalogo') }}" class="list-group-item list-group-item-action">Tienda</a>
    @endif
    @if(($rol === 'cliente') && Route::has('tienda.carrito'))
        <a href="{{ route('tienda.carrito') }}" class="list-group-item list-group-item-action">Carrito</a>
    @endif
</div>
