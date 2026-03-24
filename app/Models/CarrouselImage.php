<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class CarrouselImage extends Model
{
    protected $collection = 'carrousel_images'; // Establece el nombre de la colección

    protected $fillable = ['imagen', 'fecha_inicio', 'fecha_fin', 'enlace'];
    
}
