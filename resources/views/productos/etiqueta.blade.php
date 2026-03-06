@extends('layouts.app')

@section('title', 'Imprimir Etiqueta')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center d-print-none">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-upc-scan me-2 text-primary"></i>Etiqueta de Producto</h5>
                    <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm transform-hover">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body text-center p-5" id="printableArea">
                    <h2 class="fw-bold mb-1 text-white">{{ $producto->nombre }}</h2>
                    <p class="text-white-50 mb-3 text-uppercase small fw-bold tracking-wide">{{ $producto->categoria->nombre ?? 'General' }}</p>
                    
                    <div class="my-4 d-inline-block bg-white p-3 rounded-3 shadow-sm border border-light">
                        <svg id="barcode"></svg>
                    </div>
                    
                    <div class="mt-2">
                        <h1 class="fw-bold display-4 text-white">{{ $configuracion['moneda'] ?? '$' }} {{ number_format($producto->precio_venta, 2) }}</h1>
                    </div>
                </div>
                <div class="card-footer bg-transparent text-center p-4 d-print-none border-top border-light border-opacity-10">
                    <button onclick="window.print()" class="btn btn-lg btn-success rounded-pill px-5 shadow-lg transform-hover">
                        <i class="bi bi-printer-fill me-2"></i>Imprimir Etiqueta
                    </button>
                    <div class="mt-3 text-muted small">
                        <i class="bi bi-info-circle me-1"></i>Asegúrese de configurar la impresora para etiquetas si es necesario.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            JsBarcode("#barcode", "{{ $producto->codigo_barras }}", {
                format: "CODE128",
                lineColor: "#000",
                width: 3,
                height: 100,
                displayValue: true,
                fontSize: 20,
                margin: 10
            });
        } catch (e) {
            console.error("Error generando código de barras:", e);
            document.getElementById('barcode').outerHTML = '<div class="alert alert-warning">Error: El código "{{ $producto->codigo_barras }}" no es válido para CODE128.</div>';
        }
    });
</script>

@endsection
