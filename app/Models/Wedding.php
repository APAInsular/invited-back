<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wedding extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'Ceremony_Start_Time',
        // 'Lunch_Start_Time',
        // 'Dinner_Start_Time',
        // 'Party_Start_Time',
        // 'Party_Finish_Time',
        'user_id',
        'partner_id',
        'user_name',
        'partner_name',
        'Dress_Code',
        'Wedding_Date',
        'Music',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'Wedding_Date' => 'date',
    ];

    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
