<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    protected $fillable = [
        'cuit',
        'max_situation',
        'amount',
    ];
}
