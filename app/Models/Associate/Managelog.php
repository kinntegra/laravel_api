<?php

namespace App\Models\Associate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Managelog extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];

    public function assoicates()
    {
        return $this->belongsTo(Associate::class);
    }

    public function employees()
    {
        return $this->belongsTo(Employee::class);
    }
}
