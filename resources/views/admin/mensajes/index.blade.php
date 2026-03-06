@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="glass-card overflow-hidden">
                <div class="card-header bg-transparent border-bottom border-light border-opacity-10 py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-white"><i class="bi bi-inbox me-2 text-primary"></i>Mensajes de Contacto</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="bg-secondary bg-opacity-25 text-white-50 text-uppercase small">
                                <tr>
                                    <th class="px-4 py-3 border-0">Fecha</th>
                                    <th class="px-4 py-3 border-0">Nombre</th>
                                    <th class="px-4 py-3 border-0">Email</th>
                                    <th class="px-4 py-3 border-0">Mensaje</th>
                                    <th class="px-4 py-3 border-0 text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($mensajes as $mensaje)
                                <tr class="border-bottom border-light border-opacity-10">
                                    <td class="px-4 py-3 text-white-50">{{ $mensaje->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 fw-bold">{{ $mensaje->nombre }}</td>
                                    <td class="px-4 py-3 text-white-50">{{ $mensaje->email }}</td>
                                    <td class="px-4 py-3 text-white-50" style="max-width: 400px; white-space: normal;">
                                        {{ Str::limit($mensaje->mensaje, 100) }}
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#msgModal{{ $mensaje->id }}" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-white-50">
                                        <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                        No hay mensajes de contacto recibidos.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($mensajes->hasPages())
                    <div class="card-footer bg-transparent border-top border-light border-opacity-10 py-3">
                        {{ $mensajes->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@foreach($mensajes as $mensaje)
<!-- Modal -->
<div class="modal fade text-start" id="msgModal{{ $mensaje->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark border border-light border-opacity-10 text-white shadow-lg">
            <div class="modal-header border-bottom border-light border-opacity-10">
                <h5 class="modal-title">
                    <i class="bi bi-envelope-open me-2 text-primary"></i>
                    Detalle del Mensaje
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <label class="text-white-50 small text-uppercase mb-1">Remitente</label>
                        <div class="fs-4 fw-bold text-white d-flex align-items-center">
                            <i class="bi bi-person-circle me-2 text-secondary"></i>
                            {{ $mensaje->nombre }}
                        </div>
                    </div>
                    
                    <div class="col-12">
                         <div class="p-3 rounded-3 border border-light border-opacity-10 bg-dark bg-opacity-50">
                            <label class="text-white-50 small text-uppercase mb-1 d-block">Correo Electrónico (Responder a)</label>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <i class="bi bi-envelope-at fs-4 text-info"></i>
                                <a href="mailto:{{ $mensaje->email }}?subject=RE: Mensaje de contacto - PrismaNova" class="fs-5 text-info text-decoration-none fw-bold me-auto" id="email-text-{{ $mensaje->id }}">{{ $mensaje->email }}</a>
                                <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $mensaje->email }}'); this.innerHTML='<i class=\'bi bi-check\'></i> Copiado'; setTimeout(()=>this.innerHTML='<i class=\'bi bi-clipboard\'></i> Copiar', 2000);">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                         </div>
                    </div>

                    <div class="col-12">
                        <label class="text-white-50 small text-uppercase mb-1">Fecha de Recepción</label>
                        <div class="fs-6 text-white-50">
                            <i class="bi bi-calendar3 me-2"></i>{{ $mensaje->created_at->format('d \d\e F \d\e Y, h:i A') }}
                        </div>
                    </div>
                </div>
                
                <div class="bg-secondary bg-opacity-10 rounded-3 p-4 border border-light border-opacity-10">
                    <label class="text-primary small text-uppercase fw-bold mb-3 d-block border-bottom border-light border-opacity-10 pb-2">
                        <i class="bi bi-chat-left-text me-2"></i>Contenido del Mensaje
                    </label>
                    <p class="mb-0 fs-5" style="white-space: pre-wrap; line-height: 1.6; font-family: 'Segoe UI', sans-serif;">{{ $mensaje->mensaje }}</p>
                </div>
            </div>
            <div class="modal-footer border-top border-light border-opacity-10">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="mailto:{{ $mensaje->email }}?subject=RE: Mensaje de contacto - PrismaNova" class="btn btn-primary px-4">
                    <i class="bi bi-reply-fill me-2"></i>Responder al Correo
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
