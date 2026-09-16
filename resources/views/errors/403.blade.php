@extends('layouts.app')
@section('title', 'Acceso denegado')
@section('content')
<div style="display:flex;align-items:center;justify-content:center;min-height:60vh;">
    <div style="text-align:center;max-width:420px;">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--danger-soft);display:grid;place-items:center;margin:0 auto 24px;">
            <i class="bi bi-shield-lock" style="font-size:36px;color:var(--danger);"></i>
        </div>
        <h1 style="font-size:48px;font-weight:700;color:var(--fg);margin:0 0 8px;">403</h1>
        <h2 style="font-size:18px;font-weight:600;color:var(--fg);margin:0 0 12px;">Acceso denegado</h2>
        <p style="color:var(--fg-2);margin:0 0 28px;line-height:1.6;">No tienes permiso para realizar esta acción. Si crees que esto es un error, contacta al administrador.</p>
        <a href="{{ route('dashboard') }}" class="btn-primary-brand" style="display:inline-flex;align-items:center;gap:8px;">
            <i class="bi bi-arrow-left"></i>
            Volver al Dashboard
        </a>
    </div>
</div>
@endsection
