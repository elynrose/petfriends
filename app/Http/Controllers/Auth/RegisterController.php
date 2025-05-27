<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Referral;
use App\Notifications\ReferralCreditAwarded;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

         /**
          * Create a new user instance after a valid registration.
          *
          * @param  array  $data
          * @return \App\User
          */
         protected function create(array $data)
         {
             // Generate unique referral token for the new user
             do {
                 $token = Str::random(8);
             } while (User::where('referral_token', $token)->exists());

             $user = User::create([
                 'name' => $data['name'],
                 'email' => $data['email'],
                 'password' => Hash::make($data['password']),
                 'referral_token' => $token,
             ]);

             // Handle referral if exists
             if (request()->has('ref')) {
                 $referrer = User::where('referral_token', request('ref'))->first();
                 
                 if ($referrer) {
                     // Create or update referral record
                     $referral = Referral::updateOrCreate(
                         [
                             'referrer_id' => $referrer->id,
                             'email' => $data['email']
                         ],
                         [
                             'token' => request('ref'),
                             'is_registered' => true,
                             'registered_at' => now(),
                         ]
                     );

                     // Award credits to referrer
                     $referrer->credits += 4; // 4 hours of credits
                     $referrer->save();

                     // Notify referrer
                     $referrer->notify(new ReferralCreditAwarded($referral));
                 }
             }

             return $user;
         }
}
