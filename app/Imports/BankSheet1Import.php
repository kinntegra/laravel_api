<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Master\Bankcode;

class BankSheet1Import implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $row)
    {
        //dd($row['bank']);
        return new Bankcode([
            'bank' => $row['bank'],
            'ifsc' => $row['ifsc'],
            'micr_code' => $row['micr_code'],
            'branch' => $row['branch'],
            // 'address' => $row['address'],
            // 'std_code' => $row['std_code'],
            // 'contact' => $row['contact'],
            // 'city' => $row['city'],
            // 'district' => $row['district'],
            // 'state' => $row['state'],
        ]);
    }
}
