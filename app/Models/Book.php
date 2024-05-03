<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id_stores',
        'name',
        'isbn',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function store()
    {
        return $this->hasOne(Store::class, 'id', 'id_stores');
    }

}
