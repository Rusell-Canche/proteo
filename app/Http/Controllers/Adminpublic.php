<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NavbarColor;

class Adminpublic extends Controller
{
    public function cambiarColor(Request $request)
    {
        // Valida y obtiene el color enviado desde Vue.js
        $color = $request->validate([
            'color' => 'required|string'
        ])['color'];

        // Actualiza o crea el registro en MongoDB
        $navbarColor = NavbarColor::updateOrCreate(
            [],
            ['color' => $color]
        );

        return response()->json(['message' => 'Color de navbar actualizado correctamente']);
    }

    public function getColor()
    {
        $navbarColor = NavbarColor::firstOrCreate([]); // Obtiene el primer registro o crea uno nuevo si no existe
        return response()->json(['color' => $navbarColor->color]);
    }
}
