<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ActasdeCabildodeAyuntamientosdeQuintanaRoo extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'ActasdeCabildodeAyuntamientosdeQuintanaRoo_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Ayuntamiento', 'Período Constitucional', 'Acta de sesión', 'Libro', 'Lefort', 'Año', 'Número de caja', 'Fechas extremas', 'Período que comprende', 'Fojas', 'Descripción del contenido', 'Estante', 'Anaquel', 'Charola', 'Recurso Digital'];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
            'Ayuntamiento' => 'string', 'Período Constitucional' => 'string', 'Acta de sesión' => 'string', 'Libro' => 'string', 'Lefort' => 'string', 'Año' => 'number', 'Número de caja' => 'number', 'Fechas extremas' => 'string', 'Período que comprende' => 'string', 'Fojas' => 'number', 'Descripción del contenido' => 'string', 'Estante' => 'string', 'Anaquel' => 'string', 'Charola' => 'string', 'Recurso Digital' => 'file',
        ];

    // Define alias para los campos
    public static $fieldAliases = [
            'Ayuntamiento' => 'Ayuntamiento', 'Período Constitucional' => 'Período Constitucional', 'Acta de sesión' => 'Acta De Sesión', 'Libro' => 'Libro', 'Lefort' => 'Lefort', 'Año' => 'Año', 'Número de caja' => 'Número De Caja', 'Fechas extremas' => 'Fechas Extremas', 'Período que comprende' => 'Período Que Comprende', 'Fojas' => 'Fojas', 'Descripción del contenido' => 'Descripción Del Contenido', 'Estante' => 'Estante', 'Anaquel' => 'Anaquel', 'Charola' => 'Charola', 'Recurso Digital' => 'Recurso Digital',
        ];

    // Define los campos obligatorios
    public static $requiredFields = ['Ayuntamiento', 'Período Constitucional', 'Año', 'Fechas extremas', 'Fojas'];

    // Define el nombre con espacios
    public static $title = 'Actas de Cabildo de Ayuntamientos de Quintana Roo'; // Nombre original de la plantilla con espacios

}