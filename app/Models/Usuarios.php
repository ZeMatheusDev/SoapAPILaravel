<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Usuarios extends Model
{
    protected $connection = 'mongodb'; // conexão mongodb
    protected $collection = 'usuarios'; // nome da collection
    protected $fillable = ['usuario', 'senha']; // campos preenchíveis
    public $timestamps = false; // se não for necessário timestamps
}