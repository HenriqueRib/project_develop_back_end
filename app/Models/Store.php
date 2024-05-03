<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address_zipcode',
        'address_street',
        'address_number',
        'address_complement',
        'address_district',
        'address_state',
        'active',
        /**
         * not active 0
         * active 1
         */
        ];

        protected $hidden = [
            'deleted_at'
        ];
    
        protected $dates = ['created_at', 'updated_at', 'deleted_at'];

        public function books()
        {
            return $this->hasMany(Book::class, 'id_stores', 'id');
        }
}