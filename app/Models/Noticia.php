<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    protected $connection = 'mongodb'; // Esto es para asegurar que use MongoDB
    protected $collection = 'noticias_collection'; // Especifica la colección de MongoDB


    protected $fillable = [
        'tab', 'title', 'date' ,'image', 'content', 'imageWidth', 'num_noticia',
    ];
}
