<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Noticia;

class NoticiaController extends Controller
{
    public function store(Request $request)
    {
        // Valida el contenido del request
        $validated = $request->validate([
            'tab' => 'required|string',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'content' => 'required|string',
            'imageWidth' => 'required|integer',
            'num_noticia' => 'nullable|string', // Asegura que 'num_noticia' sea opcional
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Maneja el archivo de imagen si está presente
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('noticias', 'public');
            $validated['image'] = $imagePath;
        } else {
            // Si no hay imagen, establece la ruta en nulo
            $validated['image'] = null;
        }
    
        // Actualiza o crea la noticia en la base de datos
        // Usa el campo 'num_noticia' solo si está presente
        if (isset($validated['num_noticia'])) {
            Noticia::updateOrCreate(
                ['tab' => $validated['tab'], 'num_noticia' => $validated['num_noticia']],
                $validated
            );
        } else {
            Noticia::updateOrCreate(
                ['tab' => $validated['tab']],
                $validated
            );
        }
    
        return response()->json(['message' => 'Contenido guardado con éxito'], 201);
    }

    public function index()
    {
        $noticias = Noticia::all();
        
        // Agrupar por 'tab' y para 'noticias' también agrupar por 'num_noticia'
        $groupedNoticias = $noticias->groupBy(function ($item) {
            if ($item->tab === 'noticias' && $item->num_noticia) {
                return 'noticias_' . $item->num_noticia;
            }
            return $item->tab;
        });
    
        return response()->json($groupedNoticias);
    }
}    