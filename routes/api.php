<?php
// use App\Http\Controllers\Mobile\AuthenticationController;
// use App\Http\Controllers\Mobile\BusinessAccountController;
// use App\Http\Controllers\Mobile\ChatController;
// use App\Http\Controllers\Mobile\GeneralSettingsController;
// use App\Http\Controllers\Mobile\JobsController;
// use App\Http\Controllers\Mobile\NotificationController;
// use App\Http\Controllers\Mobile\OnboardingController;
// use App\Http\Controllers\Mobile\ReferralController;
// use App\Http\Controllers\Mobile\SubscriptionController;
// use App\Http\Controllers\Mobile\UtilityController;
// use App\Http\Controllers\Mobile\WalletController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::group([
//     'middleware' => ['api'],
//     'prefix' => 'v1',

// ], function ($router) {

//     Route::group([
//         'prefix' => 'settings',
//     ], function ($router) {
//         Route::get('/application-key', [GeneralSettingsController::class, 'getApplicationKey']);
//     });

//     Route::group([
//         'middleware' => ['validatemobilekey'],
//     ], function ($router) {
//         Route::post('/register', [OnboardingController::class, 'register']);
//         Route::post('/login', [AuthenticationController::class, 'login']);
//         Route::post('/initiate-password-reset', [AuthenticationController::class, 'initiatePasswordReset']);
//         Route::post('/password-reset-verification', [AuthenticationController::class, 'passwordResetVerification']);
//         Route::post('/create-new-password', [AuthenticationController::class, 'createNewPassword']);
//         Route::get('/platform-details', [GeneralSettingsController::class, 'platformInfomation']);
//         Route::get('/platform-categories', [GeneralSettingsController::class, 'platformCategories']);

//         Route::group([
//             'middleware' => ['mobileauthenticated'],
//         ], function () {
//             Route::post('/send-verification-mail', [OnboardingController::class, 'sendVerificationMail']);
//             Route::post('/verify-email', [OnboardingController::class, 'verifyEmail']);

//             Route::group([
//                 'middleware' => ['emailverified'],
//             ], function ($router) {
//                 Route::post('/select-account-type', [OnboardingController::class, 'selectAccountType']);

//                 Route::group([
//                     'prefix' => 'business',
//                     'middleware' => 'validatebusiness',

//                 ], function ($router) {
//                     Route::get('/dashboard', [BusinessAccountController::class, 'dashboard']);
//                     Route::post('/account-pin-setup', [BusinessAccountController::class, 'setupAccountPin']);
//                     Route::post('/update-photo', [BusinessAccountController::class, 'updateProfilePhoto']);
//                     Route::get('/view-profile', [BusinessAccountController::class, 'viewProfile']);
//                     Route::post('/update-username', [BusinessAccountController::class, 'updateUsername']);
//                     Route::post('/update-email', [BusinessAccountController::class, 'updateEmail']);
//                     Route::post('/update-phone', [BusinessAccountController::class, 'updatePhoneNumber']);
//                     Route::post('/update-country', [BusinessAccountController::class, 'updateCountry']);
//                     Route::post('/update-password', [BusinessAccountController::class, 'updatePassword']);
//                     Route::get('/view-notification-settings', [BusinessAccountController::class, 'viewNotificationSettings']);
//                     Route::post('/update-notification-settings', [BusinessAccountController::class, 'updateNotificationSettings']);
//                     Route::get('/view-business-details', [BusinessAccountController::class, 'viewBusinessDetails']);
//                     Route::post('/update-business-name', [BusinessAccountController::class, 'updateBusinessName']);
//                     Route::post('/update-business-phone', [BusinessAccountController::class, 'updateBusinessPhone']);
//                     Route::post('/update-business-email', [BusinessAccountController::class, 'updateBusinessEmail']);
//                     Route::post('/update-business-description', [BusinessAccountController::class, 'updateBusinessDescription']);
//                     Route::post('/update-business-category', [BusinessAccountController::class, 'updateBusinessCategory']);
//                     Route::post('/update-business-address', [BusinessAccountController::class, 'updateBusinessAddress']);
//                     Route::post('/update-business-socials', [BusinessAccountController::class, 'updateBusinessSocials']);
//                     Route::post('/update-business-website', [BusinessAccountController::class, 'updateBusinessWebsite']);
//                     Route::post('/update-logo', [BusinessAccountController::class, 'updateBusinessLogo']);
//                     Route::post('/delete-account', [BusinessAccountController::class, 'deleteAccount']);
//                     Route::get('/view-notifications', [NotificationController::class, 'viewNotifications']);
//                     Route::post('/delete-notification', [NotificationController::class, 'deleteNotification']);
//                     Route::post('/clear-notifications', [NotificationController::class, 'clearNotifications']);
//                     Route::get('/view-referrals', [ReferralController::class, 'viewReferrals']);
//                     Route::get('/referral-link', [ReferralController::class, 'referralLink']);
//                     Route::get('/referral-stats', [ReferralController::class, 'referralStats']);
//                     Route::get('/referral-details', [ReferralController::class, 'referralDetails']);
//                     Route::post('/initiate-card-addition', [WalletController::class, 'initiateCardAddition']);
//                     Route::post('/add-new-card', [WalletController::class, 'addNewCard']);
//                     Route::get('/view-stored-cards', [WalletController::class, 'viewStoredCards']);
//                     Route::post('/delete-card', [WalletController::class, 'deleteCard']);
//                     Route::get('/wallet-balances', [WalletController::class, 'walletBalances']);
//                     Route::get('/view-referral-points', [WalletController::class, 'viewReferralPoints']);
//                     Route::get('/view-arete-wallet-balance', [WalletController::class, 'viewJobWalletBalance']);
//                     Route::get('/view-referral-transactions', [WalletController::class, 'viewReferralTransactions']);
//                     Route::get('/view-arete-wallet-transactions', [WalletController::class, 'viewAreteTransactions']);
//                     Route::get('/card-transactions', [WalletController::class, 'viewCardTransactions']);
//                     Route::post('/fund-arete-wallet', [WalletController::class, 'fundAreteWallet']);
//                     Route::get('/bank-list', [WalletController::class, 'bankList']);
//                     Route::post('/validate-bank-account', [WalletController::class, 'validateBankAccount']);
//                     Route::post('/withdraw-funds', [WalletController::class, 'withdrawFunds']);
//                     Route::get('/view-contact-list', [ChatController::class, 'viewContactList']);
//                     Route::post('/search-contact-list', [ChatController::class, 'searchContactList']);
//                     Route::post('/initialize-chat', [ChatController::class, 'initializeChat']);
//                     Route::post('/new-chat', [ChatController::class, 'newChat']);
//                     Route::get('/chat-conversations', [ChatController::class, 'chatConversations']);
//                     Route::post('/delete-chat', [ChatController::class, 'deleteChat']);
//                     Route::post('/delete-conversation', [ChatController::class, 'deleteConversation']);
//                     Route::get('/airtime-providers', [UtilityController::class, 'airtimeProviders']);
//                     Route::post('/initiate-airtime-purchase', [UtilityController::class, 'initiateAirtimePurchase']);
//                     Route::post('/buy-airtime', [UtilityController::class, 'buyAirtime']);
//                     Route::get('/data-providers', [UtilityController::class, 'dataProviders']);
//                     Route::get('/data-plans', [UtilityController::class, 'dataPlans']);
//                     Route::post('/initiate-data-purchase', [UtilityController::class, 'initiateDataPurchase']);
//                     Route::post('/buy-data', [UtilityController::class, 'buyData']);
//                     Route::get('/electricity-providers', [UtilityController::class, 'electricityProviders']);
//                     Route::post('/initiate-electricity-purchase', [UtilityController::class, 'initiateElectricityPurchase']);
//                     Route::post('/buy-electricity-units', [UtilityController::class, 'buyElectricity']);
//                     Route::get('/cable-providers', [UtilityController::class, 'cableProviders']);
//                     Route::get('/cable-plans', [UtilityController::class, 'cablePlans']);
//                     Route::post('/initiate-cable-purchase', [UtilityController::class, 'initiateCablePurchase']);
//                     Route::post('/buy-cable-subscription', [UtilityController::class, 'buyCable']);
//                     Route::get('/utility-transactions', [UtilityController::class, 'utilityTransactions']);
//                     Route::get('/subscription-plans', [SubscriptionController::class, 'subscriptionPlans']);
//                     Route::get('/active-subscription', [SubscriptionController::class, 'activeSubscription']);
//                     Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
//                     Route::post('/set-auto-renewal', [SubscriptionController::class, 'setAutoRenewal']);

//                     Route::group([
//                         'middleware' => 'businessprofileupdated',
//                     ], function ($router) {
//                         Route::get('/generate-tracking-code', [JobsController::class, 'generateTrackingCode']);
//                         Route::post('/create-new-job', [JobsController::class, 'createNewJob']);
//                         Route::post('/update-job-details', [JobsController::class, 'updateJobDetails']);
//                         Route::get('/view-jobs', [JobsController::class, 'viewJobs']);
//                         Route::get('/filter-jobs', [JobsController::class, 'filterJobs']);
//                         Route::get('/view-job-details', [JobsController::class, 'viewJobDetails']);
//                         Route::post('/delete-job', [JobsController::class, 'deleteJob']);
//                         Route::post('/upload-project-assets', [JobsController::class, 'uploadProjectAssets']);
//                         Route::post('/delete-project-asset', [JobsController::class, 'deleteProjectAsset']);

//                     });
//                 });

//                 Route::group([
//                     'prefix' => 'artisan',

//                 ], function ($router) {
//                     Route::post('/account-pin-setup', [OnboardingController::class, 'setupAccountPin']);
//                     Route::post('/upload-photo', [OnboardingController::class, 'uploadProfilePhoto']);
//                     Route::get('/profile', [AccountController::class, 'profile']);
//                 });

//             });

//         });

//     });

// });
