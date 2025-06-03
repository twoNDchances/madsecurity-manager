<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'important',
        'user_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
        'important' => 'boolean',
    ];

    public function superior()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'user_id');
    }

    public function policies()
    {
        return $this->hasMany(Policy::class, 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Policy::class, 'policies_users');
    }

    public function hasPermission(string $action): bool
    {
        if ($this->groups()->count() === 0)
        {
            return false;
        }
        $hasAnyPermission = $this->groups()->whereHas('permissions')->exists();
        if (!$hasAnyPermission)
        {
            return false;
        }
        return $this->groups()->whereHas(
            'permissions',
            function ($query) use ($action)
            {
                $query->where('action', $action);
            }
        )->exists();
    }
}
