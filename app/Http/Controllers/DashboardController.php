<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Comanda;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = now()->startOfDay();

        $ventasHoy = Comanda::whereDate('created_at', '>=', $hoy)
            ->where('estado_comanda', '!=', 'cancelada')
            ->sum('total_usd');

        $pedidosHoy = Comanda::whereDate('created_at', '>=', $hoy)
            ->where('estado_comanda', '!=', 'cancelada')
            ->count();

        $comandasActivas = Comanda::whereNotIn('estado_comanda', ['cerrada', 'cancelada'])
            ->with('cliente', 'usuario', 'comandaDetalles')
            ->latest()
            ->get();

        $totalClientes = Cliente::count();

        $tasaBcv = Configuracion::obtener('tasa_bcv', '42.50');

        return view('dashboard', compact('ventasHoy', 'pedidosHoy', 'comandasActivas', 'totalClientes', 'tasaBcv'));
    }
}
