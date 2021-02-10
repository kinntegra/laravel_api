<?php

namespace App\Models\Associate;

use App\Models\Master\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakerCheckerLog extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    public function makerchecker()
    {
        return $this->belongsTo(MakerChecker::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
