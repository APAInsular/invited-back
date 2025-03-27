<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Hash;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstSurname',
        'secondSurname',
        'phone',
        'email',
        'password',
        // 'role',
    ];

    // public function toArray()
    // {
    //     $hashids = new Hashids();
    //     $array['id'] = $hashids->encode($this->id); // Reemplaza el id normal por el hasheado
    //     $array['name'] = $this->name;
    //     $array['firstSurname'] = $this->firstSurname;   
    //     $array['secondSurname'] = $this->secondSurname;
    //     $array['phone'] = $this->phone;
    //     $array['email'] = $this->email;

        
    //     if (isset($array['related_model_id'])) {
    //         $array['related_model_id'] = $hashids::encode($this->related_model_id);
    //     }

    //     return $array;
    // }

    // protected $appends = ['hashed_id'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        // 'id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function partner(): HasOne
    {
        return $this->hasOne(Partner::class);
    }

    public function weddings()
    {
        return $this->hasMany(Wedding::class, 'user_id');
    }
}
