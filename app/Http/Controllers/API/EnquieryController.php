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
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $enquiery = Enquiery::all();
        if($enquiery->count() > 0){
            return $this->sendResponse(EnquieryResource::collection($enquiery), 'Enquiery retrieved successfully.');
        }else{
            return $this->sendError([], 'Enquiery not found.');
        }
        

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function add1111(Request $request)
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

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
         $originalDate = $input['Departure'];
         $input['Departure'] = date("Y-m-d", strtotime($originalDate));

          $Enquiery = Enquiery::create($input);
          $input['Departure'] = $originalDate;
          Mail::send('emails.enquieryemail', array(
            'PersonName' => $input['PersonName'],
            'MobileNo' => $input['MobileNo'],
            'EmailId' => $input['EmailId'],
            'Destination' => $input['Destination'],
            'Noper' => $input['Noper'],
            'Departure' => $originalDate,
        ), function($message) use ($input){
            $message->to('enquiry@ghumao.in');
            $message->from($input['EmailId'], 'Enquiry')->subject('Ghumao Enquiry');
        });
        // Mail::send('emails.enquieryemail', array(
        //     'PersonName' => $input['PersonName'],
        //     'MobileNo' => $input['MobileNo'],
        //     'EmailId' => $input['EmailId'],
        //     'Destination' => $input['Destination'],
        //     'Noper' => $input['Noper'],
        //     'Departure' => $originalDate,
        // ), function($message) use ($input){
        //     $message->to('enquiry@ghumao.in');
        //     $message->from('development@ghumao.in', 'Enquiry')->subject('Ghumao Inquiry');
        // });
        // Mail::send('emails.enquieryemail', array(
        //     'PersonName' => $input['PersonName'],
        //     'MobileNo' => $input['MobileNo'],
        //     'EmailId' => $input['EmailId'],
        //     'Destination' => $input['Destination'],
        //     'Noper' => $input['Noper'],
        //     'Departure' => $originalDate,
        // ), function($message) use ($input){
        //     $message->to($input['EmailId']);
        //     $message->from('development@ghumao.in', 'Enquiry')->subject('Ghumao Inquiry');
        // });
        // Mail::raw('Thank!,We have received your Inquiry', function ($message) use ($input){
        //   $message->to($input['EmailId'])
        //     ->subject('Ghumao Inquiry');
        // }); 
         // Send a confirmation email to the user (this is where we change the sender)
    Mail::raw('Thank you! We have received your inquiry and will get back to you soon.', function ($message) use ($input) {
        $message->to($input['EmailId'])               // Send to the user's email
                ->from('enquiry@ghumao.in', 'Ghumao Enquiry') // Set the sender as 'enquiry@ghumao.in'
                ->subject('Ghumao Enquiry');          // Set the subject of the email
    });
        return $this->sendResponse('','Enquiry created successfully.');
        
    } 
    
    public function addold(Request $request)
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

    if($validator->fails()){
        return $this->sendError('Validation Error.', $validator->errors());
    }

    $originalDate = $input['Departure'];
    $input['Departure'] = date("Y-m-d", strtotime($originalDate));

    // Create the enquiry entry
    $Enquiery = Enquiery::create($input);
    $input['Departure'] = $originalDate;

    // Check if the enquiry is for "NEW USERS SPECIAL 10% OFF"
    if (($input['PersonName'] == $input['Destination']) && $input['PersonName'] == 'NEW USERS SPECIAL 10% OFF') {
        // Send the enquiry to Ghumao team
        Mail::send('emails.enquirysubscribe', [
            'PersonName' => $input['PersonName'],
            'MobileNo' => $input['MobileNo'],
            'EmailId' => $input['EmailId'],
            'Destination' => $input['Destination'],
            'Noper' => $input['Noper'],
            'Departure' => $originalDate,
        ], function($message) use ($input) {
            $message->to('enquiry@ghumao.in');
            $message->from($input['EmailId'], 'Enquiry')->subject('Ghumao Enquiry');
        });

        // Send a reply email to the user for signup
        Mail::send('emails.replyforsignup', [
            'PersonName' => $input['PersonName'],
            'MobileNo' => $input['MobileNo'],
            'EmailId' => $input['EmailId'],
            'Destination' => $input['Destination'],
            'Noper' => $input['Noper'],
            'Departure' => $originalDate,
        ], function($message) use ($input) {
            $message->to($input['EmailId']);
            $message->from('enquiry@ghumao.in', 'Enquiry')->subject('Ghumao Enquiry');
        });

    } else {
        // Send enquiry email to Ghumao team
        Mail::send('emails.enquieryemail', [
            'PersonName' => $input['PersonName'],
            'MobileNo' => $input['MobileNo'],
            'EmailId' => $input['EmailId'],
            'Destination' => $input['Destination'],
            'Noper' => $input['Noper'],
            'Departure' => $originalDate,
        ], function($message) use ($input) {
            $message->to('enquiry@ghumao.in');
            $message->from($input['EmailId'], 'Enquiry')->subject('Ghumao Enquiry');
        });

        // Send a confirmation email to the user
        Mail::raw('Thank you! We have received your enquiry and will get back to you soon.', function ($message) use ($input) {
            $message->to($input['EmailId']) 
                    ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                    ->subject('Ghumao Enquiry');
        });
    }

    return $this->sendResponse('', 'Enquiry created successfully.');
}

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

    // Save enquiry
    $Enquiery = Enquiery::create($input);
    $input['Departure'] = $originalDate;

    try {

        if (($input['PersonName'] == $input['Destination']) && $input['PersonName'] == 'NEW USERS SPECIAL 10% OFF') {

            // Admin mail
            Mail::send('emails.enquirysubscribe', [
                'PersonName' => $input['PersonName'],
                'MobileNo' => $input['MobileNo'],
                'EmailId' => $input['EmailId'],
                'Destination' => $input['Destination'],
                'Noper' => $input['Noper'],
                'Departure' => $originalDate,
            ], function($message) use ($input) {
                $message->to('enquiry@ghumao.in')
                        ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                        ->replyTo($input['EmailId'])
                        ->subject('Ghumao Enquiry');
            });

            // User mail
            Mail::send('emails.replyforsignup', [
                'PersonName' => $input['PersonName'],
                'MobileNo' => $input['MobileNo'],
                'EmailId' => $input['EmailId'],
                'Destination' => $input['Destination'],
                'Noper' => $input['Noper'],
                'Departure' => $originalDate,
            ], function($message) use ($input) {
                $message->to($input['EmailId'])
                        ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                        ->subject('Ghumao Enquiry');
            });

        } else {

            // Admin mail
            Mail::send('emails.enquieryemail', [
                'PersonName' => $input['PersonName'],
                'MobileNo' => $input['MobileNo'],
                'EmailId' => $input['EmailId'],
                'Destination' => $input['Destination'],
                'Noper' => $input['Noper'],
                'Departure' => $originalDate,
            ], function($message) use ($input) {
                $message->to('enquiry@ghumao.in')
                        ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                        ->replyTo($input['EmailId'])
                        ->subject('Ghumao Enquiry');
            });

            // ✅ FIXED USER EMAIL (still raw but improved)
            Mail::raw(
                "Hi ".$input['PersonName'].",\n\nThank you! We have received your enquiry and will get back to you soon.\n\nRegards,\nGhumao Team",
                function ($message) use ($input) {
                    $message->to($input['EmailId'])
                            ->from('enquiry@ghumao.in', 'Ghumao Enquiry')
                            ->replyTo('enquiry@ghumao.in')
                            ->subject('Ghumao Enquiry Confirmation');
                }
            );
        }

    } catch (\Exception $e) {
        \Log::error('Mail Error: ' . $e->getMessage());

        return response()->json([
            'success' => true,
            'message' => 'Enquiry saved but email failed.'
        ], 200);
    }

    return $this->sendResponse('', 'Enquiry created successfully.');
}
    
}