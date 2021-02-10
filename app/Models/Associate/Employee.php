<?php

namespace App\Models\Associate;

use App\Models\Address;
use App\Models\Associate\Associate;
use App\Models\Associate\Certificate;
use App\Models\associate\EmployeeContact;
use App\Models\Bank;
use App\Models\File;
use App\Models\User;
use App\Services\MyServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,SoftDeletes;

    const PATH = 'storage';
    const STORE_PATH = 'employee';
    const MODELNAME = 'Employee';
    const EMPLOYEE_ROLE = 'employee';
    const FIRST_CONTACT = '1';
    const SECOND_CONTACT = '2';
    const EMPLOYEE_PHOTO = 'photo_upload';
    const EMPLOYEE_PAN = 'pan_upload';
    const EMPLOYEE_AADHAR = 'aadhar_upload';
    const EMPLOYEE_MODEL = 'App\Models\Associate\Employee';//ASSOCIATE_CERTIFICATE_TYPE
    const CERFITICATE_FILE = 'App\Models\Associate\Certificate';
    const CORRESPONDENCE_ADDRESS_TYPE = '1';
    const PARMANENT_ADDRESS_TYPE = '4';

    const EUIN_UPLOAD = 'euin_upload';

    const NISM_VA_UPLOAD = 'nism_va_upload';
    const NISM_XA_UPLOAD = 'nism_xa_upload';
    const NISM_XB_UPLOAD = 'nism_xb_upload';
    const CFP_UPLOAD = 'cfp_upload';
    const CWM_UPLOAD = 'cwm_upload';
    const CA_UPLOAD = 'ca_upload';
    const CS_UPLOAD = 'cs_upload';
    const COURSE_UPLOAD = 'course_upload';

    protected $dates = ['deleted_at'];

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];


    /**
     *  Create User Module
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Get assocaite of the reference Employee
     */
    public function associate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function employeeContant()
    {
        return $this->hasMany(EmployeeContact::class);
    }

    public function employeeLicence()
    {
        return $this->hasOne(EmployeeLicence::class);
    }

    public function employeeCertificate()
    {
        //return $this->hasOne(AssociateCertificate::class);
        return $this->morphOne(Certificate::class, 'certificateable');
    }

    public function employeeMakerChecker()
    {
        return $this->morphOne(MakerChecker::class, 'makercheckerable');
    }

    public function employeeLogs()
    {
        return $this->morphMany(Managelog::class, 'managelogable');
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function bank()
    {
        return $this->morphOne(Bank::class, 'bankable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
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
     *  Enctrypt and Decrypt Function for PAN NO
     *
     */
    public function setPanNoAttribute($pan_no)
    {
        $this->attributes['pan_no'] = MyServices::getEncryptedString(strtoupper($pan_no));
    }

    public function getPanNoAttribute($pan_no)
    {
        return MyServices::getDecryptedString($pan_no);
    }

    /**
     * Enctrypt and Decrypt Function for Aadhar NO
     *
     */
    public function setAadharNoAttribute($aadhar_no)
    {
        $this->attributes['aadhar_no'] = MyServices::getEncryptedString($aadhar_no);
    }
    public function getAadharNoAttribute($aadhar_no)
    {
        return MyServices::getDecryptedString($aadhar_no);
    }

    /**
     * Enctrypt and Decrypt Function for Telophone NO
     *
     */
    public function setTelephoneAttribute($telephone)
    {
        $this->attributes['telephone'] = MyServices::getEncryptedString($telephone);
    }
    public function getTelephoneAttribute($telephone)
    {
        return MyServices::getDecryptedString($telephone);
    }

}
