<?php
namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessPage;
use App\Models\BusinessReviews;
use App\Models\Customer;
use App\Models\CustomerSubscription;
use App\Models\JobListing;
use App\Models\NotificationSetting;
use App\Models\PlatformCategories;
use App\Models\Products;
use App\Models\TutorialVideos;
use Auth;
use Carbon\Carbon;
use Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;

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
    public function dashboard()
    {
        $jobs  = JobListing::where("customer_id", Auth::user()->business->id)->pluck("id");
        $param = [
            'jobsPosted'         => JobListing::where("customer_id", Auth::user()->business->id)->count(),
            'areteBalance'       => Auth::user()->wallet->arete_balance,
            'referralPoints'     => Auth::user()->wallet->referral_points,
            'activeSubscription' => $activeSubscription = CustomerSubscription::where("customer_id", Auth::user()->id)->where("status", "active")->first(),
        ];

        // Get the current month
        $currentMonth = Carbon::now()->month;

        // Get the year
        $year = Carbon::now()->year;

        // Get the number of days in the current month
        $daysInMonth = Carbon::now()->daysInMonth;

        // Initialize an array to store the formatted dates
        $formattedDates = null;

        // Loop through each day of the month and format the date
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Create a Carbon instance for the current day
            $date = Carbon::createFromDate($year, $currentMonth, $day);

            // Format the date as "dd Mmm" (e.g., "01 Mar", "02 Mar", etc.)
            $formattedDate = $date->format('d M');

            // Add the formatted date to the array
            $formattedDates[] = $formattedDate;
        }

        // $formattedDates = implode(', ', $formattedDates);

        // dd($formattedDates);

        return view("business.dashboard", compact("param", "formattedDates"));
    }

    /**
     * profile
     *
     * @return void
     */
    public function viewProfile()
    {
        return view("business.profile");
    }

    /**
     * updateProfile
     *
     * @param Request request
     *
     * @return void
     */
    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'phone'      => 'required',
            'gender'     => 'required',
            'country'    => 'required',
        ]);

        $parseEmail = Customer::where("email", $request->email)->where("id", "!=", Auth::user()->id)->count();
        if ($parseEmail > 0) {
            toast('Email already taken by someone else.', 'error');
            return back();
        }

        $user             = Auth::user();
        $user->first_name = $request->first_name;
        $user->first_name = $request->first_name;
        $user->phone      = $request->phone;
        $user->gender     = $request->gender;
        $user->country    = $request->country;
        if ($request->has('profile_photo')) {
            $uploadedFileUrl = Cloudinary::upload($request->file('profile_photo')->getRealPath())->getSecurePath();
            $user->photo     = $uploadedFileUrl;
        }

        if ($user->save()) {
            toast('Profile Information Successfully Updated.', 'success');
            return back();
        } else {
            toast('Something went wrong. Please try again', 'error');
            return back();
        }

    }

    /**
     * changePassword
     *
     * @return void
     */
    public function changePassword()
    {
        return view("business.change_password");
    }

    /**
     * updatePassword
     *
     * @param Request request
     *
     * @return void
     */
    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required',
            'new_password_confirmation' => 'required',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            toast('Invalid current password provided.', 'error');
            return back();
        } else {
            if ($request->new_password != $request->new_password_confirmation) {
                toast('Your newly seleted passwords do not match.', 'error');
                return back();
            } else {
                $user->password = Hash::make($request->new_password);
                $user->save();
            }
        }

        if ($user->save()) {
            toast('Password Successfully Updated.', 'success');
            return back();
        } else {
            toast('Something went wrong. Please try again', 'error');
            return back();
        }

    }

    /**
     * businessProfile
     *
     * @return void
     */
    public function businessProfile()
    {
        $business   = Business::where("customer_id", Auth::user()->id)->first();
        $categories = PlatformCategories::orderBy("category_name", "asc")->where("category_type", "business")->get();
        return view("business.business_information", compact("business", "categories"));
    }

    public function updateBusinessProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name'        => 'required',
            'business_category'    => 'required',
            'business_description' => 'required',
            'business_phone'       => 'required',
            'business_email'       => 'required',
            'country'              => 'required',
            'state'                => 'required',
            'city'                 => 'required',
            'business_address'     => 'required',
        ]);

        if ($request->business_category == "Others") {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|unique:platform_categories',
            ]);
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errors = implode("<br>", $errors);
            toast($errors, 'error');
            return back();
        }

        try {

            DB::beginTransaction();

            if ($request->business_category == "Others") {
                $platformCat                = new PlatformCategories;
                $platformCat->category_name = $request->category_name;
                $platformCat->slug          = preg_replace("/ /", "-", strtolower($request->category_name));
                $platformCat->save();
                $businessCategoryId   = $platformCat->id;
                $businessCategoryName = PlatformCategories::find($platformCat->id)->category_name;
            } else {

                $businessCategoryId   = $request->business_category;
                $businessCategoryName = PlatformCategories::find($request->business_category)->category_name;
            }

            $business                       = Business::where("customer_id", Auth::user()->id)->first();
            $business->business_name        = $request->business_name;
            $business->slug                 = preg_replace("/ /", "-", strtolower($request->business_name));
            $business->category_id          = $businessCategoryId;
            $business->business_category    = $businessCategoryName;
            $business->country              = $request->country;
            $business->state                = $request->state;
            $business->city                 = $request->city;
            $business->street               = $request->business_address;
            $business->business_address     = $request->business_address;
            $business->business_description = $request->business_description;
            $business->business_phone       = $request->business_phone;
            $business->business_email       = $request->business_email;
            $business->website_url          = $request->website_url;
            $business->facebook_url         = $request->facebook_url;
            $business->twitter_url          = $request->twitter_url;
            $business->instagram_url        = $request->instagram_url;
            $business->linkedin_url         = $request->linkedin_url;
            $business->latitude             = $request->latitude;
            $business->longitude            = $request->longitude;
            $business->visibility           = 1;
            if ($request->has('business_logo')) {
                $uploadedFileUrl         = Cloudinary::upload($request->file('business_logo')->getRealPath())->getSecurePath();
                $business->business_logo = $uploadedFileUrl;
            }

            $business->save();

            DB::commit();

            toast('Business Information Successfully Updated.', 'success');
            return back();

        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            toast('Something went wrong. Please try again', 'error');
            return back();
        }
    }

    public function businessPage()
    {
        $businessExist = Business::where("customer_id", Auth::user()->id)->first();

        if (isset($businessExist->business_name)) {
            $business      = Business::where("customer_id", Auth::user()->id)->first();
            $topBanner     = BusinessPage::where("business_id", $business->id)->where("file_position", "banner")->first();
            $sliderBanners = BusinessPage::where("business_id", $business->id)->where("file_position", "slider")->get();
            $catalogues    = BusinessPage::where("business_id", $business->id)->where("file_position", "catalogue")->get();
            return view("business.business_page", compact("business", "topBanner", "sliderBanners", "catalogues"));
        } else {
            toast('Please Complete The Business Information Form to be able to setup your business page.', 'error');
            return redirect()->route("business.businessProfile");
        }

    }

    public function notificationSettings()
    {
        $notifications = NotificationSetting::where("customer_id", Auth::user()->id)->first();
        return view("business.notification_settings", compact("notifications"));
    }

    /**
     * toggleAllNotificationSettings
     *
     * @param Request request
     *
     * @return void
     */
    public function toggleAllNotificationSettings(Request $request)
    {

        NotificationSetting::where('customer_id', Auth::user()->id)->update([
            'unusual_activity'   => $request->status,
            'new_browser_signin' => $request->status,
            'latest_news'        => $request->status,
            'features_updates'   => $request->status,
            'account_tips'       => $request->status,
            'all_not'            => $request->status,
        ]);

        return response()->json(['status' => 'Notification status changed successfully.']);
    }

    /**
     * toggleSpecificNotificationSettings
     *
     * @param Request request
     *
     * @return void
     */
    public function toggleSpecificNotificationSettings(Request $request)
    {

        NotificationSetting::where('customer_id', Auth::user()->id)->update([
            $request->param => $request->status,
        ]);

        return response()->json(['status' => 'Notification status changed successfully.']);

    }

    public function unsubscribeAllNotifications()
    {
        $notifications                     = NotificationSetting::where("customer_id", Auth::user()->id)->first();
        $notifications->unusual_activity   = 0;
        $notifications->new_browser_signin = 0;
        $notifications->latest_news        = 0;
        $notifications->features_updates   = 0;
        $notifications->account_tips       = 0;
        if ($notifications->save()) {
            toast('You have unsubscribed from all notifications.', 'success');
            return back();
        } else {
            toast('Something went wrong. Please try again', 'error');
            return back();
        }
    }

    /**
     * security
     *
     * @return void
     */
    public function security()
    {
        $google2fa       = app('pragmarx.google2fa');
        $google2faSecret = $google2fa->generateSecretKey();
        $QRImage         = $google2fa->getQRCodeInline(
            env('APP_NAME'),
            Auth::user()->email,
            $google2faSecret
        );
        return view("business.security", compact("google2faSecret", "QRImage"));
    }

    public function enableGA(Request $request)
    {
        $gaCode   = $request->google2fa_code;
        $gaSecret = $request->google2fa_secret;

        if ($gaCode == null || $gaSecret == null) {
            toast('Please enter a valid Google 2FA Code.', 'error');
            return back();
        }

        $user      = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $valid     = $google2fa->verifyKey($gaSecret, $gaCode);

        if ($valid) {
            $user->google2fa_secret = $gaSecret;
            if ($user->save()) {
                toast('Successfully Enabled Google Authenticator on your account', 'success');
                return back();
            } else {
                toast('Something went wrong.', 'error');
                return back();
            }

        } else {
            toast('Invalid Google 2FA Code.', 'error');
            return back();

        }

    }

    public function select2FA(Request $request)
    {

        $user = Auth::user();

        if ($request->param == "google_auth2fa") {
            if (isset($user->google2fa_secret) && $request->status == 1) {
                $data = [
                    'id'   => Auth::user()->id,
                    'time' => now(),
                ];
                Session::put('myGoogle2fa', $data);
                $user->auth_2fa = "GoogleAuth";
            } else if (isset($user->google2fa_secret) && $request->status == 0) {
                $user->auth_2fa = null;
                Session::forget('myGoogle2fa');
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Please Setup Google Authenticator to be able to enable this option.',
                ]);
            }
        }

        if ($request->param == "email_auth2fa") {
            if ($request->status == 1) {
                $user->auth_2fa = "Email";
                $data           = [
                    'id'   => Auth::user()->id,
                    'time' => now(),
                ];
                Session::put('myValid2fa', $data);
            } else {
                $user->auth_2fa = null;
                Session::forget('myValid2fa');
            }
        }

        if ($user->save()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Authentication 2FA Method Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong! Please try again',
            ]);
        }

    }

    public function selectWithdrawalConfirmation(Request $request)
    {

        $user = Auth::user();

        if ($request->param == "google_withdrawal") {
            if (isset($user->google2fa_secret) && $request->status == 1) {
                $user->withdrawal_confirmation = "GoogleAuth";
            } else if (isset($user->google2fa_secret) && $request->status == 0) {
                $user->withdrawal_confirmation = null;
            } else {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Please Setup Google Authenticator to be able to enable this option.',
                ]);
            }
        }

        if ($request->param == "email_withdrawal") {
            if ($request->status == 1) {
                $user->withdrawal_confirmation = "Email";
            } else {
                $user->withdrawal_confirmation = null;
            }
        }

        if ($user->save()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Withdrawal Confirmation Method Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Something went wrong! Please try again',
            ]);
        }

    }

    public function deleteAccount()
    {
        return view("business.delete_account");
    }

    public function processAccountDeletion()
    {
        $customer         = Auth::user();
        $customer->status = "deleted";
        if ($customer->save()) {
            $jobs = JobListing::where("customer_id", Auth::user()->id)->update([
                'status' => "deleted",
            ]);

            toast('Account Deleted Successfully.', 'success');
            return back();
        } else {
            toast('Something Went Wrong.', 'error');
            return back();
        }
    }

    public function miniStore()
    {
        $search = request()->q;
        $filter = request()->filter == null ? 'asc' : request()->filter;
        if (isset($search)) {
            $lastRecord = Products::where("product_name", "LIKE", "%" . $search . "%")->count();
            $marker     = $this->shopMarkers($lastRecord, request()->page);
            $products   = Products::orderBy("id", $filter)->where("product_name", "LIKE", "%" . $search . "%")->paginate(12);
        } else {
            $lastRecord = Products::count();
            $marker     = $this->shopMarkers($lastRecord, request()->page);
            $products   = Products::orderBy("id", $filter)->paginate(12);
        }
        return view("business.mini_store", compact("products", "search", "lastRecord", "marker", "filter"));
    }

    public function academy()
    {
        $search = request()->q;
        $filter = request()->filter == null ? 'asc' : request()->filter;
        if (isset($search)) {
            $lastRecord     = TutorialVideos::where("video_title", "LIKE", "%" . $search . "%")->count();
            $marker         = $this->academyMarkers($lastRecord, request()->page);
            $tutorialVideos = TutorialVideos::orderBy("id", $filter)->where("video_title", "LIKE", "%" . $search . "%")->paginate(9);
        } else {
            $lastRecord     = TutorialVideos::count();
            $marker         = $this->academyMarkers($lastRecord, request()->page);
            $tutorialVideos = TutorialVideos::orderBy("id", $filter)->paginate(9);
        }
        return view("business.academy", compact("tutorialVideos", "search", "lastRecord", "marker", "filter"));
    }

    public function WalletTransactions()
    {
        return view("business.payment_methods");
    }

    public function shopMarkers($lastRecord, $pageNum)
    {
        if ($pageNum == null) {
            $pageNum = 1;
        }
        $end    = (12 * ((int) $pageNum));
        $marker = [];
        if ((int) $pageNum == 1) {
            $marker["begin"] = (int) $pageNum;
            $marker["index"] = (int) $pageNum;
        } else {
            $marker["begin"] = number_format(((12 * ((int) $pageNum)) - 11), 0);
            $marker["index"] = number_format(((12 * ((int) $pageNum)) - 11), 0);
        }

        if ($end > $lastRecord) {
            $marker["end"] = number_format($lastRecord, 0);
        } else {
            $marker["end"] = number_format($end, 0);
        }

        return $marker;
    }

    public function academyMarkers($lastRecord, $pageNum)
    {
        if ($pageNum == null) {
            $pageNum = 1;
        }
        $end    = (9 * ((int) $pageNum));
        $marker = [];
        if ((int) $pageNum == 1) {
            $marker["begin"] = (int) $pageNum;
            $marker["index"] = (int) $pageNum;
        } else {
            $marker["begin"] = number_format(((9 * ((int) $pageNum)) - 8), 0);
            $marker["index"] = number_format(((9 * ((int) $pageNum)) - 8), 0);
        }

        if ($end > $lastRecord) {
            $marker["end"] = number_format($lastRecord, 0);
        } else {
            $marker["end"] = number_format($end, 0);
        }

        return $marker;
    }

    public function updatePageSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id'       => 'required',
            'banner_type'       => 'required',
            'catalogue_display' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errors = implode("<br>", $errors);
            toast($errors, 'error');
            return back();
        }

        $business                    = Business::find($request->business_id);
        $business->page_banner       = $request->banner_type;
        $business->catalogue_display = $request->catalogue_display;
        if ($business->save()) {
            toast('Page Settings Updated Successfully.', 'success');
            return back();
        } else {
            toast('Something Went Wrong.', 'error');
            return back();
        }

    }

    public function updateTopBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'top_banner'  => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:max_width=1920,max_height=360',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errors = implode("<br>", $errors);
            toast($errors, 'error');
            return back();
        }
        try {

            $fileName      = $request->file('top_banner')->getClientOriginalName();
            $byteSize      = $request->file('top_banner')->getSize();
            $fileSize      = $this->formatFileSize($byteSize);
            $fileExtension = $request->file('top_banner')->getClientOriginalExtension();
            $fileURL       = Cloudinary::uploadFile($request->file('top_banner')->getRealPath())->getSecurePath();

            $bannerExist = BusinessPage::where("business_id", $request->business_id)->where("file_position", "banner")->first();
            if (isset($bannerExist)) {
                $bannerExist->file_url       = $fileURL;
                $bannerExist->file_url       = $fileURL;
                $bannerExist->file_name      = $fileName;
                $bannerExist->file_size      = $fileSize;
                $bannerExist->file_extension = $fileExtension;
                if ($bannerExist->save()) {
                    toast('Static Page Banner Updated Successfully.', 'success');
                    return back();
                } else {
                    toast('Something Went Wrong.', 'error');
                    return back();
                }
            } else {
                $banner                 = new BusinessPage;
                $banner->business_id    = $request->business_id;
                $banner->file_position  = "banner";
                $banner->file_type      = "image";
                $banner->file_url       = $fileURL;
                $banner->file_name      = $fileName;
                $banner->file_size      = $fileSize;
                $banner->file_extension = $fileExtension;
                if ($banner->save()) {
                    toast('Static Page Banner Updated Successfully.', 'success');
                    return back();
                } else {
                    toast('Something Went Wrong.', 'error');
                    return back();
                }
            }
        } catch (\Exception $e) {
            toast('We are having issues connecting to our cloud file management server. Please try again later.', 'error');
            return back();
        }
    }

    public function uploadSliderBanner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id'   => 'required',
            'slider_banner' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048|dimensions:max_width=1500,max_height=450',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errors = implode("<br>", $errors);
            toast($errors, 'error');
            return back();
        }

        try {
            $fileName      = $request->file('slider_banner')->getClientOriginalName();
            $byteSize      = $request->file('slider_banner')->getSize();
            $fileSize      = $this->formatFileSize($byteSize);
            $fileExtension = $request->file('slider_banner')->getClientOriginalExtension();
            $fileURL       = Cloudinary::uploadFile($request->file('slider_banner')->getRealPath())->getSecurePath();

            $banner                 = new BusinessPage;
            $banner->business_id    = $request->business_id;
            $banner->file_position  = "slider";
            $banner->file_type      = "image";
            $banner->file_url       = $fileURL;
            $banner->file_name      = $fileName;
            $banner->file_size      = $fileSize;
            $banner->file_extension = $fileExtension;
            if ($banner->save()) {
                toast('Slider Banner Uploaded Successfully.', 'success');
                return back();
            } else {
                toast('Something Went Wrong.', 'error');
                return back();
            }
        } catch (\Exception $e) {
            toast('We are having issues connecting to our cloud file management server. Please try again later.', 'error');
            return back();
        }

    }

    public function uploadCatalogue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id'     => 'required',
            'catalogue_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errors = implode("<br>", $errors);
            toast($errors, 'error');
            return back();
        }
        try {

            $fileName      = $request->file('catalogue_image')->getClientOriginalName();
            $byteSize      = $request->file('catalogue_image')->getSize();
            $fileSize      = $this->formatFileSize($byteSize);
            $fileExtension = $request->file('catalogue_image')->getClientOriginalExtension();
            $fileURL       = Cloudinary::uploadFile($request->file('catalogue_image')->getRealPath())->getSecurePath();

            $banner                 = new BusinessPage;
            $banner->business_id    = $request->business_id;
            $banner->file_position  = "catalogue";
            $banner->file_type      = "image";
            $banner->file_url       = $fileURL;
            $banner->file_name      = $fileName;
            $banner->file_size      = $fileSize;
            $banner->file_extension = $fileExtension;
            if ($banner->save()) {
                toast('Business Catalogue Image Uploaded Successfully.', 'success');
                return back();
            } else {
                toast('Something Went Wrong.', 'error');
                return back();
            }
        } catch (\Exception $e) {
            toast('We are having issues connecting to our cloud file management server. Please try again later.', 'error');
            return back();
        }

    }

    public function removePageFile($id)
    {
        $pageFile = BusinessPage::find($id);
        if (isset($pageFile)) {
            if ($pageFile->delete()) {
                toast('File Deleted Successfully.', 'success');
                return back();
            } else {
                toast('Something Went Wrong.', 'error');
                return back();
            }
        } else {
            toast('Something Went Wrong.', 'error');
            return back();
        }
    }

    public function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i     = 0;

        while ($size > 1024) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function reviewBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required',
            'rating'      => 'required',
            'review'      => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash("alert-type", "error");
            Session::flash("message", "Please fill the review form");
            return back();
        }

        $review              = new BusinessReviews;
        $review->business_id = $request->business_id;
        $review->customer_id = Auth::user()->id;
        $review->rating      = $request->rating;
        $review->review      = $request->review;
        if ($review->save()) {
            Session::flash("alert-type", "success");
            Session::flash("message", "Review Submitted Successfully.");
            return back();
        } else {
            Session::flash("alert-type", "error");
            Session::flash("message", "Something Went Wrong.");
            return back();
        }
    }
}
