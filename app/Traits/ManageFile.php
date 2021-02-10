<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use App\Models\File;

trait ManageFile
{
    private $fileData = [
        'name',
        'fieldname',
        'path',
        'mime_type',
        'created_at',
        'updated_at'
    ];

    protected function storeFile($file, $fieldname, $storepath, $path, $id)
    {
        $db = File::where('fieldname', $fieldname)->where('fileable_id',$id)->first();

        if($db)
        {
            if(Storage::disk()->exists($db->name))
            {
                Storage::disk()->delete($db->name);
            }
        }

        $fileName = Storage::disk()->put($storepath, $file);
        $mime_type = $file->getMimeType();


        $data = [
            'name' => $fileName,
            'fieldname' => $fieldname,
            'path' => $path,
            'mime_type' => $mime_type,
        ];
        return $data;
    }
}
