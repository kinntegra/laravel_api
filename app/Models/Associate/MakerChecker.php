<?php

namespace App\Models\Associate;

use App\Models\Master\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakerChecker extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    public function assoicate()
    {
        return $this->belongsTo(Associate::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function makercheckerlogs()
    {
        return $this->hasMany(MakerCheckerLog::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
