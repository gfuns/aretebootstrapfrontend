<?php
namespace App\Http\Controllers;

use App\Mail\AuthenticationOTP as AuthenticationOTP;
use App\Mail\LoginNotification as LoginNotification;
use App\Models\Business;
use App\Models\CustomerOtp;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Jenssegers\Agent\Facades\Agent;
use Mail;
use Stevebauman\Location\Facades\Location;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->account_type == "business") {
            $businessExist = Business::where("customer_id", Auth::user()->id)->first();
            if (! isset($businessExist)) {
                $business              = new Business;
                $business->customer_id = Auth::user()->id;
                $business->save();
            }

            try {
                if (Auth::user()->auth_2fa == "Email") {
                    if ($otp = CustomerOtp::updateOrCreate(
                        [
                            'customer_id' => Auth::user()->id,
                            'otp_type'    => Auth::user()->auth_2fa,
                        ], [
                            'otp'            => $this->generateOtp(),
                            'otp_expiration' => Carbon::now()->addMinutes(10),
                        ])) {

                        if (Auth::user()->auth_2fa == "Email") {
                            $user = Auth::user();
                            Mail::to($user)->send(new AuthenticationOTP($user, $otp));
                        }
                    }

                }

            } catch (\Exception $e) {
                report($e);
            } finally {
                return redirect()->route("business.dashboard");
            }

            return redirect()->route("business.dashboard");
        } else if (Auth::user()->account_type == "artisan") {
            return redirect()->route("artisan.dashboard");
        } else {
            return redirect()->route("accountSelection");
        }
    }

    public function authy()
    {
        try {
            $user       = Auth::user();
            $platform   = Agent::platform();
            $ip         = "172.70.231.54"; //request()->ip();
            $location   = Location::get($ip);
            $deviceInfo = [
                "device"     => $platform . "-" . Agent::version($platform),
                "browser"    => Agent::browser(),
                "ip_address" => $location->ip,
                "location"   => $location->countryName,
            ];

            Mail::to($user)->send(new LoginNotification($user, $deviceInfo));
        } catch (\Exception $e) {
            report($e);
        } finally {
            return redirect()->route("home");
        }
    }

    /**
     * Generate a 6-digit One-Time Code
     *
     * @param null
     *
     * @return String $otp
     */
    public function generateOtp()
    {
        $pin = range(0, 9);
        $set = shuffle($pin);
        $otp = "";
        for ($i = 0; $i < 6; $i++) {
            $otp = $otp . "" . $pin[$i];
        }

        return $otp;
    }

}
