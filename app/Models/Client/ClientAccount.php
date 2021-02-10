<?php

namespace App\Models\Client;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAccount extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    const DEFAULT_ADDRESS_TYPE = 'App\Models\Client\ClientAccount';

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
