<?php

namespace App\Models;

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

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationships
    public function policies()
    {
        return $this->belongsToMany(Policy::class, 'permissions_policies');
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
    ];

    private static array $exclusionList = [
        //
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
            $resource = strtolower(str_replace('Policy', '', class_basename($className)));
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
}
