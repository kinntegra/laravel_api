<?php

namespace App\Http\Controllers;

use App\Mail\SupervisiorNotification;
use App\Models\Associate\Associate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PDF;

class HomeController extends ApiController
{
    public function __construct()
    {
        //parent::__construct();
    }


    public function index(Request $request)
    {
        $fileName = 'empty';
        if($request->hasFile('photo_upload'))
        {
            $file = $request->photo_upload;
            //$fileName = Storage::disk()->put('', $file);//AJbbcapYkEPbT8UVKVbT2hVVtG9R73pHKS0TUgmF.png
            $fileName = Storage::disk()->put('', $file);

        }
        return $this->showMessage($fileName);

    }

    public function generatePDF()
    {
        $data = [
            'title' => 'First PDF for Medium',
            'heading' => 'Hello from 99Points.info',
            'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.'
              ];

          $pdf = PDF::loadView('pdf_view', $data);
          return $pdf->download('medium.pdf');

    }

    public function testEmail()
    {
        $link = 'http://127.0.0.1:8001/associate/test';
        $data = [
            'name' => 'shasihkant',
            'link' => env('APP_WEBURL').'/'.$link,
            'message1' => 'Associate Shashikant has accepted the request.',
            'message2' => 'Now the Associate is active',
        ];
        // $subject = 'Email From Kinntegra';

        // $email = 'shashivarma88@gmail.com';

        // Mail::to($email)->send(new SupervisiorNotification($data, $subject));
        return view('emails.supervisior')->with(['data' => $data]);
        dd('email send Successfully');
    }

    public function download($id)
    {
        //        1   Terminal ID
        //        2   Login id
        //        3   branch id
        //        4   password
        //        5   Name
        //        6   Subbroker code
        //        7   Sub broker ARN code
        //        8   Subbrooker EUIN
        //        9   Address1
        //        10  Address2
        //        11  Address3
        //        12  City
        //        13  State
        //        14  Pin
        //        15  Country
        //        16  Phone
        //        17  mobile
        //        18  fax
        //        19  email
        //        20  Access level
        $data = "{1}|{2}|{3}|{4}|{5}|{6}|{7}|{8}|{9}|{10}|{11}|{12}|{13}|{14}|{15}|{16}|{17}|{18}|{19}|{20}";

        $associate = Associate::find($id);
        $associate->user;
        if ($associate != null)
        {
            $data = str_replace('{1}', 'DEALER', $data);
            $data = str_replace('{2}', $associate->username, $data);
            $data = str_replace('{3}', 'CORPBRANCH', $data);
            $data = str_replace('{4}', $associate->password, $data);
            $data = str_replace('{5}', $associate->name, $data);
            $data = str_replace('{6}', $associate->username, $data);
            $data = str_replace('{7}', $associate->arn, $data);
            $data = str_replace('{8}', $associate->euin, $data);
            $data = str_replace('{9}', $associate->address1, $data);
            $data = str_replace('{10}', $associate->address2, $data);
            $data = str_replace('{11}', $associate->address3, $data);
            $data = str_replace('{12}', $associate->city, $data);
            $data = str_replace('{13}', $associate->state, $data);
            $data = str_replace('{14}', $associate->pincode, $data);
            $data = str_replace('{15}', 'India', $data);
            $data = str_replace('{16}', $associate->phone, $data);
            $data = str_replace('{17}', $associate->mobile, $data);
            $data = str_replace('{18}', '', $data);
            $data = str_replace('{19}', $associate->email_sales, $data);
            $data = str_replace('{20}', 'F', $data);
        }
        //dd($associate->user->name);
        if (Storage::exists('/associatedocuments/' . $id . '/' . $associate->user->name . '.txt'))
        {
            Storage::delete('/associatedocuments/' . $id . '/' . $associate->user->name . '.txt');
        }

        Storage::put('/associatedocuments/' . $id . '/' . $associate->user->name . '.txt', $data);

        return Storage::download('/associatedocuments/' . $id . '/' . $associate->user->name . '.txt');

    }
}
