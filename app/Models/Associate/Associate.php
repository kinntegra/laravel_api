<?php

namespace App\Models\Associate;

use App\Models\Address;
use App\Models\Bank;
use App\Models\File;
use App\Models\User;
use App\Services\MyServices;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Associate extends Model
{
    use HasFactory, SoftDeletes;

    const PATH = 'storage';
    const STORE_PATH = 'associate';
    const MODELNAME = 'Associate';
    const ASSOCIATE_ADMIN_ROLE = 'admin';
    const ASSOCIATE_UPLOAD = 'associateUpload';
    const ASSOCIATE_MORE_UPLOAD = 'associateMoreUpload';
    const ASSOCIATE_LICENCE_UPLOAD = 'associateLicenceUpload';
    const ASSOCIATE_ADDRESS_UPLOAD = 'associateAddressUpload';
    const ASSOCIATE_BANK_UPLOAD = 'associateBankUpload';
    const ASSOCIATE_NOMINEE_UPLOAD = 'associateNomineeUpload';
    const ASSOCIATE_CERTIFICATE_UPLOAD = 'associateCertificateUpload';
    //const ASSOCIATE_GURADIAN_UPLOAD = 'assoicateGuardianUpload';
    const GUARDIAN_PAN_UPLOAD = 'guardian_pan_upload';
    const FIRST_AUTHORISE = '1';
    const SECOND_AUTHORISE = '2';
    const THIRD_AUTHORISE = '3';

    const ASSOCIATE_PHOTO = 'photo_upload';
    const ASSOCIATE_PAN = 'pan_upload';
    const ASSOCIATE_AADHAR = 'aadhar_upload';
    const ASSOCIATE_MODEL = 'App\Models\Associate\Associate';
    const DEFAULT_ADDRESS_TYPE = '2';
    const ASSOCIATE_LOGO = 'logo_upload';
    const ASSOCIATE_GST = 'gst_upload';
    const ASSOCIATE_SHOP_EST = 'shop_est_upload';
    const ASSOCIATE_PD = 'pd_upload';
    const ASSOCIATE_PD_ASL = 'pd_asl_upload';
    const ASSOCIATE_PD_COI = 'pd_coi_upload';
    const ASSOCIATE_CO_MOA = 'co_moa_upload';
    const ASSOCIATE_CO_AOA = 'co_aoa_upload';
    const ASSOCIATE_CO_COI = 'co_coi_upload';
    const ASSOCIATE_CO_ASL = 'co_asl_upload';
    const ASSOCIATE_CO_BR =  'co_br_upload';

    const ARN_UPLOAD = 'arn_upload';
    const EUIN_UPLOAD = 'euin_upload';
    const RIA_UPLOAD = 'ria_upload';

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

    /**
     *  Associate Module belongs to User Module
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function associateLicence()
    {
        return $this->hasOne(AssociateLicence::class);
    }

    public function associateCertificate()
    {
        //return $this->hasOne(AssociateCertificate::class);
        return $this->morphOne(Certificate::class, 'certificateable');
    }

    public function associateMakerChecker()
    {
        return $this->morphOne(MakerChecker::class, 'makercheckerable');
    }

    public function associateLogs()
    {
        return $this->morphMany(Managelog::class, 'managelogable');
    }

    public function associateNominee()
    {
        return $this->hasOne(AssociateNominee::class);
    }

    public function associateAuthorises()
    {
        return $this->hasMany(AssociateAuthorise::class);
    }

    public function associateCommercials()
    {
        return $this->hasMany(AssociateCommercial::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get all of the deployments for the project.
     */
    // public function makerCheckerLogs()
    // {
    //     return $this->hasManyThrough(MakerCheckerLog::class, MakerChecker::class,'');
    // }

    public function banks()
    {
        return $this->morphMany(Bank::class, 'bankable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }


}
