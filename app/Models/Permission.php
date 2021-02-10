<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    const PARENT_ID = '0';

    protected $dates = ['deleted_at'];

    protected $table = 'permissions';
    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at','pivot',
    ];
    protected $guarded = [];

    public function roles()
    {
    	return $this->belongsToMany(Role::class, 'permission_role');
    }

    public function hasAccess(array $permissions)
    {
        foreach($permissions as $permission)
        {
            if($this->hasPermission($permission))
            {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(string $permission) : bool
    {
        return $this->name == $permission;
    }

    /**
     * Get the index name for the model.
     *
     * @return string
    */
    public function childs() {
        return $this->hasMany(Permission::class,'parent_id','id') ;
    }
}
