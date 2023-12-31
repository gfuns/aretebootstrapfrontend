<?php

namespace App\Jobs;

use App\Mail\PasswordResetMail as PasswordResetMail;
use App\Models\Customer;
use App\Models\CustomerOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendPasswordResetMail
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $otp;
    /**
     * Create a new job instance.
     */
    public function __construct(CustomerOtp $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $otp = $this->otp;
        $customer = Customer::find($otp->customer_id);

        try {
            Mail::to($customer)->send(new PasswordResetMail($customer, $otp->otp));
        } catch (\Exception $e) {
            report($e);
        }
    }
}
