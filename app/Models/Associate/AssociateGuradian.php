<?php

namespace App\Models\Associate;

use App\Models\Address;
use App\Models\File;
use App\Services\MyServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociateGuradian extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    /**
     *  Enctrypt Function for PAN NO
     *
     */
    public function setGuardianPanNoAttribute($guardian_pan_no)
    {
        $this->attributes['guardian_pan_no'] = MyServices::getEncryptedString(strtoupper($guardian_pan_no));
    }

    /**
     *  Decrypt Function for PAN NO
     *
     */
    public function getGuardianPanNoAttribute($guardian_pan_no)
    {
        return MyServices::getDecryptedString($guardian_pan_no);
    }

    /**
     * Enctrypt Function for Telophone NO
     *
     */
    public function setGuardianTelephoneAttribute($guardian_telephone)
    {
        $this->attributes['guardian_telephone'] = MyServices::getEncryptedString($guardian_telephone);
    }

    /**
     * Decrypt Function for Telophone NO
     *
     */
    public function getGuardianTelephoneAttribute($guardian_telephone)
    {
        return MyServices::getDecryptedString($guardian_telephone);
    }


    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setGuardianEmailAttribute($guardian_email)
    {
        $this->attributes['guardian_email'] = MyServices::getEncryptedString(strtolower($guardian_email));
    }

    /**
     * Encrypt the Email before insert in to the database
     *
     */
    public function setGuardianMobileAttribute($guardian_mobile)
    {
        $this->attributes['guardian_mobile'] = MyServices::getEncryptedString($guardian_mobile);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getGuardianEmailAttribute($guardian_email)
    {
        return MyServices::getDecryptedString($guardian_email);
    }

    /**
     * Display name attribute in below format
     *
     */
    public function getGuardianMobileAttribute($guardian_mobile)
    {
        return MyServices::getDecryptedString($guardian_mobile);
    }



    public function assoicateNominee()
    {
        return $this->belongsTo(AssociateNominee::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
