<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailVerificationCode;
use App\Jobs\SendPasswordResetMail;
use App\Models\Artisans;
use App\Models\Business;
use App\Models\Customer;
use App\Models\CustomerOtp;
use App\Models\CustomerSubscription;
use App\Models\CustomerWallet;
use App\Models\NotificationSetting;
use App\Models\Referral;
use App\Models\ReferralTransaction;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    /**
     * Verify the One-Time Code sent to Customer for Email Verification
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'digit_1' => 'required',
            'digit_2' => 'required',
            'digit_3' => 'required',
            'digit_4' => 'required',
        ]);

        if ($validator->fails()) {
            toast("Please enter the complete verification code", 'error');
            return back();
        }

        $verificationCode = $request->digit_1 . "" . $request->digit_2 . "" . $request->digit_3 . "" . $request->digit_4;

        $codeIsValid = CustomerOtp::where("otp_type", "email")->where("customer_id", Auth::user()->id)->where("otp", $verificationCode)->first();

        if (!$codeIsValid) {
            toast("The provided verification code is invalid", 'error');
            return back();
        }

        if (now() > $codeIsValid->otp_expiration) {
            toast("The provided verification code has expired", 'error');
            return back();
        }

        if (!$codeIsValid->delete()) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        if (!Auth::user()->update(['email_verified_at' => now()])) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        $activateTrial = CustomerSubscription::where("customer_id", Auth::user()->id)->where("plan_id", 1)->first();
        if (!isset($activateTrial)) {
            $subscription = new CustomerSubscription;
            $subscription->customer_id = Auth::user()->id;
            $subscription->plan_id = 1;
            $subscription->card_details = "N/A for Trial Plan";
            $subscription->subscription_amount = 0;
            $subscription->auto_renew = 0;
            $subscription->status = "active";
            $subscription->next_due_date = Carbon::now()->addDays(30);
            $subscription->save();
        }

        $referral = Referral::where("referral_id", Auth::user()->id)->whereNull("referral_type")->first();
        if (isset($referral)) {
            try {
                DB::beginTransaction();
                $referral->referral_type = "business";
                $referral->bonus_received = $this->getReferralBonus(Auth::user()->account_type);
                $referral->save();

                $customer = Customer::find($referral->customer_id);

                $transaction = new ReferralTransaction;
                $transaction->customer_id = $referral->customer_id;
                $transaction->trx_type = "credit";
                $transaction->amount = $referral->bonus_received;
                $transaction->details = "Referral Bonus received for referring " . Auth::user()->first_name . " " . Auth::user()->last_name;
                $transaction->balance_before = $customer->wallet->referral_points;
                $transaction->balance_after = ($customer->wallet->referral_points + $referral->bonus_received);
                $transaction->save();

                $customerWallet = CustomerWallet::where("customer_id", $referral->customer_id)->first();
                $customerWallet->referral_points = (double) ($customerWallet->referral_points + $referral->bonus_received);
                $customerWallet->save();

                DB::commit();
            } catch (\Exception $e) {
                report($e);
                DB::rollback();
            }

        }

        toast("Email Verified Successfully", 'success');
        return redirect()->route("home");
    }

    /**
     * Send Customer Email Verification Code
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function sendVerificationMail(Request $request)
    {

        if (!$otp = CustomerOtp::updateOrCreate(
            [
                'customer_id' => Auth::user()->id,
                'otp_type' => 'email',
            ], [
                'otp' => $this->generateOtp(),
                'otp_expiration' => Carbon::now()->addMinutes(5),
            ])) {
            return back();
        }

        SendEmailVerificationCode::dispatch($otp);

        return redirect()->route("home");

    }

    /**
     * Initiate Customer Password Reset
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function initiatePasswordReset(Request $request)
    {
        $validator = $this->validate($request, [
            'email' => 'required|',
        ]);

        $accountExist = Customer::where("email", $request->email)->where("status", "!=", "deleted")->first();

        if (!$accountExist) {
            toast("We could not find an account for the provided email", 'error');
            return back();
        }

        if (!$otp = CustomerOtp::updateOrCreate(
            [
                'customer_id' => $accountExist->id,
                'otp_type' => 'reset',
            ], [
                'otp' => $this->generateOtp(),
                'otp_expiration' => Carbon::now()->addMinutes(5),
            ])) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        SendPasswordResetMail::dispatch($otp);
        Session::put("email", $request->email);
        return redirect()->route("pwdResetConfirmation");

    }

    public function pwdResetConfirmation()
    {
        $email = Session::get("email");
        return view("auth.passwords.confirm", compact("email"));
    }

    /**
     * Verify the One-Time Code sent to Customer for Password Reset
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function passwordResetVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'digit_1' => 'required',
            'digit_2' => 'required',
            'digit_3' => 'required',
            'digit_4' => 'required',
        ]);

        if ($validator->fails()) {
            toast("Please enter the complete confirmation code", 'error');
            return back();
        }

        $confirmationCode = $request->digit_1 . "" . $request->digit_2 . "" . $request->digit_3 . "" . $request->digit_4;

        $customer = Customer::where("email", $request->email)->where("status", "!=", "deleted")->first();

        if (!$customer) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        $codeIsValid = CustomerOtp::where("otp_type", "reset")->where("customer_id", $customer->id)->where("otp", $confirmationCode)->first();

        if (!$codeIsValid) {
            toast("The provided password reset code is invalid", 'error');
            return back();
        }

        if (now() > $codeIsValid->otp_expiration) {
            toast("The provided password reset code has expired", 'error');
            return back();
        }

        if (!$codeIsValid->delete()) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        Session::put("email", $request->email);
        return redirect()->route("newPassword");
    }

    public function newPassword()
    {
        $email = Session::get("email");
        return view("auth.passwords.reset", compact("email"));
    }

    /**
     * Verify the One-Time Code sent to Customer for Password Reset
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function createNewPassword(Request $request)
    {
        $validator = $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required',
        ]);

        $customer = Customer::where("email", $request->email)->where("status", "!=", "deleted")->first();

        if (!$customer) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        if ($request->password != $request->password_confirmation) {
            toast("Your newly seleted passwords do not match.", 'error');
            return back();
        } else {
            $customer->password = Hash::make($request->password);
            if (!$customer->save()) {
                toast("Something Went Wrong", 'error');
                return back();
            }
        }

        toast("Password Changed Successfully", 'success');
        return redirect("/login");

    }

    public function accountSelection()
    {
        return view("auth.account_selection");
    }

    /**
     * selectAccountType
     *
     * @param Request request
     *
     * @return JsonResponse
     */
    public function selectAccountType($accountType)
    {
        if ($accountType != 'business' && $accountType != 'artisan') {
            toast("Account Type must be 'business' or 'artisan'", 'error');
            return back();
        }

        if (!Auth::user()->update(['account_type' => $accountType])) {
            toast("Something Went Wrong", 'error');
            return back();
        }

        $notSet = NotificationSetting::updateOrCreate(
            [
                'customer_id' => Auth::user()->id,
            ], [
                'push_notification' => 1,
                'email_notification' => 1,
            ]);

        if (Auth::user()->account_type == "business") {
            $busSet = Business::updateOrCreate(
                [
                    'customer_id' => Auth::user()->id,
                ],
            );

            $referral = Referral::where("referral_id", Auth::user()->id)->whereNull("referral_type")->first();
            if (isset($referral)) {
                try {
                    DB::beginTransaction();
                    $referral->referral_type = "business";
                    $referral->bonus_received = $this->getReferralBonus(Auth::user()->account_type);
                    $referral->save();

                    $customerWallet = CustomerWallet::where("customer_id", $referral->customer_id)->first();
                    $customerWallet->referral_points = (double) ($customerWallet->referral_points + $referral->bonus_received);
                    $customerWallet->save();

                    $transaction = new ReferralTransaction;
                    $transaction->customer_id = $referral->customer_id;
                    $transaction->trx_type = "credit";
                    $transaction->amount = $referral->bonus_received;
                    $transaction->details = "Referral Bonus Received";
                    $transaction->save();

                    DB::commit();
                } catch (\Exception $e) {
                    report($e);
                    DB::rollback();
                }

            }
        } else {
            $artisan = Artisans::updateOrCreate(
                [
                    'customer_id' => Auth::user()->id,
                ],
            );

            $referral = Referral::where("referral_id", Auth::user()->id)->whereNull("referral_type")->first();
            if (isset($referral)) {
                try {
                    DB::beginTransaction();
                    $referral->referral_type = "artisan";
                    $referral->bonus_received = $this->getReferralBonus(Auth::user()->account_type);
                    $referral->save();

                    $customerWallet = CustomerWallet::where("customer_id", $referral->customer_id)->first();
                    $customerWallet->referral_points = (double) ($customerWallet->referral_points + $referral->bonus_received);
                    $customerWallet->save();

                    $transaction = new ReferralTransaction;
                    $transaction->customer_id = $referral->customer_id;
                    $transaction->trx_type = "credit";
                    $transaction->amount = $referral->bonus_received;
                    $transaction->details = "Referral Bonus Received";
                    $transaction->save();

                    DB::commit();
                } catch (\Exception $e) {
                    report($e);
                    DB::rollback();
                }
            }
        }

        return redirect()->route("home");

    }

    /**
     * Generate a 4-digit One-Time Code
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
        for ($i = 0; $i < 4; $i++) {
            $otp = $otp . "" . $pin[$i];
        }

        return $otp;
    }

    /**
     * getReferralBonus
     *
     * @param mixed accountType
     *
     * @return void
     */
    public function getReferralBonus($accountType)
    {
        if ($accountType == "business") {
            return (double) 40.00;
        } else {
            return (double) 20.00;
        }
    }
}
