<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AcervoBibliográfico extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'AcervoBibliográfico_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Título', 'Autor', 'Editorial', 'Año de publicacion', 'Edición', 'ISBN', 'Género o categoría', 'Número de páginas', 'Resumen breve', 'Ubicación en la biblioteca', 'Idioma', 'Recurso digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Título' => 'string', 'Autor' => 'string', 'Editorial' => 'string', 'Año de publicacion' => 'string', 'Edición' => 'string', 'ISBN' => 'number', 'Género o categoría' => 'string', 'Número de páginas' => 'number', 'Resumen breve' => 'string', 'Ubicación en la biblioteca' => 'string', 'Idioma' => 'string', 'Recurso digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Título' => 'Título', 'Autor' => 'Autor', 'Editorial' => 'Editorial', 'Año de publicacion' => 'Año De Publicacion', 'Edición' => 'Edición', 'ISBN' => 'Isbn', 'Género o categoría' => 'Género O Categoría', 'Número de páginas' => 'Número De Páginas', 'Resumen breve' => 'Resumen Breve', 'Ubicación en la biblioteca' => 'Ubicación En La Biblioteca', 'Idioma' => 'Idioma', 'Recurso digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        'Título', 'Autor', 'Año de publicacion', 'Número de páginas', 'Ubicación en la biblioteca', 'Idioma', 'Recurso digital', 
    ];

    // Define el nombre con espacios
    public static $title = 'Acervo Bibliográfico'; // Nombre original de la plantilla con espacios

}