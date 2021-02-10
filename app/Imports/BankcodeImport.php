<?php

namespace App\Imports;

use App\Models\Master\Bankcode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankcodeImport implements ToModel, WithHeadingRow
{

    /**
     * @return int
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //dd($row);
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
