@extends('layouts.app')
@section('title', 'Créditos')
@section('content')
<div class="page-head">
    <div>
        <div class="page-eyebrow">Financiero</div>
        <h1 class="page-title">Créditos</h1>
        <p class="page-sub">Gestión de créditos a clientes</p>
    </div>
</div>
<div class="filter-card">
    <div class="search-box">
        <input type="text" id="buscar" placeholder="Buscar crédito…" class="input-brand" aria-label="Buscar crédito" />
    </div>
</div>
<div class="table-panel">
    <table class="table display" id="tablaCreditos" style="width:100%">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Monto total</th>
                <th>Saldo pendiente</th>
                <th>Estado</th>
                <th>Fecha emisión</th>
                <th style="text-align:right">Acciones</th>
            </tr>
        </thead>
    </table>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('tablaCreditos', '{{ route("creditos.data") }}', [
        {data:'cliente.nombre',name:'cliente_id'},
        {data:'monto_total_usd',name:'monto_total_usd'},{data:'saldo_pendiente_usd',name:'saldo_pendiente_usd'},
        {data:'estado',name:'estado'},{data:'fecha_emision',name:'fecha_emision',render:function(d){return formatDate(d);}},
        {data:'acciones',name:'acciones',orderable:false,searchable:false}
    ]);
});
</script>
@endpush
