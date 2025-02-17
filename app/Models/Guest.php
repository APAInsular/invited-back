<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'Name', 
        'First_Surname', 
        'Second_Surname', 
        'Extra_Information', 
        'Allergy', 
        'Feeding', 
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
