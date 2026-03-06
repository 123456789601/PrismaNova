@extends('layouts.app')

@section('title', 'Punto de Venta (POS)')

@section('content')
<div class="container-fluid h-100">
    <div class="row h-100">
        <!-- Left Panel: Products -->
        <div class="col-md-8 col-lg-8 p-0 border-end border-light border-opacity-10">
            <div class="d-flex flex-column h-100">
                <!-- Toolbar -->
                <div class="p-2 bg-dark border-bottom border-light border-opacity-10 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-light" onclick="openRecentSales()" title="Ventas Recientes">
                        <i class="bi bi-clock-history me-1"></i> Recientes
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="holdSale()" title="Poner en Espera">
                        <i class="bi bi-pause-circle me-1"></i> Espera
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="resumeSale()" title="Recuperar Venta">
                        <i class="bi bi-play-circle me-1"></i> Recuperar
                    </button>
                    <div class="ms-auto">
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="openMovements()" title="Registrar Movimiento">
                            <i class="bi bi-arrow-left-right me-1"></i> Movimientos
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="openCashRegister()" title="Estado de Caja">
                            <i class="bi bi-cash-stack me-1"></i> Caja
                        </button>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="p-3 bg-secondary bg-opacity-10 border-bottom border-light border-opacity-10">
                    <div class="row g-2">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-primary text-primary"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchProduct" class="form-control border-primary bg-transparent text-white" placeholder="Buscar producto por nombre o código..." autocomplete="off" autofocus>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select id="categoryFilter" class="form-select bg-transparent text-white border-secondary">
                                <option value="all">Todas las Categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-grow-1 overflow-auto p-3" style="height: 0px; min-height: 500px;">
                    <div class="row g-3" id="productGrid">
                        @foreach($productos as $p)
                            <div class="col-6 col-md-4 col-lg-3 product-item" data-name="{{ strtolower($p->nombre) }}" data-category="{{ $p->id_categoria }}">
                                <div class="card h-100 glass-card product-card cursor-pointer hover-scale" onclick="addToCart({{ json_encode($p) }})">
                                    <div class="card-body p-2 text-center">
                                        <div class="mb-2" style="height: 100px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                            @if($p->imagen)
                                                <img src="{{ asset('storage/' . $p->imagen) }}" alt="{{ $p->nombre }}" class="img-fluid rounded" style="max-height: 100%;">
                                            @else
                                                <i class="bi bi-box-seam display-4 text-white-50"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title text-white small mb-1 text-truncate" title="{{ $p->nombre }}">{{ $p->nombre }}</h6>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="badge bg-primary rounded-pill">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($p->precio_venta, 2) }}</span>
                                            <small class="text-white-50 stock-badge" data-stock="{{ $p->stock }}">Stock: {{ $p->stock }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div id="noResults" class="text-center py-5 d-none">
                        <i class="bi bi-search display-1 text-white-50 mb-3"></i>
                        <h4 class="text-white-50">No se encontraron productos</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Cart -->
        <div class="col-md-4 col-lg-4 p-0 bg-secondary bg-opacity-25 d-flex flex-column h-100 border-start border-light border-opacity-10">
            <!-- Header -->
            <div class="p-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Carrito de Compras</h5>
                <span class="badge bg-white text-primary rounded-pill" id="cartCount">0 items</span>
            </div>

            <!-- Client Selection -->
            <div class="p-3 border-bottom border-light border-opacity-10">
                <label class="small text-white-50 mb-1">Cliente</label>
                <div class="input-group">
                    <select class="form-select bg-transparent text-white border-secondary" id="clientSelect">
                        @foreach($clientes as $c)
                            <option value="{{ $c->id_cliente }}">{{ $c->nombre }} {{ $c->apellido }} ({{ $c->documento }})</option>
                        @endforeach
                    </select>
                            <button class="btn btn-outline-light" type="button" title="Nuevo Cliente" data-bs-toggle="modal" data-bs-target="#newClientModal"><i class="bi bi-person-plus"></i></button>
                        </div>
                    </div>

            <!-- Cart Items -->
            <div class="flex-grow-1 overflow-auto p-0" style="height: 0px;">
                <table class="table table-hover table-borderless text-white mb-0 align-middle">
                    <tbody id="cartItems">
                        <!-- Items injected by JS -->
                    </tbody>
                </table>
                <div id="emptyCart" class="text-center py-5">
                    <i class="bi bi-basket display-1 text-white-50 mb-3"></i>
                    <p class="text-white-50">El carrito está vacío</p>
                </div>
            </div>

            <!-- Summary -->
            <div class="p-3 bg-dark bg-opacity-50 border-top border-light border-opacity-10">
                <div class="d-flex justify-content-between mb-1 text-white-50 small">
                    <span>Subtotal:</span>
                    <span>{{ $configuracion['moneda'] ?? '$' }} <span id="summarySubtotal">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-white-50 small align-items-center">
                    <span>Descuento: <button class="btn btn-sm btn-link text-white-50 p-0 ms-1 text-decoration-none" onclick="setDiscount()" title="Aplicar descuento manual"><i class="bi bi-pencil-square"></i></button></span>
                    <span class="text-success">- {{ $configuracion['moneda'] ?? '$' }} <span id="summaryDiscount">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between mb-1 text-white-50 small">
                    <span>Impuesto (18%):</span>
                    <span>{{ $configuracion['moneda'] ?? '$' }} <span id="summaryTax">0.00</span></span>
                </div>
                <div class="d-flex justify-content-between mb-3 pt-2 border-top border-light border-opacity-25">
                    <span class="fs-4 fw-bold text-white">Total:</span>
                    <span class="fs-4 fw-bold text-primary">{{ $configuracion['moneda'] ?? '$' }} <span id="summaryTotal">0.00</span></span>
                </div>
                <button class="btn btn-success w-100 py-3 fw-bold shadow-lg hover-scale" id="btnCheckout" onclick="openCheckoutModal()" disabled>
                    <i class="bi bi-cash-coin me-2"></i>COBRAR
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom border-light border-opacity-10">
                <h5 class="modal-title text-white"><i class="bi bi-wallet2 me-2"></i>Finalizar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="posForm" method="POST" action="{{ route('ventas.store') }}">
                    @csrf
                    <input type="hidden" name="id_cliente" id="formClienteId">
                    <input type="hidden" name="fecha" value="{{ now()->format('Y-m-d\\TH:i') }}">
                    <input type="hidden" name="impuesto" id="formImpuesto">
                    <!-- Products JSON will be appended here -->
                    
                    <div class="text-center mb-4">
                        <h6 class="text-white-50 uppercase tracking-wider">Total a Pagar</h6>
                        <h1 class="text-primary fw-bold display-4">{{ $configuracion['moneda'] ?? '$' }} <span id="modalTotal">0.00</span></h1>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-white fw-bold">Método de Pago</label>
                        <div class="row g-2">
                            @foreach($metodos as $m)
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="metodo_pago_id" id="mp_{{ $m->id_metodo_pago }}" value="{{ $m->id_metodo_pago }}" {{ $loop->first ? 'checked' : '' }} onchange="togglePaymentFields(this)">
                                    <label class="btn btn-outline-light w-100 h-100 d-flex align-items-center justify-content-center gap-2" for="mp_{{ $m->id_metodo_pago }}" data-nombre="{{ $m->nombre }}">
                                        @if(strtolower($m->nombre) == 'efectivo') <i class="bi bi-cash"></i>
                                        @elseif(strtolower($m->nombre) == 'tarjeta') <i class="bi bi-credit-card"></i>
                                        @else <i class="bi bi-wallet"></i> @endif
                                        {{ $m->nombre }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cash Fields -->
                    <div id="cashFields" class="payment-section">
                        <div class="mb-3">
                            <label class="form-label text-white">Monto Recibido</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary bg-opacity-25 border-0 text-white">{{ $configuracion['moneda'] ?? '$' }} </span>
                                <input type="number" step="0.01" class="form-control bg-secondary bg-opacity-25 border-0 text-white fs-5" name="monto_recibido" id="montoRecibido" oninput="calculateChange()">
                            </div>
                        </div>
                        <div class="alert alert-success d-flex justify-content-between align-items-center mb-0">
                            <span class="fw-bold">Cambio / Vuelto:</span>
                            <span class="fs-4 fw-bold">{{ $configuracion['moneda'] ?? '$' }} <span id="cambioDisplay">0.00</span></span>
                        </div>
                        <input type="hidden" name="metodo_pago" value="Efectivo" id="metodoPagoNombre">
                    </div>

                    <!-- Card Fields -->
                    <div id="cardFields" class="payment-section d-none">
                        <div class="mb-3">
                            <label class="form-label text-white">Referencia / Nro. Operación</label>
                            <input type="text" class="form-control bg-secondary bg-opacity-25 border-0 text-white" name="referencia_pago" placeholder="Ej: 00123456">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-white">Últimos 4 dígitos</label>
                            <input type="text" class="form-control bg-secondary bg-opacity-25 border-0 text-white" name="ultimos_digitos" maxlength="4" placeholder="Ej: 4242">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-lg" id="btnConfirmPay">
                            <i class="bi bi-check-circle me-2"></i>CONFIRMAR VENTA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .product-card { transition: all 0.2s; }
    .product-card:active { transform: scale(0.95); }
    .hover-scale:hover { transform: translateY(-2px); }
    .cursor-pointer { cursor: pointer; }
    /* Hide scrollbar for clean look but allow scroll */
    .overflow-auto::-webkit-scrollbar { width: 6px; }
    .overflow-auto::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
    .overflow-auto::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
</style>

<!-- Sale Success Modal -->
<div class="modal fade" id="saleSuccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-success border-2">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success display-1"></i>
                </div>
                <h2 class="fw-bold mb-3">¡Venta Exitosa!</h2>
                
                <div class="card bg-secondary bg-opacity-10 border-light border-opacity-10 mb-4">
                    <div class="card-body">
                        <h5 class="text-white-50 mb-2">Su cambio / vuelto es:</h5>
                        <h1 class="text-warning fw-bold display-3">{{ $configuracion['moneda'] ?? '$' }} <span id="successChange">0.00</span></h1>
                    </div>
                </div>

                <div class="d-grid gap-3">
                    <button class="btn btn-primary btn-lg" onclick="printLastTicket()">
                        <i class="bi bi-printer me-2"></i>Imprimir Ticket
                    </button>
                    <button class="btn btn-outline-light" onclick="closeSuccessModal()">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Nueva Venta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Client Modal -->
<div class="modal fade" id="newClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newClientForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="nombre" class="form-control bg-secondary bg-opacity-10 text-white border-secondary" required pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios" oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellido *</label>
                        <input type="text" name="apellido" class="form-control bg-secondary bg-opacity-10 text-white border-secondary" required pattern="[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+" title="Solo letras y espacios" oninput="this.value = this.value.replace(/[^a-zA-ZñÑáéíóúÁÉÍÓÚ\s]/g, '')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Documento *</label>
                        <input type="text" name="documento" class="form-control bg-secondary bg-opacity-10 text-white border-secondary" required pattern="\d+" title="Solo números" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control bg-secondary bg-opacity-10 text-white border-secondary">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control bg-secondary bg-opacity-10 text-white border-secondary">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control bg-secondary bg-opacity-10 text-white border-secondary">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveNewClient()">Guardar Cliente</button>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales Modal -->
<div class="modal fade" id="recentSalesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Ventas Recientes</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hora</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="recentSalesTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Suspended Sales Modal -->
<div class="modal fade" id="suspendedSalesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-pause-circle me-2"></i>Ventas en Espera</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nota</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="suspendedSalesTable"></tbody>
                </table>
                <div id="noSuspended" class="text-center py-4 d-none">
                    <p class="text-white-50">No hay ventas en espera</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cash Register Modal -->
<div class="modal fade" id="cashRegisterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Estado de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="cashRegisterBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <form id="closeRegisterForm" action="" method="POST" style="display:none;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Cerrar Caja</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Movements Modal -->
<div class="modal fade" id="movementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right me-2"></i>Registrar Movimiento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="movementForm">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Movimiento</label>
                        <select name="tipo" class="form-select bg-secondary bg-opacity-10 text-white border-secondary" required>
                            <option value="ingreso">Ingreso (Entrada de dinero)</option>
                            <option value="egreso">Egreso (Salida de dinero)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto</label>
                        <div class="input-group">
                            <span class="input-group-text bg-secondary bg-opacity-10 text-white border-secondary">{{ $configuracion['moneda'] ?? '$' }} </span>
                            <input type="number" step="0.01" name="monto" class="form-control bg-secondary bg-opacity-10 text-white border-secondary" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción / Motivo</label>
                        <textarea name="descripcion" class="form-control bg-secondary bg-opacity-10 text-white border-secondary" rows="3" required placeholder="Ej: Pago a proveedor, Cambio inicial, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveMovement()">Guardar Movimiento</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];
    let manualDiscount = 0; // Effective discount applied
    let fixedDiscountInput = 0; // Stored fixed discount
    let discountPercentage = 0;
    let discountType = 'fixed';
    let currentCajaId = null;
    const taxRate = 0.18; // 18% IGV (Peru standard, adjust as needed)

    // Toolbar Functions
    function setDiscount() {
        const input = prompt('Ingrese descuento (Ej: "10" para monto fijo, "10%" para porcentaje):');
        if (input !== null) {
            if (input.trim().endsWith('%')) {
                const pct = parseFloat(input.replace('%', ''));
                if (!isNaN(pct) && pct >= 0 && pct <= 100) {
                    discountType = 'percentage';
                    discountPercentage = pct;
                } else {
                    alert('Porcentaje inválido');
                    return;
                }
            } else {
                const val = parseFloat(input);
                if (!isNaN(val) && val >= 0) {
                    discountType = 'fixed';
                    fixedDiscountInput = val;
                    discountPercentage = 0;
                } else {
                    alert('Monto inválido');
                    return;
                }
            }
            updateCartUI();
        }
    }

    function openMovements() {
        // Check if caja is open first
        fetch("{{ route('caja.estado') }}")
            .then(res => res.json())
            .then(data => {
                if (!data.abierta) {
                    alert('Debe abrir una caja antes de registrar movimientos.');
                    return;
                }
                currentCajaId = data.id_caja;
                const modal = new bootstrap.Modal(document.getElementById('movementsModal'));
                modal.show();
            })
            .catch(err => alert('Error al verificar estado de caja'));
    }

    function saveMovement() {
        if (!currentCajaId) return;

        const form = document.getElementById('movementForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch(`/caja/${currentCajaId}/movimiento`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                alert('Movimiento registrado correctamente');
                const modal = bootstrap.Modal.getInstance(document.getElementById('movementsModal'));
                modal.hide();
                form.reset();
            } else {
                alert('Error: ' + resp.message);
            }
        })
        .catch(err => alert('Error de conexión'));
    }

    function holdSale() {
        if (cart.length === 0) {
            alert('El carrito está vacío');
            return;
        }
        
        const nota = prompt('Ingrese una nota para identificar esta venta (opcional):', 'Cliente en espera');
        if (nota === null) return; // Cancelled

        const idCliente = document.getElementById('clientSelect').value;
        const total = parseFloat(document.getElementById('summaryTotal').innerText);

        fetch("{{ route('ventas.suspendidas.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                contenido: cart,
                total: total,
                id_cliente: idCliente,
                nota: nota
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cart = [];
                updateCartUI();
                alert('Venta puesta en espera correctamente');
            } else {
                alert('Error al suspender venta: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(err => alert('Error de conexión'));
    }

    function resumeSale() {
        const modal = new bootstrap.Modal(document.getElementById('suspendedSalesModal'));
        modal.show();
        
        fetch("{{ route('ventas.suspendidas.index') }}")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('suspendedSalesTable');
                const noSuspended = document.getElementById('noSuspended');
                tbody.innerHTML = '';
                
                if (data.length === 0) {
                    noSuspended.classList.remove('d-none');
                } else {
                    noSuspended.classList.add('d-none');
                    data.forEach(v => {
                        const date = new Date(v.created_at);
                        const fecha = date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        const cliente = v.cliente ? `${v.cliente.nombre} ${v.cliente.apellido}` : 'Público General';
                        const nota = v.nota || '-';
                        
                        tbody.innerHTML += `
                            <tr>
                                <td>${nota}</td>
                                <td>${fecha}</td>
                                <td>${cliente}</td>
                                <td>{{ $configuracion['moneda'] ?? '$' }} ${parseFloat(v.total).toFixed(2)}</td>
                                <td>
                                    <button onclick="loadSuspendedSale(${v.id_venta_suspendida})" class="btn btn-sm btn-primary" title="Cargar"><i class="bi bi-box-arrow-in-down"></i></button>
                                    <button onclick="deleteSuspendedSale(${v.id_venta_suspendida})" class="btn btn-sm btn-danger ms-1" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
    }

    function loadSuspendedSale(id) {
        if (cart.length > 0) {
            if (!confirm('El carrito actual no está vacío. ¿Desea sobrescribirlo con la venta suspendida?')) {
                return;
            }
        }

        // We could fetch the specific sale, but for now we can just reload the list or store data in the row.
        // Better to fetch or filter from the data we just got. 
        // But since we are in a new function scope, let's fetch or just rely on the server to handle the retrieval/deletion logic.
        // Actually, we need the content. The index returns the content.
        
        // Let's refactor resumeSale to store data in a global var or just fetch again.
        // Simplest: fetch the list again filtering by ID, or just trust the user to click the button which calls this function.
        // Wait, I need the content. The `index` method returns `contenido`.
        
        fetch("{{ route('ventas.suspendidas.index') }}")
            .then(res => res.json())
            .then(ventas => {
                const venta = ventas.find(v => v.id_venta_suspendida == id);
                if (venta) {
                    cart = venta.contenido;
                    document.getElementById('clientSelect').value = venta.id_cliente || document.querySelector('#clientSelect option:first-child').value;
                    updateCartUI();
                    
                    // Delete from suspended list
                    fetch(`/ventas/suspendidas/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('suspendedSalesModal'));
                        modal.hide();
                    });
                }
            });
    }
    
    function deleteSuspendedSale(id) {
        if (!confirm('¿Eliminar esta venta suspendida permanentemente?')) return;
        
        fetch(`/ventas/suspendidas/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Refresh list
                resumeSale(); // This re-opens modal and fetches list, might be jarring.
                // Better: remove row.
                // For simplicity, just close and re-open or let the user click resume again.
                // Let's just refresh the table.
                const modal = bootstrap.Modal.getInstance(document.getElementById('suspendedSalesModal'));
                // Actually resumeSale() shows the modal. If it's already shown, it just refreshes content.
                resumeSale(); 
            }
        });
    }

    function openRecentSales() {
        const modal = new bootstrap.Modal(document.getElementById('recentSalesModal'));
        modal.show();
        
        fetch("{{ route('ventas.recent') }}")
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('recentSalesTable');
                tbody.innerHTML = '';
                data.forEach(v => {
                    const date = new Date(v.fecha);
                    const time = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    const cliente = v.cliente ? `${v.cliente.nombre} ${v.cliente.apellido}` : 'Público General';
                    
                    let estadoBadge = '';
                    if (v.estado === 'completada') {
                        estadoBadge = '<span class="badge bg-success">Completada</span>';
                    } else if (v.estado === 'anulada') {
                        estadoBadge = '<span class="badge bg-danger">Anulada</span>';
                    }

                    let actions = `
                        <button onclick="window.open('/ventas/${v.id_venta}', '_blank')" class="btn btn-sm btn-outline-primary" title="Ver Detalles"><i class="bi bi-eye"></i></button>
                        <a href="/ventas/${v.id_venta}/ticket" target="_blank" class="btn btn-sm btn-outline-light" title="Ticket"><i class="bi bi-printer"></i></a>
                        <a href="/ventas/${v.id_venta}/factura" target="_blank" class="btn btn-sm btn-outline-info" title="Factura"><i class="bi bi-file-text"></i></a>
                    `;
                    
                    if (v.estado === 'completada') {
                        actions += `
                            <button onclick="anularVenta(${v.id_venta}, '${parseFloat(v.total).toFixed(2)}')" class="btn btn-sm btn-outline-danger ms-1" title="Anular/Devolución"><i class="bi bi-x-circle"></i></button>
                        `;
                    }

                    tbody.innerHTML += `
                        <tr>
                            <td>${v.id_venta}</td>
                            <td>${time}</td>
                            <td>${cliente}</td>
                            <td>{{ $configuracion['moneda'] ?? '$' }} ${parseFloat(v.total).toFixed(2)}</td>
                            <td>${estadoBadge}</td>
                            <td>${actions}</td>
                        </tr>
                    `;
                });
            });
    }

    function anularVenta(id, total) {
        if (!confirm(`¿Está seguro de anular esta venta por {{ $configuracion['moneda'] ?? '$' }} ${total}? \n\nEsto revertirá el stock de los productos. \n\nIMPORTANTE: Si la venta fue en EFECTIVO, el sistema descontará este monto del saldo esperado en caja. Asegúrese de devolver el dinero al cliente.`)) return;
        
        fetch(`/ventas/${id}/anular`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Venta anulada correctamente. Stock restaurado.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('recentSalesModal'));
                modal.hide();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => alert('Error de conexión'));
    }

    function openCashRegister() {
        const modal = new bootstrap.Modal(document.getElementById('cashRegisterModal'));
        modal.show();
        
        fetch("{{ route('caja.estado') }}")
            .then(res => res.json())
            .then(data => {
                const body = document.getElementById('cashRegisterBody');
                if (!data.abierta) {
                    body.innerHTML = `
                        <div class="text-center">
                            <p class="text-danger mb-3">No hay caja abierta.</p>
                            <form action="{{ route('caja.abrir') }}" method="POST">
                                @csrf
                                <button class="btn btn-primary">Abrir Caja</button>
                            </form>
                        </div>
                    `;
                    document.getElementById('closeRegisterForm').style.display = 'none';
                } else {
                    body.innerHTML = `
                        <ul class="list-group list-group-flush bg-transparent mb-3">
                            <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                                <span>Monto Inicial:</span>
                                <strong>{{ $configuracion['moneda'] ?? '$' }} ${parseFloat(data.monto_inicial).toFixed(2)}</strong>
                            </li>
                            <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                                <span>Ventas Efectivo:</span>
                                <strong class="text-success">+ {{ $configuracion['moneda'] ?? '$' }} ${parseFloat(data.ventas_efectivo).toFixed(2)}</strong>
                            </li>
                            <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                                <span>Ingresos Manuales:</span>
                                <strong class="text-success">+ {{ $configuracion['moneda'] ?? '$' }} ${parseFloat(data.ingresos).toFixed(2)}</strong>
                            </li>
                            <li class="list-group-item bg-transparent text-white d-flex justify-content-between">
                                <span>Egresos Manuales:</span>
                                <strong class="text-danger">- {{ $configuracion['moneda'] ?? '$' }} ${parseFloat(data.egresos).toFixed(2)}</strong>
                            </li>
                            <li class="list-group-item bg-transparent text-white d-flex justify-content-between border-top border-light mt-2 pt-2">
                                <span class="fs-5">Total en Caja:</span>
                                <span class="fs-5 fw-bold text-warning">{{ $configuracion['moneda'] ?? '$' }} ${parseFloat(data.saldo_esperado).toFixed(2)}</span>
                            </li>
                        </ul>
                        
                        <div class="card bg-secondary bg-opacity-10 border-light border-opacity-10 mb-3">
                            <div class="card-header bg-transparent border-light border-opacity-10 text-white small fw-bold">
                                Registrar Movimiento
                            </div>
                            <div class="card-body">
                                <form id="movementForm" onsubmit="registrarMovimiento(event, ${data.id_caja})">
                                    <div class="row g-2">
                                        <div class="col-4">
                                            <select class="form-select form-select-sm bg-dark text-white border-secondary" name="tipo" required>
                                                <option value="ingreso">Ingreso</option>
                                                <option value="egreso">Egreso</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <input type="number" step="0.01" class="form-control form-control-sm bg-dark text-white border-secondary" name="monto" placeholder="Monto" required>
                                        </div>
                                        <div class="col-4">
                                            <button type="submit" class="btn btn-sm btn-primary w-100">Registrar</button>
                                        </div>
                                        <div class="col-12">
                                            <input type="text" class="form-control form-control-sm bg-dark text-white border-secondary" name="descripcion" placeholder="Motivo (opcional)">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mt-2 text-center">
                            <small class="text-white-50">Ventas Tarjeta: {{ $configuracion['moneda'] ?? '$' }} ${parseFloat(data.ventas_tarjeta).toFixed(2)}</small>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-white border-bottom border-light border-opacity-10 pb-2">Últimos Movimientos</h6>
                            <ul class="list-group list-group-flush bg-transparent" style="max-height: 200px; overflow-y: auto;">
                                ${data.movimientos && data.movimientos.length > 0 ? 
                                    data.movimientos.map(m => `
                                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between align-items-center px-0 py-2 border-light border-opacity-10">
                                            <div>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge ${m.tipo === 'ingreso' ? 'bg-success' : 'bg-danger'} me-2">${m.tipo === 'ingreso' ? '+' : '-'}</span>
                                                    <small>${m.descripcion || 'Sin descripción'}</small>
                                                </div>
                                                <small class="text-white-50" style="font-size: 0.75rem;">${m.hora}</small>
                                            </div>
                                            <span class="${m.tipo === 'ingreso' ? 'text-success' : 'text-danger'} fw-bold">
                                                {{ $configuracion['moneda'] ?? '$' }} ${parseFloat(m.monto).toFixed(2)}
                                            </span>
                                        </li>
                                    `).join('') 
                                    : '<li class="list-group-item bg-transparent text-white-50 text-center">No hay movimientos registrados</li>'
                                }
                            </ul>
                        </div>
                    `;
                    const closeForm = document.getElementById('closeRegisterForm');
                    closeForm.action = `/caja/${data.id_caja}/cerrar`;
                    closeForm.style.display = 'block';
                }
            });
    }

    function registrarMovimiento(e, cajaId) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        fetch(`/caja/${cajaId}/movimiento`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                alert('Movimiento registrado');
                const modal = bootstrap.Modal.getInstance(document.getElementById('cashRegisterModal'));
                modal.hide();
                // Optionally reload modal to show new totals
            } else {
                alert('Error: ' + resp.message);
            }
        })
        .catch(err => alert('Error de conexión'));
    }

    // Product Search & Filter
    document.getElementById('searchProduct').addEventListener('keyup', filterProducts);
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);

    function filterProducts() {
        const term = document.getElementById('searchProduct').value.toLowerCase();
        const cat = document.getElementById('categoryFilter').value;
        const items = document.querySelectorAll('.product-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = item.dataset.name;
            const itemCat = item.dataset.category;
            const matchesSearch = name.includes(term);
            const matchesCat = cat === 'all' || itemCat === cat;

            if (matchesSearch && matchesCat) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResults');
        if (visibleCount === 0) noResults.classList.remove('d-none');
        else noResults.classList.add('d-none');
    }

    // Cart Logic
    function addToCart(product) {
        const existing = cart.find(item => item.id_producto === product.id_producto);
        if (existing) {
            if (existing.cantidad + 1 > product.stock) {
                alert('Stock insuficiente');
                return;
            }
            existing.cantidad++;
        } else {
            if (product.stock < 1) {
                alert('Sin stock disponible');
                return;
            }
            cart.push({
                id_producto: product.id_producto,
                nombre: product.nombre,
                precio: parseFloat(product.precio_venta),
                cantidad: 1,
                stock: product.stock
            });
        }
        updateCartUI();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    function increaseQuantity(index) {
        const item = cart[index];
        if (item.cantidad + 1 > item.stock) {
            alert('Stock máximo alcanzado: ' + item.stock);
            return;
        }
        item.cantidad++;
        updateCartUI();
    }

    function decreaseQuantity(index) {
        const item = cart[index];
        if (item.cantidad - 1 <= 0) {
            removeFromCart(index);
        } else {
            item.cantidad--;
            updateCartUI();
        }
    }

    function updateQuantity(index, newQty) {
        const item = cart[index];
        newQty = parseInt(newQty);
        
        if (isNaN(newQty) || newQty <= 0) {
            removeFromCart(index);
            return;
        }
        
        if (newQty > item.stock) {
            alert('Stock máximo alcanzado: ' + item.stock);
            newQty = item.stock;
        }
        item.cantidad = newQty;
        updateCartUI();
    }

    function updateCartUI() {
        const tbody = document.getElementById('cartItems');
        const emptyState = document.getElementById('emptyCart');
        const btnCheckout = document.getElementById('btnCheckout');
        
        tbody.innerHTML = '';
        
        if (cart.length === 0) {
            emptyState.classList.remove('d-none');
            btnCheckout.disabled = true;
        } else {
            emptyState.classList.add('d-none');
            btnCheckout.disabled = false;
        }

        let subtotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.cantidad * item.precio;
            subtotal += itemTotal;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="width: 35%;">
                    <div class="fw-bold text-truncate" style="max-width: 130px;" title="${item.nombre}">${item.nombre}</div>
                    <div class="small text-white-50">{{ $configuracion['moneda'] ?? '$' }} ${item.precio.toFixed(2)}</div>
                </td>
                <td style="width: 35%;">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-outline-secondary text-white border-secondary" type="button" onclick="decreaseQuantity(${index})"><i class="bi bi-dash"></i></button>
                        <input type="number" class="form-control bg-secondary bg-opacity-25 text-white border-secondary text-center px-1" 
                               value="${item.cantidad}" min="0" max="${item.stock}" 
                               onchange="updateQuantity(${index}, this.value)">
                        <button class="btn btn-outline-secondary text-white border-secondary" type="button" onclick="increaseQuantity(${index})"><i class="bi bi-plus"></i></button>
                    </div>
                </td>
                <td class="text-end" style="width: 20%;">{{ $configuracion['moneda'] ?? '$' }} ${itemTotal.toFixed(2)}</td>
                <td class="text-end" style="width: 10%;">
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${index})" title="Eliminar"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Calculate Totals
        // Recalculate discount
        if (discountType === 'percentage') {
            manualDiscount = (subtotal * discountPercentage) / 100;
        } else {
            manualDiscount = fixedDiscountInput;
        }
        
        // Cap discount at subtotal
        if (manualDiscount > subtotal) {
            manualDiscount = subtotal;
        }

        // Base = Total / (1 + Rate)
        const base = subtotal / (1 + taxRate);
        const tax = subtotal - base;

        let total = subtotal - manualDiscount;
        if(total < 0) total = 0;
        
        // For display:
        document.getElementById('summarySubtotal').innerText = base.toFixed(2);
        document.getElementById('summaryTax').innerText = tax.toFixed(2);
        
        let discountDisplay = manualDiscount.toFixed(2);
        if (discountType === 'percentage' && manualDiscount > 0) {
            discountDisplay += ` (${discountPercentage}%)`;
        }
        document.getElementById('summaryDiscount').innerText = discountDisplay;
        
        document.getElementById('summaryTotal').innerText = total.toFixed(2);
        document.getElementById('cartCount').innerText = cart.length + ' items';
        
        // Update modal total as well
        document.getElementById('modalTotal').innerText = total.toFixed(2);
    }

    // Checkout Logic
    let lastSaleId = null;

    document.getElementById('posForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnConfirmPay');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';

        const form = e.target;
        const formData = new FormData(form);
        
        // Ensure manual discount is sent if not in form
        if (!formData.has('descuento')) {
            formData.append('descuento', manualDiscount);
        }

        // Add products manually because they might not be in the form if addHiddenInput wasn't called (though openCheckoutModal calls it)
        // Ideally we trust openCheckoutModal, but let's be safe.
        // Actually openCheckoutModal handles it.

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Success
                lastSaleId = data.venta_id;
                
                // Calculate change for display
                const total = parseFloat(document.getElementById('modalTotal').innerText);
                const received = parseFloat(document.getElementById('montoRecibido').value) || 0;
                let change = 0;
                if (document.getElementById('metodoPagoNombre').value === 'Efectivo') {
                    change = Math.max(0, received - total);
                }
                
                document.getElementById('successChange').innerText = change.toFixed(2);
                
                // Hide checkout modal
                bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                
                // Show success modal
                new bootstrap.Modal(document.getElementById('saleSuccessModal')).show();
                
                // Reset Cart
                cart = [];
                manualDiscount = 0;
                fixedDiscountInput = 0;
                discountPercentage = 0;
                discountType = 'fixed';
                updateCartUI();
                form.reset();
                
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error de conexión');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>CONFIRMAR VENTA';
        });
    });

    function printLastTicket() {
        if (lastSaleId) {
            window.open(`/ventas/${lastSaleId}/ticket`, '_blank');
        }
    }

    function closeSuccessModal() {
        bootstrap.Modal.getInstance(document.getElementById('saleSuccessModal')).hide();
        document.getElementById('searchProduct').focus();
    }

    function openCheckoutModal() {
        const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        
        // Populate hidden inputs
        document.getElementById('formClienteId').value = document.getElementById('clientSelect').value;
        document.getElementById('formImpuesto').value = document.getElementById('summaryTax').innerText;
        
        // Add manual discount
        let discountInput = document.getElementById('formDescuento');
        if (!discountInput) {
            discountInput = document.createElement('input');
            discountInput.type = 'hidden';
            discountInput.name = 'descuento';
            discountInput.id = 'formDescuento';
            document.getElementById('posForm').appendChild(discountInput);
        }
        discountInput.value = manualDiscount;

        // Clean previous hidden product inputs
        const form = document.getElementById('posForm');
        form.querySelectorAll('.product-input').forEach(e => e.remove());
        
        // Add current products
        cart.forEach((item, index) => {
            addHiddenInput(form, `id_producto[${index}]`, item.id_producto);
            addHiddenInput(form, `cantidad[${index}]`, item.cantidad);
            addHiddenInput(form, `precio_unitario[${index}]`, item.precio);
        });

        // Trigger payment method toggle to set initial state
        const checkedMp = document.querySelector('input[name="metodo_pago_id"]:checked');
        if(checkedMp) togglePaymentFields(checkedMp);

        modal.show();
        
        // Focus on amount received if cash is selected
        setTimeout(() => {
            const amountInput = document.getElementById('montoRecibido');
            if(!amountInput.closest('.d-none')) amountInput.focus();
        }, 500);
    }

    function addHiddenInput(form, name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        input.classList.add('product-input');
        form.appendChild(input);
    }

    function togglePaymentFields(radio) {
        const label = document.querySelector(`label[for="${radio.id}"]`);
        const name = label.dataset.nombre.toLowerCase();
        document.getElementById('metodoPagoNombre').value = label.dataset.nombre;

        const cashFields = document.getElementById('cashFields');
        const cardFields = document.getElementById('cardFields');
        const btnConfirm = document.getElementById('btnConfirmPay');

        if (name === 'efectivo') {
            cashFields.classList.remove('d-none');
            cardFields.classList.add('d-none');
            calculateChange();
        } else {
            cashFields.classList.add('d-none');
            cardFields.classList.remove('d-none');
            btnConfirm.disabled = false; // Always enable for non-cash (simplified)
        }
    }

    function calculateChange() {
        const total = parseFloat(document.getElementById('modalTotal').innerText);
        const received = parseFloat(document.getElementById('montoRecibido').value) || 0;
        const change = received - total;
        
        const changeDisplay = document.getElementById('cambioDisplay');
        const btnConfirm = document.getElementById('btnConfirmPay');

        if (change >= 0) {
            changeDisplay.innerText = change.toFixed(2);
            changeDisplay.classList.remove('text-danger');
            changeDisplay.classList.add('text-success');
            btnConfirm.disabled = false;
        } else {
            changeDisplay.innerText = "Falta: " + Math.abs(change).toFixed(2);
            changeDisplay.classList.add('text-danger');
            changeDisplay.classList.remove('text-success');
            btnConfirm.disabled = true;
        }
    }

    function saveNewClient() {
        const form = document.getElementById('newClientForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.estado = 'activo'; // Default state

        fetch("{{ route('clientes.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(resp => {
            if (resp.success) {
                const cliente = resp.cliente;
                const select = document.getElementById('clientSelect');
                const option = document.createElement('option');
                option.value = cliente.id_cliente;
                option.text = `${cliente.nombre} ${cliente.apellido} (${cliente.documento})`;
                select.add(option);
                select.value = cliente.id_cliente; // Select the new client
                
                alert('Cliente registrado correctamente');
                const modal = bootstrap.Modal.getInstance(document.getElementById('newClientModal'));
                modal.hide();
                form.reset();
            } else {
                alert('Error: ' + (resp.message || 'Error al guardar cliente'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error de conexión al guardar cliente');
        });
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', function(e) {
        // F1: Focus Search
        if (e.key === 'F1') {
            e.preventDefault();
            document.getElementById('searchProduct').focus();
        }
        // F2: Checkout
        if (e.key === 'F2') {
            e.preventDefault();
            if (!document.getElementById('btnCheckout').disabled) {
                openCheckoutModal();
            }
        }
        // F4: Movements
        if (e.key === 'F4') {
            e.preventDefault();
            openMovements();
        }
        // F8: Recent Sales
        if (e.key === 'F8') {
            e.preventDefault();
            openRecentSales();
        }
        // F9: Hold Sale
        if (e.key === 'F9') {
            e.preventDefault();
            holdSale();
        }
        // F10: Cash Register
        if (e.key === 'F10') {
            e.preventDefault();
            openCashRegister();
        }
    });
</script>
@endsection
