<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    //
     use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'location',
        'condition',
        'quantity',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
