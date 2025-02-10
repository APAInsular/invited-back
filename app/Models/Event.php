<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id', 'name', 'description', 'fecha', 'time', 'location', 'estado'
    ];

    public function boda()
    {
        return $this->belongsTo(Wedding::class);
    }
}
