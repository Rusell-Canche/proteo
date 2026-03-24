<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Periódicos extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'Periódicos_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Nombre', 'Año', 'Mes', 'Día', 'Número de páginas', 'Recurso digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Nombre' => 'string', 'Año' => 'number', 'Mes' => 'string', 'Día' => 'number', 'Número de páginas' => 'number', 'Recurso digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Nombre' => 'Nombre', 'Año' => 'Año', 'Mes' => 'Mes', 'Día' => 'Día', 'Número de páginas' => 'Número De Páginas', 'Recurso digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        'Nombre', 'Año', 'Mes', 'Día', 'Número de páginas', 'Recurso digital', 
    ];

    // Define el nombre con espacios
    public static $title = 'Periódicos'; // Nombre original de la plantilla con espacios

}