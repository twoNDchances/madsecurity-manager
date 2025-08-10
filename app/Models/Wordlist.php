<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wordlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias',
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
    public function words()
    {
        return $this->hasMany(Word::class, 'wordlist_id');
    }

    public function targets()
    {
        return $this->hasMany(Target::class, 'wordlist_id');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class, 'wordlist_id');
    }

    public function decisions()
    {
        return $this->hasMany(Decision::class, 'wordlist_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    // Businesses
    public static bool $skipObserver = false;
}
