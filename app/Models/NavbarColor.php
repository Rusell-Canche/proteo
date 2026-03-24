<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;


class NavbarColor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'navbar_colors';
    protected $fillable = ['color'];
}
