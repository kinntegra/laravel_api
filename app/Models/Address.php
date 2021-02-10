<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    const ADDRESS_UPLOAD = 'address_upload';
    const CORRESPONDENCE_ADDRESS_UPLOAD = 'c_address_upload';
    const PARMANENT_ADDRESS_UPLOAD = 'p_address_upload';

    const CORRESPONDENCE_ID = '1';
    const PARMANENT_ID = '4';

    protected $dates = ['deleted_at'];

    protected $table = 'addresses';
    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
