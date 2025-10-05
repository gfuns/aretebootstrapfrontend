<?php
namespace App\Http\Controllers;

use App\Mail\AirtimeSuccessful as AirtimeSuccessful;
use App\Mail\CableSuccessful as CableSuccessful;
use App\Mail\DataSuccessful as DataSuccessful;
use App\Mail\ElectricitySuccessful as ElectricitySuccessful;
use App\Models\Business;
use App\Models\CardTransactions;
use App\Models\Customer;
use App\Models\CustomerCards;
use App\Models\CustomerSubscription;
use App\Models\CustomerWallet;
use App\Models\JobListing;
use App\Models\Referral;
use App\Models\ReferralTransaction;
use App\Models\SubscriptionPlan;
use App\Models\UtilityTransactions;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mail;

class CronController extends Controller
{
    //

    public function renewSubscription()
    {
        $today                 = Carbon::today()->toDateString();
        $renewableTransactions = CustomerSubscription::whereDate("next_due_date", $today)->where("status", "active")->get();
        foreach ($renewableTransactions as $rt) {

            if ($rt->plan_id == 1) {
                $rt->status = "inactive";
                $rt->save();

                //We Deactivate All his Job Listings and Deactivate His Business Page
                Business::where("customer_id", $rt->customer_id)->update([
                    "visibility" => 0,
                ]);

                JobListing::where("customer_id", $rt->customer_id)->update([
                    "status" => "archived",
                ]);
            } else {

                $activeCard = CustomerCards::where("customer_id", $rt->customer_id)->where("default_card", 1)->first();
                $plan       = SubscriptionPlan::find($rt->plan_id);

                $status = $this->chargeCardWithAuthorization($activeCard->id, $rt->plan_id, $rt->customer_id);

                if ($status === true) {
                    try {
                        DB::beginTransaction();

                        CustomerSubscription::where("customer_id", $rt->customer_id)->update([
                            "status" => "inactive",
                        ]);

                        $subscription                      = new CustomerSubscription;
                        $subscription->customer_id         = $rt->customer_id;
                        $subscription->plan_id             = $plan->id;
                        $subscription->card_details        = ucwords($activeCard->card_brand) . " ending with " . $activeCard->last_four_digits;
                        $subscription->subscription_amount = $plan->billing_amount;
                        $subscription->auto_renew          = 1;
                        $subscription->status              = "active";
                        $subscription->next_due_date       = Carbon::now()->addDays($plan->duration);
                        $subscription->save();

                        $referral = Referral::where("referral_id", $rt->customer_id)->first();
                        if (isset($referral)) {
                            $referralSubscribed = CustomerSubscription::where("customer_id", $referral->customer_id)->where("status", "active")->first();
                            if (isset($referralSubscribed)) {
                                $bonus    = ((5 / 100) * $plan->billing_amount);
                                $customer = Customer::find($referral->customer_id);

                                $transaction                 = new ReferralTransaction;
                                $transaction->customer_id    = $referral->customer_id;
                                $transaction->trx_type       = "credit";
                                $transaction->amount         = $bonus;
                                $transaction->details        = "Bonus received from subcription made by " . $referral->customer->first_name . " " . $referral->customer->last_name;
                                $transaction->balance_before = $customer->wallet->referral_points;
                                $transaction->balance_after  = ($customer->wallet->referral_points + $bonus);
                                $transaction->save();

                                $customerWallet                  = CustomerWallet::where("customer_id", $referral->customer_id)->first();
                                $customerWallet->referral_points = (double) ($customerWallet->referral_points + $bonus);
                                $customerWallet->save();
                            }
                        }
                        DB::commit();

                    } catch (\Exception $e) {
                        DB::rollback();
                        report($e);

                    }
                } else {
                    $rt->status = "inactive";
                    $rt->save();

                    //We Deactivate All his Job Listings and Deactivate His Business Page
                    Business::where("customer_id", $rt->customer_id)->update([
                        "visibility" => 0,
                    ]);

                    JobListing::where("customer_id", $rt->customer_id)->update([
                        "status" => "archived",
                    ]);

                }
            }
        }
    }

    public function expiredSubscriptions()
    {
        $today                 = Carbon::today()->toDateString();
        $rexpiredSubscriptions = CustomerSubscription::whereDate("next_due_date", "<", $today)->where("status", "active")->get();
        foreach ($rexpiredSubscriptions as $et) {
            $et->status = "inactive";
            $et->save();

            //We Deactivate All his Job Listings and Deactivate His Business Page
            Business::where("customer_id", $et->customer_id)->update([
                "visibility" => 0,
            ]);

            JobListing::where("customer_id", $et->customer_id)->update([
                "status" => "archived",
            ]);

            $activeCard = CustomerCards::where("customer_id", $et->customer_id)->where("default_card", 1)->first();
            $plan       = SubscriptionPlan::find($et->plan_id);

            $status = false; //$this->chargeCardWithAuthorization($activeCard->id, $et->plan_id, $et->customer_id);

            if ($status === true) {
                try {
                    DB::beginTransaction();

                    CustomerSubscription::where("customer_id", $et->customer_id)->update([
                        "status" => "inactive",
                    ]);

                    $subscription                      = new CustomerSubscription;
                    $subscription->customer_id         = $et->customer_id;
                    $subscription->plan_id             = $plan->id;
                    $subscription->card_details        = ucwords($activeCard->card_brand) . " ending with " . $activeCard->last_four_digits;
                    $subscription->subscription_amount = $plan->billing_amount;
                    $subscription->auto_renew          = 1;
                    $subscription->status              = "active";
                    $subscription->next_due_date       = Carbon::now()->addDays($plan->duration);
                    $subscription->save();

                    $referral = Referral::where("referral_id", $et->customer_id)->first();
                    if (isset($referral)) {
                        $referralSubscribed = CustomerSubscription::where("customer_id", $referral->customer_id)->where("status", "active")->first();
                        if (isset($referralSubscribed)) {
                            $bonus    = ((5 / 100) * $plan->billing_amount);
                            $customer = Customer::find($referral->customer_id);

                            $transaction                 = new ReferralTransaction;
                            $transaction->customer_id    = $referral->customer_id;
                            $transaction->trx_type       = "credit";
                            $transaction->amount         = $bonus;
                            $transaction->details        = "Bonus received from subcription made by " . $referral->customer->first_name . " " . $referral->customer->last_name;
                            $transaction->balance_before = $customer->wallet->referral_points;
                            $transaction->balance_after  = ($customer->wallet->referral_points + $bonus);
                            $transaction->save();

                            $customerWallet                  = CustomerWallet::where("customer_id", $referral->customer_id)->first();
                            $customerWallet->referral_points = (double) ($customerWallet->referral_points + $bonus);
                            $customerWallet->save();
                        }
                    }
                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollback();
                    report($e);

                }
            } else {
                $et->status = "inactive";
                $et->save();

                //We Deactivate All his Job Listings and Deactivate His Business Page
                Business::where("customer_id", $et->customer_id)->update([
                    "visibility" => 0,
                ]);

                JobListing::where("customer_id", $et->customer_id)->update([
                    "status" => "archived",
                ]);
            }
        }
    }

    /**
     * chargeCardWithAuthorization
     *
     * @param mixed cardId
     *
     * @return void
     */
    public function chargeCardWithAuthorization($cardId, $planId, $customerId)
    {
        try {
            $customer = Customer::find($customerId);
            $plan     = SubscriptionPlan::find($planId);
            $card     = CustomerCards::where("id", $cardId)->where("customer_id", $customer->id)->first();
            if (isset($plan) && isset($card)) {
                $response = Http::accept('application/json')->withHeaders([
                    'Authorization' => "Bearer " . env('PAYSTACK_SECRET_KEY'),
                ])->post("https://api.paystack.co/transaction/charge_authorization", [
                    "authorization_code" => $card->authorization_code,
                    "email"              => $customer->email,
                    "amount"             => ($plan->billing_amount * 100),
                ]);

                $resData = $response->json();

                if ($resData["status"] === true && $resData["data"]["status"] == "success") {
                    $cardTrx                     = new CardTransactions;
                    $cardTrx->customer_id        = $customer->id;
                    $cardTrx->card_id            = $cardId;
                    $cardTrx->amount             = $plan->billing_amount;
                    $cardTrx->paystack_reference = $resData["data"]["reference"];
                    $cardTrx->description        = ucwords($card->card_brand) . " card:  " . $card->last_four_digits . " - Customer Subscription to " . $plan->plan . " Plan (" . $plan->duration . ")";
                    if ($cardTrx->save()) {
                        return true;

                    } else {
                        return false;

                    }
                } else {
                    return false;
                }

            } else {
                return false;
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return false;
        }

    }

    public function closeInitiatedTransactions()
    {
        $billTransactions = UtilityTransactions::where("status", "Initiated")->whereDate("created_at", '<>', Carbon::today())->get();
        foreach ($billTransactions as $trx) {
            $trx->status = "Failed";
            $trx->save();
        }

        return response()->json([
            'title'   => 'Close Initiated Transactions CRON Job',
            'status'  => 'success',
            'message' => 'Close Initiated Transactions CRON Successful.',
        ]);
    }

    public function checkPendingElectricity()
    {

        $pendingTransactions = UtilityTransactions::where("status", "Pending")->where("trx_type", "Electricity")->get();

        foreach ($pendingTransactions as $trans) {

            try {

                $data = [
                    'request_id' => $trans->reference,
                ];

                $curl = curl_init(env('VTPASS_ENDPOINT') . "/api/requery");
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERPWD, env('VTPASS_PK') . ":" . env('VTPASS_SK'));
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($curl);
                curl_close($curl);

                $result = json_decode($response);

                if (isset($result)) {
                    dd($result);
                    if ($result->code == "000" && $result->response_description == "TRANSACTION SUCCESSFUL") {
                        $trx                    = UtilityTransactions::find($trans->id);
                        $trx->token             = isset($result->Token) ? $result->Token : $result->mainToken;
                        $trx->units             = isset($result->TariffRate) ? $result->TariffRate : (isset($result->mainTokenUnits) ? $result->mainTokenUnits : $result->Units);
                        $trx->recipient_address = isset($result->Address) ? $result->Address : (isset($result->CustomerAddress) ? $result->CustomerAddress : null);
                        $trx->status            = "Successful";
                        $trx->save();

                        $user = Customer::find($trx->customer_id);
                        Mail::to($user)->send(new ElectricitySuccessful($user, $trx));

                    } elseif ($result->code == "016" && $result->response_description == "TRANSACTION FAILED") {

                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Failed";
                        $trx->save();

                    } elseif ($result->code == "040" && $result->response_description == "TRANSACTION REVERSED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Reversed";
                        $trx->save();
                    } else {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Pending";
                        $trx->save();
                    }
                }

            } catch (\Exception $e) {
                report($e);
            }
        }

        return response()->json([
            'title'   => 'Check Pending Electricity CRON Job',
            'status'  => 'success',
            'message' => 'Check Pending Electricity CRON Successful.',
        ]);
    }

    public function checkPendingCable()
    {
        $pendingTransactions = UtilityTransactions::where("status", "Pending")->where("trx_type", "Cable")->get();

        foreach ($pendingTransactions as $trans) {

            try {

                $data = [
                    'request_id' => $trans->reference,
                ];

                $curl = curl_init(env('VTPASS_ENDPOINT') . "/api/requery");
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERPWD, env('VTPASS_PK') . ":" . env('VTPASS_SK'));
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($curl);
                curl_close($curl);

                $result = json_decode($response);

                if (isset($result)) {

                    if ($result->code == "000" && $result->response_description == "TRANSACTION SUCCESSFUL") {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Successful";
                        $trx->save();

                        $user = Customer::find($trx->customer_id);
                        Mail::to($user)->send(new CableSuccessful($user, $trx));
                    } elseif ($result->code == "016" && $result->response_description == "TRANSACTION FAILED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Failed";
                        $trx->save();
                    } elseif ($result->code == "040" && $result->response_description == "TRANSACTION REVERSED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Reversed";
                        $trx->save();
                    } else {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Pending";
                        $trx->save();
                    }
                }

            } catch (\Exception $e) {
                report($e);
            }
        }

        return response()->json([
            'title'   => 'Check Pending Cable CRON Job',
            'status'  => 'success',
            'message' => 'Check Pending Cable CRON Successful.',
        ]);
    }

    public function checkPendingData()
    {
        $pendingTransactions = UtilityTransactions::where("status", "Pending")->where("trx_type", "Data")->get();

        foreach ($pendingTransactions as $trans) {

            try {

                $data = [
                    'request_id' => $trans->reference,
                ];

                $curl = curl_init(env('VTPASS_ENDPOINT') . "/api/requery");
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERPWD, env('VTPASS_PK') . ":" . env('VTPASS_SK'));
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($curl);
                curl_close($curl);

                $result = json_decode($response);

                if (isset($result)) {

                    if ($result->code == "000" && $result->response_description == "TRANSACTION SUCCESSFUL") {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Successful";
                        $trx->save();

                        $user = Customer::find($trx->customer_id);
                        Mail::to($user)->send(new DataSuccessful($user, $trx));

                    } elseif ($result->code == "016" && $result->response_description == "TRANSACTION FAILED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Failed";
                        $trx->save();
                    } elseif ($result->code == "040" && $result->response_description == "TRANSACTION REVERSED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Reversed";
                        $trx->save();
                    } else {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Pending";
                        $trx->save();
                    }
                }

            } catch (\Exception $e) {
                report($e);
            }
        }

        return response()->json([
            'title'   => 'Check Pending Data CRON Job',
            'status'  => 'success',
            'message' => 'Check Pending Data CRON Successful.',
        ]);
    }

    public function checkPendingAirtime()
    {
        $pendingTransactions = UtilityTransactions::where("status", "Pending")->where("trx_type", "Airtime")->get();

        foreach ($pendingTransactions as $trans) {

            try {

                $data = [
                    'request_id' => $trans->reference,
                ];

                $curl = curl_init(env('VTPASS_ENDPOINT') . "/api/requery");
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_USERPWD, env('VTPASS_PK') . ":" . env('VTPASS_SK'));
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                $response = curl_exec($curl);
                curl_close($curl);

                $result = json_decode($response);

                if (isset($result)) {

                    if ($result->code == "000" && $result->response_description == "TRANSACTION SUCCESSFUL") {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Successful";
                        $trx->save();

                        $user = Customer::find($trx->customer_id);
                        Mail::to($user)->send(new AirtimeSuccessful($user, $trx));

                    } elseif ($result->code == "016" && $result->response_description == "TRANSACTION FAILED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Failed";
                        $trx->save();
                    } elseif ($result->code == "040" && $result->response_description == "TRANSACTION REVERSED") {
                        $trx = UtilityTransactions::find($trans->id);

                        if ($trx->payment_method == "Bonus Balance") {
                            $wallet                  = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->referral_points = (double) ($wallet->referral_points + $trx->total_amount);
                            $wallet->save();
                        } else {
                            $wallet                = CustomerWallet::where("customer_id", $trx->customer_id)->first();
                            $wallet->arete_balance = (double) ($wallet->arete_balance + $trx->total_amount);
                            $wallet->save();
                        }

                        $trx->status = "Reversed";
                        $trx->save();
                    } else {
                        $trx         = UtilityTransactions::find($trans->id);
                        $trx->status = "Pending";
                        $trx->save();
                    }
                }

            } catch (\Exception $e) {
                report($e);
            }
        }

        return response()->json([
            'title'   => 'Check Pending Airtime CRON Job',
            'status'  => 'success',
            'message' => 'Check Pending Airtime CRON Successful.',
        ]);
    }
}
