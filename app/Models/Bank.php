<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    const BANK_UPLOAD = 'cheque_upload';

    const BANK_OTHER_UPLOAD = 'mfd_ria_cheque_upload';

    const BANK_DEFAULT = '1';

    const BANK_NOT_DEFAULT = '0';

    protected $dates = ['deleted_at'];

    protected $table = 'banks';

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    public function bankable()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }

}
