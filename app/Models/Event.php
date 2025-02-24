<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id',
        'name',
        'description',
        'fecha',
        'time',
        'location_id',
        'estado'
    ];

    public function boda()
    {
        return $this->belongsTo(Wedding::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
