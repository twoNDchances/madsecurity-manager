<?php

namespace App\Models;

use App\Models\Scopes\ImportantScope;
use App\Services\FingerprintService;
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
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'user_id',
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

    // Belongs
    public function getSuperior()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getSubordinates()
    {
        return $this->hasMany(User::class, 'user_id');
    }

    public function getPermissions()
    {
        return $this->hasMany(Permission::class, 'user_id');
    }

    public function getPolicies()
    {
        return $this->hasMany(Policy::class, 'user_id');
    }

    public function getRules()
    {
        return $this->hasMany(Rule::class, 'user_id');
    }

    public function getTags()
    {
        return $this->hasMany(Tag::class, 'user_id');
    }

    public function getTargets()
    {
        return $this->hasMany(Target::class, 'user_id');
    }

    public function getWordlists()
    {
        return $this->hasMany(Wordlist::class, 'user_id');
    }

    public function getTokens()
    {
        return $this->hasMany(Token::class, 'user_id');
    }

    public function getDecisions()
    {
        return $this->hasMany(Decision::class, 'user_id');
    }

    public function getDefenders()
    {
        return $this->hasMany(Defender::class, 'user_id');
    }

    // Relationships
    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'policies_users');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    public function tokens()
    {
        return $this->belongsToMany(Token::class, 'tokens_users');
    }

    // Businesses
    public function hasPermission(string $action): bool
    {
        if ($this->policies()->count() === 0)
        {
            return false;
        }
        $hasAnyPermission = $this->policies()->whereHas('permissions')->exists();
        if (!$hasAnyPermission)
        {
            return false;
        }
        return $this->policies()->whereHas(
            'permissions',
            function ($query) use ($action)
            {
                $query->where('action', $action);
            }
        )
        ->exists();
    }

    public static bool $skipObserver = false;
}
