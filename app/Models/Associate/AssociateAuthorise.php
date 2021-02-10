<?php

namespace App\Models\Associate;

use App\Services\MyServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociateAuthorise extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];


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

    public function assoicates()
    {
        return $this->belongsTo(Associate::class);
    }
}
