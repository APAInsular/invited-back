<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'firstSurname', 
        'secondSurname', 
        'extraInformation', 
        'allergy', 
        'feeding', 
        'wedding_id'
    ];

    public function wedding()
    {
        return $this->belongsTo(Wedding::class);
    }

    public function attendants()
    {
        return $this->hasMany(Attendant::class);
    }
}
