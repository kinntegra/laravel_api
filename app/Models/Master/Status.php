<?php

namespace App\Models\Master;

use App\Models\Associate\MakerChecker;
use App\Models\Associate\MakerCheckerLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'statuses';

    protected $guarded = [];

    use HasFactory;

    const NEW_ASSOCIATE = 'Associate Creation Pending';
    const NEW_ASSOCIATE_STATUS = '1';
    //badge.badge-info

    // const ASSOCIATE_REUPDATE_STATUS = '1';
    // const ASSOCIATE_REUPDATE_COMMENT = '';

    const ASSOCIATE_SUPERVISIOR_STATUS = '2';
    const ASSOCIATE_SUPERVISIOR_COMMENT = 'Pending for Supervisior Verification';
    //badge.badge-info

    const ASSOCIATE_SUPERVISIOR_STATUS_APPROVED = '3';
    const ASSOCIATE_SUPERVISIOR_COMMENT_APPROVED = 'Approved By Supervisior';
    //badge.badge-warning

    const ASSOCIATE_SUPERVISIOR_STATUS_REJECT = '4';
    const ASSOCIATE_SUPERVISIOR_COMMENT_REJECT = 'Rejected By Supervisior';
    //badge.badge-danger

    const ASSOCIATE_USER_SUPERVISIOR_STATUS = '5';
    const ASSOCIATE_USER_SUPERVISIOR_COMMENT = 'Pending for Associate Verification';
    //badge.badge-warning

    const ASSOCIATE_USER_STATUS_APPROVED = '6';
    const ASSOCIATE_USER_COMMENT_APPROVED = 'Associate Approved';
    //badge badge-success badge-pill

    const ASSOCIATE_USER_STATUS_REJECT = '7';
    const ASSOCIATE_USER_COMMENT_REJECT = 'Associate Rejected';
    //badge.badge-danger

    const USER_STATUS_ACTIVE = '8';
    const USER_COMMENT_ACTIVE = 'Active';
    //badge badge-success badge-pill

    const USER_STATUS_INACTIVE = '9';
    const USER_COMMENT_INACTIVE = 'In Active';
    //badge.badge-default

    const USER_BSE_UPLOAD_STATUS = '10';
    const USER_BSE_UPLOAD_COMMENT = 'Pending for BSE Upload';
    //badge.badge-warning
//----------------------------------------------------------------------
    const NEW_EMPLOYEE = 'Employee Creation Pending';
    const NEW_EMPLOYEE_STATUS = '1';

    const EMPLOYEE_SUPERVISIOR_STATUS = '2';
    const EMPLOYEE_SUPERVISIOR_COMMENT = 'Pending for Supervisior Verification';

    const EMPLOYEE_SUPERVISIOR_STATUS_APPROVED = '3';
    const EMPLOYEE_SUPERVISIOR_COMMENT_APPROVED = 'Supervisior Approved';

    const EMPLOYEE_SUPERVISIOR_STATUS_REJECT = '4';
    const EMPLOYEE_SUPERVISIOR_COMMENT_REJECT = 'Supervisior Rejected';

    const EMPLOYEE_USER_SUPERVISIOR_STATUS = '5';
    const EMPLOYEE_USER_SUPERVISIOR_COMMENT = 'Pending for Employee Verification';

    const EMPLOYEE_USER_STATUS_APPROVED = '6';
    const EMPLOYEE_USER_COMMENT_APPROVED = 'Employee Approved';

    const EMPLOYEE_USER_STATUS_REJECT = '7';
    const EMPLOYEE_USER_COMMENT_REJECT = 'Employee Rejected';

    public function makercheckers()
    {
        return $this->hasMany(MakerChecker::class);
    }

    public function makercheckerlog()
    {
        return $this->hasMany(MakerCheckerLog::class);
    }
}
