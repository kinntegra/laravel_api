<?php

namespace App\Models;

use App\Models\Associate\Associate;
use App\Models\Associate\Employee;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Services\MyServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    const ACTIVE_USER = '1';
    const INACTIVE_USER = '0';


    protected $table = 'users';

    public static function boot()
    {
        parent::boot();
        // create a event to happen on saving
        // static::saving(function($table)  {
        //     $table->created_by = Auth::user()->id;
        // });
        // create a event to happen on deleting
        // static::deleting(function($table)  {
        //     $table->deleted_by = Auth::user()->username;
        // });

        //create a event to hpeen on creating
        static::creating(function ($user) {
            $user->updated_by = Auth::user()->id;
            $user->created_by = Auth::user()->id;
        });

        // create a event to happen on updating
        static::updating(function($table)  {
            if (Auth::user()) {
            $table->updated_by = Auth::user()->id;
            }
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'username',
        'password',
        'pin',
        'is_active',
        'is_first',
        'in_house',
        'activation_token',
        'last_login_at',
        'last_login_ip',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    //protected $casts = [
    //    'email_verified_at' => 'datetime',
    //];

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findForPassport($username)
    {
        $username = MyServices::getEncryptedString(strtoupper($username));
        return $this->where('username', $username)->first();
    }


    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param  string  $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'username';
    }

    public function hasPanNo($pan_no)
    {
        return $this->all()->where('username', $pan_no)->first() ? true : false;
    }


    public function isActive()
    {
        return $this->active  == User::ACTIVE_USER;
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

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
     * Set name attribute in lower case
     *
     */
    public function setNameAttribute($name)
    {
        $this->attributes['name'] = strtolower($name);
    }

    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setEmailAttribute($email)
    {
        $this->attributes['email'] = MyServices::getEncryptedString(strtolower($email));
    }

    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setMobileAttribute($mobile)
    {
        $this->attributes['mobile'] = MyServices::getEncryptedString($mobile);
    }

    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setPinAttribute($pin)
    {
        // if(!$pin){
        //     $pin = substr($this->mobile, -6);
        // }
        if(!$pin){
            $pin = '123456';
        }
        $this->attributes['pin'] = MyServices::getEncryptedString($pin);
    }

    /**
     * Encrypt the Pan Number before insert in to the database
     *
     */
    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = MyServices::getEncryptedString(strtoupper($username));
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getNameAttribute($name)
    {
        return ucwords($name);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getEmailAttribute($email)
    {
        return MyServices::getDecryptedString($email);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getMobileAttribute($mobile)
    {
        return MyServices::getDecryptedString($mobile);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getPinAttribute($pin)
    {
        return MyServices::getDecryptedString($pin);
    }

    /**
     * Generate the Activation Code
     *
     */
    public static function generateActivationCode()
    {
        return Str::random(40);
    }

    /**
     * Relationship Between User and Associate 1-1
     */
    public function associate()
    {
        return $this->hasOne(Associate::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function hasRole(array $roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    public function hasAccess(array $permissions)
    {
        foreach($this->roles as $role)
        {
            if($role->hasAccess($permissions))
            {
                return true;
            }
        }
        //return $this->setAccess($permissions);
        return false;
    }

    public function setAccess(array $permissions)
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
}
