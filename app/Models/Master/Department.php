<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    const DEFAULT_ACTIVE_YES = '1';
    const DEFAULT_ACTIVE_NO = '0';

    protected $dates = ['deleted_at'];

    protected $table = 'departments';

    public static function boot()
    {
        parent::boot();
        //create a event to hapeen on creating
        static::creating(function ($table) {
            $table->updated_by = Auth::user()->id;
            $table->created_by = Auth::user()->id;
        });

        // create a event to happen on updating
        static::updating(function($table)  {
            $table->updated_by = Auth::user()->id;
        });
    }

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

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParent($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeSubparent($query, $id)
    {
        return $query->where('parent_id', $id);
    }

    public function setNameAttribute($name)
    {
        $this->attributes['slug'] = strtolower(Str::slug($name));

        $this->attributes['name'] = $name;
    }
}
