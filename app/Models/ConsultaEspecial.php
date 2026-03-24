<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ConsultaEspecial extends Model
{   
    protected $connection = 'mongodb';

    protected $collection = 'ConsultaEspecial_pendientes'; // Establece el nombre de la colección

    protected $guarded = ['_id', 'Tipo de documento', 'Título', 'Número de inventario', 'Número de revistero', 'Autor', 'Año', 'Observaciones', 'Estante', 'Anaquel', 'Charola', 'Recurso Digital', ];

    // Define los tipos de datos de los campos de manera estática
    public static $fieldTypes = [
        'Tipo de documento' => 'string', 'Título' => 'string', 'Número de inventario' => 'number', 'Número de revistero' => 'number', 'Autor' => 'string', 'Año' => 'number', 'Observaciones' => 'string', 'Estante' => 'string', 'Anaquel' => 'string', 'Charola' => 'string', 'Recurso Digital' => 'file', 
    ];

    // Define alias para los campos
    public static $fieldAliases = [
        'Tipo de documento' => 'Tipo De Documento', 'Título' => 'Título', 'Número de inventario' => 'Número De Inventario', 'Número de revistero' => 'Número De Revistero', 'Autor' => 'Autor', 'Año' => 'Año', 'Observaciones' => 'Observaciones', 'Estante' => 'Estante', 'Anaquel' => 'Anaquel', 'Charola' => 'Charola', 'Recurso Digital' => 'Recurso Digital', 
    ];

    // Define los campos obligatorios
    public static $requiredFields = [
        'Tipo de documento', 'Título', 'Número de inventario', 'Número de revistero', 'Año', 
    ];

    // Define el nombre con espacios
    public static $title = 'Consulta Especial'; // Nombre original de la plantilla con espacios

}