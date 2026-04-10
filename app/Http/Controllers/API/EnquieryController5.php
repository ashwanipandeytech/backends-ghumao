<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Enquiery;
use Validator;
use App\Http\Resources\Enquiery as EnquieryResource;
use Illuminate\Support\Facades\Auth;
use Mail;

class EnquieryController extends BaseController
{

    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $enquiery = Enquiery::all();

        if ($enquiery->count() > 0) {
            return $this->sendResponse(EnquieryResource::collection($enquiery), 'Enquiery retrieved successfully.');
        } else {
            return $this->sendError([], 'Enquiery not found.');
        }
    }


    /**
     * Store enquiry (old function)
     */

    public function add1(Request $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'PersonName' => 'required',
            'EmailId' => 'required|email',
            'MobileNo' => 'required',
            'Destination' => 'required',
            'Noper' => 'required',
            'Departure' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $originalDate = $input['Departure'];
        $input['Departure'] = date("Y-m-d", strtotime($originalDate));

        $Enquiery = Enquiery::create($input);

        $input['Departure'] = $originalDate;

        /*
        |-----------------------------------------
        | Send email to Owner
        |-----------------------------------------
        */

        Mail::send('emails.enquieryemail', [
            'PersonName' => $input['PersonName'],
            'MobileNo' => $input['MobileNo'],
            'EmailId' => $input['EmailId'],
            'Destination' => $input['Destination'],
            'Noper' => $input['Noper'],
            'Departure' => $originalDate,
        ], function ($message) use ($input) {

            $message->to('enquiry@ghumao.in')
                ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                ->replyTo($input['EmailId'], $input['PersonName'])
                ->subject('Ghumao Enquiry');

        });

        /*
        |-----------------------------------------
        | Send confirmation email to user
        |-----------------------------------------
        */

        Mail::raw('Thank you! We have received your inquiry and will get back to you soon.', function ($message) use ($input) {

            $message->to($input['EmailId'])
                ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                ->subject('Ghumao Enquiry');

        });

        return $this->sendResponse('', 'Enquiry created successfully.');
    }



    /**
     * Main enquiry function
     */

    public function add(Request $request)
    {

        $input = $request->all();

        $validator = Validator::make($input, [
            'PersonName' => 'required',
            'EmailId' => 'required|email',
            'MobileNo' => 'required',
            'Destination' => 'required',
            'Noper' => 'required',
            'Departure' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $originalDate = $input['Departure'];
        $input['Departure'] = date("Y-m-d", strtotime($originalDate));

        $Enquiery = Enquiery::create($input);

        $input['Departure'] = $originalDate;


        /*
        |-----------------------------------------
        | SPECIAL OFFER CONDITION
        |-----------------------------------------
        */

        if (($input['PersonName'] == $input['Destination']) && $input['PersonName'] == 'NEW USERS SPECIAL 10% OFF') {

            /*
            | Send email to Owner
            */

            Mail::send('emails.enquirysubscribe', [
                'PersonName' => $input['PersonName'],
                'MobileNo' => $input['MobileNo'],
                'EmailId' => $input['EmailId'],
                'Destination' => $input['Destination'],
                'Noper' => $input['Noper'],
                'Departure' => $originalDate,
            ], function ($message) use ($input) {

                $message->to('enquiry@ghumao.in')
                    ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                    ->replyTo($input['EmailId'], $input['PersonName'])
                    ->subject('Ghumao Enquiry');

            });


            /*
            | Send reply to User
            */

            Mail::send('emails.replyforsignup', [
                'PersonName' => $input['PersonName'],
                'MobileNo' => $input['MobileNo'],
                'EmailId' => $input['EmailId'],
                'Destination' => $input['Destination'],
                'Noper' => $input['Noper'],
                'Departure' => $originalDate,
            ], function ($message) use ($input) {

                $message->to($input['EmailId'])
                    ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                    ->subject('Ghumao Enquiry');

            });

        } else {

            /*
            | Send enquiry email to Owner
            */

            Mail::send('emails.enquieryemail', [
                'PersonName' => $input['PersonName'],
                'MobileNo' => $input['MobileNo'],
                'EmailId' => $input['EmailId'],
                'Destination' => $input['Destination'],
                'Noper' => $input['Noper'],
                'Departure' => $originalDate,
            ], function ($message) use ($input) {

                $message->to('enquiry@ghumao.in')
                    ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                    ->replyTo($input['EmailId'], $input['PersonName'])
                    ->subject('Ghumao Enquiry');

            });


            /*
            | Send confirmation email to user
            */

            Mail::raw('Thank you! We have received your enquiry and will get back to you soon.', function ($message) use ($input) {

                $message->to($input['EmailId'])
                    ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                    ->subject('Ghumao Enquiry');

            });

        }

        return $this->sendResponse('', 'Enquiry created successfully.');
    }

}