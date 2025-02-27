<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'wedding_id',
        'image',
    ];

    /**
     * Relación: Una imagen pertenece a una boda.
     */
    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }
}
