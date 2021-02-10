<?php

namespace App\Imports;

use App\Models\Master\Bankcode;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BankImport implements WithMultipleSheets
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function sheets(): array
    {
        return [
            new BankSheet1Import(),
            //1 => new BankSheet2Import(),
        ];
    }
}
