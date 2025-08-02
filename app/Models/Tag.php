<?php

namespace App\Models;

use App\Services\FingerprintService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
        'user_id',
    ];

    protected $hidden = [
        'user_id',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationships
    public function defenders()
    {
        return $this->morphedByMany(Defender::class, 'taggable');
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'taggable');
    }

    public function permissions()
    {
        return $this->morphedByMany(Permission::class, 'taggable');
    }

    public function policies()
    {
        return $this->morphedByMany(Policy::class, 'taggable');
    }

    public function rules()
    {
        return $this->morphedByMany(Rule::class, 'taggable');
    }

    public function targets()
    {
        return $this->morphedByMany(Target::class, 'taggable');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'taggable');
    }

    public function wordlists()
    {
        return $this->morphedByMany(Wordlist::class, 'taggable');
    }

    public function decisions()
    {
        return $this->morphedByMany(Decision::class, 'taggable');
    }

    public function tokens()
    {
        return $this->morphMany(Token::class, 'taggable');
    }

    // Businesses
    public static bool $skipObserver = false;
}
