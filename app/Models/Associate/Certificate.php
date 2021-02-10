<?php

namespace App\Models\Associate;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
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

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function certificateable()
    {
        return $this->morphTo();
    }
}
