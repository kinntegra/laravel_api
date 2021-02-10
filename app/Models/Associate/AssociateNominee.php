<?php

namespace App\Models\Associate;

use App\Models\Address;
use App\Services\MyServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociateNominee extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    /**
     * Enctrypt Function for Telophone NO
     *
     */
    public function setNomineeTelephoneAttribute($nominee_telephone)
    {
        $this->attributes['nominee_telephone'] = MyServices::getEncryptedString($nominee_telephone);
    }

    /**
     * Decrypt Function for Telophone NO
     *
     */
    public function getNomineeTelephoneAttribute($nominee_telephone)
    {
        return MyServices::getDecryptedString($nominee_telephone);
    }


    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setNomineeEmailAttribute($nominee_email)
    {
        $this->attributes['nominee_email'] = MyServices::getEncryptedString(strtolower($nominee_email));
    }

    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setNomineeMobileAttribute($nominee_mobile)
    {
        $this->attributes['nominee_mobile'] = MyServices::getEncryptedString($nominee_mobile);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getNomineeEmailAttribute($nominee_email)
    {
        return MyServices::getDecryptedString($nominee_email);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getNomineeMobileAttribute($nominee_mobile)
    {
        return MyServices::getDecryptedString($nominee_mobile);
    }

    public function assoicate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function assoicateGuardian()
    {
        return $this->hasOne(AssociateGuradian::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

}
