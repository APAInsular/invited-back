<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Name',
        'First_Surname',
        'Second_Surname',
        'Extra_Information',
        'Allergy',
        'Feeding',
        'weeding_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'weeding_id' => 'integer',
    ];

    public function weeding(): BelongsTo
    {
        return $this->belongsTo(Weeding::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }
}
