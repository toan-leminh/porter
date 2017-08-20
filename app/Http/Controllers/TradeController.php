<?php

namespace App\Http\Controllers;

use App\Lib\Transferwise;
use App\Mail\GeneralMail;
use App\QuoteDetail;
use App\QuoteHd;
use App\TemporaryQuotes;
use App\User;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use Mail;
use Symfony\Component\HttpFoundation\ParameterBag;
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
        // Get sessiondata and remove  if existed
        $data = $request->session()->pull('quote_data', []);
        $request->request = new ParameterBag($data);

        // Get all countries
        $countries = DB::table('country_mst')->pluck('name', 'country_cd');

        $currencies = [
            'USD' => 'USD',
            'JPY' => 'JPY',
            'VND' => 'VND',
        ];

        // Get content of mail templates
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
            // Validate submit data
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
                // Send data to confirm page
                $data = $request->all();
                $data['temporary_quote_id'] = $temporaryQuotes->id;

                $request->session()->put('quote_data', $data);

                return redirect()->route('trade.confirm');
            }else{

                return redirect()->route('trade.offer')->with(['error'=>'Email and Passcode are not matched. Please check again!'])->withInput();
            }

        }

        return redirect()->route('trade.offer')->with(['status'=>'email_checked'])->withInput();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $quoteData = $request->session()->get('quote_data');
        if($quoteData == null){
            return redirect()->route('trade.offer');
        }

        return view('trade.confirm', compact('data'));
    }

    /**
     * Show confirm screen
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postConfirm(Request $request){
        // Get session data
        $quoteData = $request->session()->get('quote_data');
        if($quoteData == null){
            return redirect()->route('trade.offer');
        }


        $this->validate($request, [
            'partner_email' => 'required|string|email|max:255',
            'mail_subject' => 'required|string|max:255',
            'mail_content' => 'required',
        ]);

        // Get post data
        $data = $request->all();

        // Send email to partner
        Mail::raw($data['mail_content'], function ($messages) use ($data) {
            $messages->to($data['partner_email'])
                ->subject($data['mail_subject'])
             ;
        });
        $request->session()->flash('alert-success', 'Email was sent to ' . ($quoteData['type'] ? 'Buyer' : 'Seller') . '!');

        // Create user if not exit
        $user = User::where(['email' => $quoteData['email']])->first();
        if($user){
            $hasRegistered = true;
        }else{
            $hasRegistered = false;
            // Get temporary quote data
            $temporaryQuote = TemporaryQuotes::find($quoteData['temporary_quote_id']);

            $user = new User([
                'email' => $quoteData['email'],
                'password' => Hash::make($temporaryQuote->code),
                'name' => $quoteData['first_name'] . ' ' . $quoteData['last_name']
            ]);
            $user->save();
        }

        //TODO Save Offer
        // Insert data to quote HD table
        // Seller
        if($quoteData['type'] == '1'){
            // Save to quote_hd table in database
            $quoteHd = new QuoteHd([
                'seller_email' => $quoteData['email'],
                'seller_name' => $quoteData['first_name'] . ' ' . $quoteData['last_name'],
                'buyer_email' => $quoteData['partner_email'],
                'buyer_country' => $quoteData['partner_country'],
                'customer_id' => $user->id,
                'trading_fee' => $quoteData['trading_fee'],
                'total_amount' => $quoteData['total_amount']
            ]);
            $quoteHd->save();
        // Buyer
        }elseif($quoteData['type'] == '2'){
            // Save to quote_hd table in database
            $quoteHd = new QuoteHd([
                'buyer_email' => $quoteData['email'],
                'buyer_name' => $quoteData['first_name'] . ' ' . $quoteData['last_name'],
                'seller_email' => $quoteData['partner_email'],
                'seller_country' => $quoteData['partner_country'],
                'customer_id' => $user->id,
                'trading_fee' => $quoteData['trading_fee'],
                'total_amount' => $quoteData['total_amount']
            ]);
            $quoteHd->save();
        }
        // Save to quote_detail table in database
        $quoteDetailList = [];
        foreach ($quoteData['trade_item'] as $item){
            $quoteDetail = new QuoteDetail($item);
            $quoteDetailList[] = $quoteDetail;
        }
        $quoteHd->quoteDetails()->saveMany($quoteDetailList);

        // Remove session data
        //$request->session()->forget('quote_data');
        //$request->session()->flush();

        // In case already registered user, check login or redirect to login screen
        if($hasRegistered){
            // Redirect to home page
            if(!Auth::check()){
                $request->session()->flash('alert-warning', 'You are member already. Please login to access dashboard screen');
            }
        // In case new user, auto login
        }else{
            Auth::login($user, true);
            $request->session()->flash('alert-success', 'We have created user for you. Welcome to My Transporter!');
        }

        // Redirect to Dashboard screen
        return redirect()->route('home');
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
}
