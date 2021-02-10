<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    const DEFAULT_ACTIVE_YES = '1';
    const DEFAULT_ACTIVE_NO = '0';

    protected $dates = ['deleted_at'];

    protected $table = 'roles';
    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at','pivot',
    ];

    public function users()
	{
		return $this->belongsToMany(User::class, 'role_user');
	}

	public function permissions()
    {
    	return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function hasAccess(array $permissions)
    {
        foreach ($this->permissions as $permission)
        {
            if($permission->hasAccess($permissions))
            {
                return true;
            }
        }
        return false;
    }

    public function setNameAttribute($name)
    {
        $this->attributes['slug'] = strtolower($name);

        $this->attributes['name'] = $name;
    }
}
