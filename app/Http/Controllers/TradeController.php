<?php

namespace App\Http\Controllers;

use App\Lib\Transferwise;
use App\Mail\GeneralMail;
use App\TemporaryQuotes;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use Mail;
use View;
use Auth;
use Hash;

class TradeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function offer(Request $request)
    {
        // Get all countries
        $countries = DB::table('country_mst')->pluck('name', 'country_cd');

        $currencies = [
            'USD' => 'USD',
            'JPY' => 'JPY',
            'VND' => 'VND',
        ];
        $emailTemplates = [
            'default' => ['name' => 'デフォルト', 'template' => 'email.quotes.default']
        ];

        foreach ($emailTemplates as $i=>$tmp){
            $emailTemplates[$i]['content'] = View::make($tmp['template'])->render();
        }


        return view('trade.offer', compact('countries', 'emailTemplates', 'currencies'));
    }

    /**
     * Post a offer
     *
     * @return \Illuminate\Http\Response
     */
    public function postOffer(Request $request)
    {
        // Validate request data
        $this->validate($request, [
            'type' => 'required',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255'
        ]);

        if($request->get('check_email_submit')) {
            // Get parameters from request
            $type = $request->get('type');
            $email = $request->get('email');
            $firstName = $request->get('first_name');
            $lastName = $request->get('last_name');

            $randomCode = str_pad(rand(0, 999999), '0', STR_PAD_LEFT);

            // Create temporary quote with random code
            $temporaryQuotes = TemporaryQuotes::where(['email' => $email])->first();
            if(!$temporaryQuotes){
                $temporaryQuotes = new TemporaryQuotes([
                    'email' => $request->get('email'),
                ]);
            }
            $temporaryQuotes->code = $randomCode;
            $temporaryQuotes->save();

            // Send random code to user
            Mail::to($email)->send(new GeneralMail('temporary_quote_code', 'Authentication code', [
                'name' => $firstName. $lastName, 'code' => $randomCode
            ]));

            return redirect()->route('trade.offer')->with(['status'=>'email_checked', 'type'=> $type])->withInput();
        }

        if($request->get('confirm_submit')){
            $this->validate($request, [
                'partner_email' => 'required|string|email|max:255',
                'mail_subject' => 'required|string|max:255',
                'mail_content' => 'required',
                'passcode' => 'required|string',
            ]);

            // Confirm email and passcode
            $email = $request->get('email');
            $passcode = $request->get('passcode');
            $temporaryQuotes = TemporaryQuotes::where(['email' => $email, 'code' => $passcode])->first();
            if($temporaryQuotes){
                return redirect()->route('trade.confirm')->with(['data' => $request->all()]);
            }else{
                redirect()->route('trade.offer')->with(['error'=>'Email and Passcode are not matched. Please check again!'])->withInput();
            }

        }

        return redirect()->route('trade.offer')->with(['status'=>'email_checked'])->withInput();
    }

    /**
     * Get temporary Transferwise quotation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransferwiseQuote(Request $request){
        $source = $request->get('source');
        $target = $request->get('target');
        $amount = $request->get('amount');

        // Call Transferwise API to get temporary quotes
        $result = Transferwise::temporaryQuotes($source, $target, $amount);

        // Return response
        if($result){
            // Success
            if(empty($result['errors'])){
                $response = [
                    'status' => [
                        'code' => 0,
                        'message' => 'OK'
                    ],
                    'data' =>[
                        'fee' => $result['fee'],
                        'targetAmount' => $result['targetAmount'],
                        'sourceAmount' => $result['sourceAmount'],
                    ]
                ];
                // Get error result from Transferwise API
            }else{
                $response = [
                    'status' => [
                        'code' => 1,
                        'message' => $result['errors'][0]['message']
                    ]
                ];
            }
        // Cant get result from Transferwise
        }else{
            $response = [
                'status' => [
                    'code' => 2,
                    'message' => "Can't get result from Transferwise"
                ]
            ];
        }

        return response()->json($response);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $data = $request->session()->get('data');

//        dd($data);
        return view('trade.confirm', compact('data'));
    }

    /**
     * Show confirm screen
     * @param Request $request
     */
    public function postConfirm(Request $request){
        $this->validate($request, [
            'partner_email' => 'required|string|email|max:255',
            'mail_subject' => 'required|string|max:255',
            'mail_content' => 'required',
            'email' => 'required|string|email|max:255',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'type' => 'required|integer',
        ]);

        // Get post data
        $data = $request->all();

        // Send email to partner
        Mail::raw($data['mail_content'], function ($messages) use ($data) {
            $messages->to($data['partner_email'])
                ->subject($data['mail_subject'])
             ;
        });
        $request->session()->flash('alert-success', 'Email was sent to ' . ($data['type'] ? 'Buyer' : 'Seller') . '!');

        // Create user
        $user = User::where(['email' => $data['email']])->first();
        if($user){
            // Redirect to home page
            if(!Auth::check()){
                $request->session()->flash('alert-warning', 'You are member already. Please login to access dashboard screen');
            }
            return redirect()->route('home');
        }
        $password = Str::random(8);
        $user = new User([
            'email' => $data['email'],
            'password' => Hash::make($password),
            'name' => $data['first_name'] . $data['last_name']
        ]);
        $user->save();
        Auth::login($user, true);

        //TODO Save Offer
        // Redirect to Dashboard screen
        $request->session()->flash('alert-warning', 'We have created user for you. Please check email');
        return redirect()->route('home');
    }
}
