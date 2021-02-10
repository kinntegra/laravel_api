<?php

namespace App\Models\associate;

use App\Models\Associate\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContact extends Model
{
    use HasFactory;

    /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
    protected $guarded = [];


    public function employees()
    {
        return $this->belongsTo(Employee::class);
    }
}
