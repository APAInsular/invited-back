<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendant extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name', 
        'First_Surname', 
        'Second_Surname', 
        'age', 
        'guest_id'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}
