<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CarrouselImage;
use Illuminate\Support\Facades\Storage;


class CarrouselController extends Controller
{
    public function store(Request $request)
    {
        // Valida la solicitud
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'enlace' => 'nullable|url', // Nueva validación para el campo de enlace

        ]);
    
        // Procesa la imagen y obtén la ruta temporal
        $imagen = $request->file('imagen');
        $imagenNombre = time() . '.' . $imagen->getClientOriginalExtension();
        $rutaRelativa = 'carrousel_images/' . $imagenNombre;
    
        // Almacena la imagen en el disco público
        $imagen->storeAs('carrousel_images', $imagenNombre, 'public');
    
        // Guarda la información en la colección, almacenando solo la ruta relativa
        $carrouselImage = new CarrouselImage([
            'imagen' => $rutaRelativa,
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
            'enlace' => $request->input('enlace'), // Nuevo campo de enlace

        ]);
    
        $carrouselImage->save();
    
        // Puedes agregar un mensaje de éxito si es necesario
        return response()->json(['message' => 'Imagen almacenada correctamente']);
    }
    

    // Obtiene imágenes activas en el carrousel basadas en la fecha actual
    public function getImagesForCarousel()
    {
        $today = now()->format('Y-m-d H:i:s');
        $images = CarrouselImage::where('fecha_inicio', '<=', $today)
            ->where('fecha_fin', '>=', $today)
            ->get();

        return response()->json($images);
    }

    // Obtiene todas las imágenes almacenadas en el carrousel
    public function getAllCarrouselImages()
    {
        $carrouselImages = CarrouselImage::all();

        return response()->json($carrouselImages);
    }

    // Elimina una imagen específica del carrousel por su ID
    public function eliminarImagen($id)
    {
        $imagen = CarrouselImage::find($id);

        if (!$imagen) {
            return response()->json(['message' => 'Imagen no encontrada'], 404);
        }
        
        $urlImagen = $imagen->imagen;
        $nombreArchivo = basename($urlImagen);

        // Elimina la imagen de la carpeta de almacenamiento
        Storage::disk('public')->delete('carrousel_images/' . $nombreArchivo);

        // Elimina la imagen de la colección y la base de datos
        $imagen->delete();

        return response()->json(['message' => 'Imagen eliminada correctamente']);
    }
}
