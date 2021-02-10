<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bankcode extends Model
{
    use HasFactory;

    protected $table = 'bankcodes';

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'ifsc';
    }
}
