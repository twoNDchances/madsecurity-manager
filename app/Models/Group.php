<?php

namespace App\Models;

use App\Services\FingerprintService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'execution_order',
        'level',
        'description',
        'user_id',
    ];

    protected $casts = [
        'execution_order' => 'integer',
        'level' => 'integer',
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
    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rules_groups')
        ->withPivot('position')
        ->orderBy('position');
    }

    public function defenders()
    {
        return $this->belongsToMany(Defender::class, 'defenders_groups')
        ->withPivot('status');
    }

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    // Businesses
    public static bool $skipObserver = false;
}
