<?php

namespace App\Models;

use App\Services\FingerprintService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'action',
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
    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'policies_permissions');
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
    private static array $methodDescriptions = [
        'all' => 'Full',
        'viewAny' => 'List',
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'deleteAny' => 'Multi-Delete',
        'health' => 'Health-Check',
        'collect' => 'Collect-Data',
        'apply' => 'Apply-Data',
        'revoke' => 'Revoke-Data',
        'implement' => 'Implement-Data',
        'suspend' => 'Suspend-Data',
    ];

    private static array $exclusionList = [
        'report' => [
            'create',
            'update',
        ],
        'fingerprint' => [
            'create',
            'update',
        ],
    ];

    public static function getPolicyPermissionOptions(): array
    {
        $policiesPath = app_path('Policies');
        $files = glob($policiesPath . '/*.php');
        $permissions = [];
        foreach ($files as $file)
        {
            $className = 'App\\Policies\\' . basename($file, '.php');
            if (!class_exists($className))
            {
                require_once $file;
            }
            if (!class_exists($className))
            {
                continue;
            }
            $resource = strtolower(preg_replace('/Policy$/', '', class_basename($className)));
            $methods = get_class_methods($className);
            foreach ($methods as $method)
            {
                if (!isset(self::$methodDescriptions[$method])) continue;
                $key = "$resource.$method";
                $label = ucfirst($resource) . ':' . self::$methodDescriptions[$method];
                $permissions[$key] = $label;
            }
        }
        return $permissions;
    }

    public static function flattenExclusionList(): array
    {
        $result = [];
        foreach (self::$exclusionList as $module => $permissions)
        {
            foreach ($permissions as $permission)
            {
                $result[] = "$module.$permission";
            }
        }
        return $result;
    }

    public static function getAvailablePermissions(): array
    {
        $permissions = [];
        $allPermissions = self::getPolicyPermissionOptions();
        $excluded = self::flattenExclusionList();
        foreach ($allPermissions as $key => $value)
        {
            if (in_array($key, $excluded))
            {
                continue;
            }
            $permissions[$key] = $value;
        }
        return $permissions;
    }

    public static bool $skipObserver = false;
}
