<?php

namespace App\Models;

use App\Services\FingerprintService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decision extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'phase_type',
        'score',
        'action',
        'action_configuration',
        'wordlist_id',
        'user_id',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getWordlist()
    {
        return $this->belongsTo(Wordlist::class, 'wordlist_id');
    }

    // Relationships
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function defenders()
    {
        return $this->belongsToMany(Defender::class, 'defenders_decisions')
        ->withPivot('status');
    }

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    // Businesses
    public static bool $skipObserver = false;
}
