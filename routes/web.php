<?php

use App\Http\Controllers\Auth\ForumLoginController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\FrontEndController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/', [FrontEndController::class, 'index']);

Route::get('/business-listing', [FrontEndController::class, 'businessListing']);

Route::get('/business-categories', [FrontEndController::class, 'businessCategories']);

Route::get('/business-categories/{slug}', [FrontEndController::class, 'listingByCategories']);

Route::get('/business/details/{slug}', [FrontEndController::class, 'businessDetails']);

Route::get('/job-portal', [FrontEndController::class, 'jobPortal']);

Route::get('/job/details/{slug}', [FrontEndController::class, 'jobDetails']);

// Route::get('/shop-now', [FrontEndController::class, 'shop']);

// Route::get('/academy', [FrontEndController::class, 'academy']);

Route::get('/blog', [FrontEndController::class, 'blogPosts']);

Route::get('/blog/{slug}', [FrontEndController::class, 'blogDetails'])->name("blogDetails");

Route::get('/about-us', [FrontEndController::class, 'aboutUs']);

Route::get('/contact-us', [FrontEndController::class, 'contactUs']);

Route::post('/processContactForm', [FrontEndController::class, 'processContactForm'])->name("processContactForm");

Route::get('/terms-and-conditions', [FrontEndController::class, 'terms']);

Route::get('/privacy-policy', [FrontEndController::class, 'privacyPolicy']);

Route::get('/cookie-policy', [FrontEndController::class, 'cookiePolicy']);

Route::get('/faqs', [FrontEndController::class, 'faqs']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/authy', [App\Http\Controllers\HomeController::class, 'authy'])->name('authy');

Route::post('/verify-email', [App\Http\Controllers\OnboardingController::class, 'verifyEmail'])->name("verifyEmail");

Route::get('/send-verification-mail', [App\Http\Controllers\OnboardingController::class, 'sendVerificationMail'])->name("sendVerificationMail");

Route::post('/initiate-password-reset', [App\Http\Controllers\OnboardingController::class, 'initiatePasswordReset'])->name("initiatePasswordReset");

Route::get('/password-reset-confirmation', [App\Http\Controllers\OnboardingController::class, 'pwdResetConfirmation'])->name("pwdResetConfirmation");

Route::post('/password-reset-verification', [App\Http\Controllers\OnboardingController::class, 'passwordResetVerification'])->name("passwordResetVerification");

Route::get('/new-password', [App\Http\Controllers\OnboardingController::class, 'newPassword'])->name("newPassword");

Route::post('/create-new-password', [App\Http\Controllers\OnboardingController::class, 'createNewPassword'])->name("createNewPassword");

Route::get('/account-selection', [App\Http\Controllers\OnboardingController::class, 'accountSelection'])->name("accountSelection");

Route::get('/select-account/{accountType}', [App\Http\Controllers\OnboardingController::class, 'selectAccountType'])->name("selectAccount");

Route::group([
    'middleware' => ['emailverified', 'webauthenticated', 'g2fa'],
], function ($router) {

    Route::get('/business/dashboard', [App\Http\Controllers\Business\HomeController::class, 'dashboard'])->name('business.dashboard');

    Route::get('/artisan/dashboard', [App\Http\Controllers\Artisan\HomeController::class, 'dashboard'])->name('artisan.dashboard');

});

Route::group([
    'middleware' => ['emailverified'],
], function ($router) {
    Route::post('/select-account-type', [OnboardingController::class, 'selectAccountType']);

    Route::group([
        'prefix'     => 'business',
        'middleware' => ['validatebusiness', 'webauthenticated', 'g2fa'],

    ], function ($router) {

        Route::get('/view-profile', [App\Http\Controllers\Business\HomeController::class, 'viewProfile'])->name("business.viewProfile");

        Route::get('/change-password', [App\Http\Controllers\Business\HomeController::class, 'changePassword'])->name("business.changePassword");

        Route::post('/update-profile', [App\Http\Controllers\Business\HomeController::class, 'updateProfile'])->name("business.updateProfile");

        Route::post('/update-password', [App\Http\Controllers\Business\HomeController::class, 'updatePassword'])->name("business.updatePassword");

        Route::group([
            'middleware' => ['usersubscribed'],

        ], function ($router) {

            Route::get('/business-profile', [App\Http\Controllers\Business\HomeController::class, 'businessProfile'])->name("business.businessProfile");

            Route::post('/update-business-profile', [App\Http\Controllers\Business\HomeController::class, 'updateBusinessProfile'])->name("business.updateBusinessProfile");

            Route::get('/business-page-setup', [App\Http\Controllers\Business\HomeController::class, 'businessPage'])->name("business.businessPage");

            Route::post('/update-page-settings', [App\Http\Controllers\Business\HomeController::class, 'updatePageSettings'])->name("business.updatePageSettings");

            Route::post('/update-top-banner', [App\Http\Controllers\Business\HomeController::class, 'updateTopBanner'])->name("business.updateTopBanner");

            Route::post('/update-slider-banner', [App\Http\Controllers\Business\HomeController::class, 'uploadSliderBanner'])->name("business.uploadSliderBanner");

            Route::post('/upload-catalogue', [App\Http\Controllers\Business\HomeController::class, 'uploadCatalogue'])->name("business.uploadCatalogue");

            Route::get('/remove-page-file/{id}', [App\Http\Controllers\Business\HomeController::class, 'removePageFile'])->name("business.removePageFile");

            Route::get('/job-listing', [App\Http\Controllers\Business\JobListingController::class, 'jobListings'])->name("business.jobListing");

            Route::get('/job/details/{id}', [App\Http\Controllers\Business\JobListingController::class, 'jobDetails'])->name("business.jobDetails");

            Route::get('/job/delete/{id}', [App\Http\Controllers\Business\JobListingController::class, 'deleteJob'])->name("business.deleteJob");

            Route::get('/job/archive/{id}', [App\Http\Controllers\Business\JobListingController::class, 'archiveJob'])->name("business.archiveJob");

            Route::get('/job/publish/{id}', [App\Http\Controllers\Business\JobListingController::class, 'publishJob'])->name("business.publishJob");

        });

        Route::get('/notification-settings', [App\Http\Controllers\Business\HomeController::class, 'notificationSettings'])->name("business.notificationSettings");

        Route::get('/notification/unsubscribe-all', [App\Http\Controllers\Business\HomeController::class, 'unsubscribeAllNotifications'])->name("business.unsubscribeAllNotifications");

        Route::get('/toggleAllNotificationSettings', [App\Http\Controllers\Business\HomeController::class, 'toggleAllNotificationSettings'])->name('business.toggleAllNotificationSettings');

        Route::get('/toggleSpecificNotificationSettings', [App\Http\Controllers\Business\HomeController::class, 'toggleSpecificNotificationSettings'])->name('business.toggleSpecificNotificationSettings');

        Route::get('/security', [App\Http\Controllers\Business\HomeController::class, 'security'])->name("business.security");

        Route::post('/select2FA', [App\Http\Controllers\Business\HomeController::class, 'select2FA'])->name("business.select2FA");

        Route::post('/enableGA', [App\Http\Controllers\Business\HomeController::class, 'enableGA'])->name("business.enableGA");

        Route::post('/selectWithdrawalConfirmation', [App\Http\Controllers\Business\HomeController::class, 'selectWithdrawalConfirmation'])->name("business.selectWithdrawalConfirmation");

        Route::get('/referrals', [App\Http\Controllers\Business\ReferralController::class, 'referrals'])->name("business.referrals");

        Route::get('/utility-transactions', [App\Http\Controllers\Business\UtilityController::class, 'utilityTransactions'])->name("business.utilityTransactions");

        Route::get('/buy-airtime', [App\Http\Controllers\Business\UtilityController::class, 'buyAirtime'])->name("business.buyAirtime");

        Route::get('/buy-airtime/preview/{id}', [App\Http\Controllers\Business\UtilityController::class, 'airtimePreview'])->name("business.airtimePreview");

        Route::get('/airtime-purchase/points/{id}', [App\Http\Controllers\Business\UtilityController::class, 'pointsAirtimePurchase'])->name("business.pointsAirtimePurchase");

        Route::get('/airtime-purchase/wallet/{id}', [App\Http\Controllers\Business\UtilityController::class, 'walletAirtimePurchase'])->name("business.walletAirtimePurchase");

        Route::post('airtimePurchasePreview', [App\Http\Controllers\Business\UtilityController::class, 'airtimePurchasePreview'])->name("business.airtimePurchasePreview");

        Route::get('/buy-data', [App\Http\Controllers\Business\UtilityController::class, 'buyData'])->name("business.buyData");

        Route::get('/buy-data/preview/{id}', [App\Http\Controllers\Business\UtilityController::class, 'dataPreview'])->name("business.dataPreview");

        Route::get('/data/plans/{provider}', [App\Http\Controllers\Business\UtilityController::class, 'retrieveDataPlans'])->name('business.data.plans');

        Route::post('dataPurchasePreview', [App\Http\Controllers\Business\UtilityController::class, 'dataPurchasePreview'])->name("business.dataPurchasePreview");

        Route::get('/data-purchase/points/{id}', [App\Http\Controllers\Business\UtilityController::class, 'pointsDataPurchase'])->name("business.pointsDataPurchase");

        Route::get('/data-purchase/wallet/{id}', [App\Http\Controllers\Business\UtilityController::class, 'walletDataPurchase'])->name("business.walletDataPurchase");

        Route::get('/buy-cable', [App\Http\Controllers\Business\UtilityController::class, 'buyCable'])->name("business.buyCable");

        Route::get('/cable/plans/{provider}', [App\Http\Controllers\Business\UtilityController::class, 'retrieveCablePlans'])->name('business.cable.plans');

        Route::post('cablePurchasePreview', [App\Http\Controllers\Business\UtilityController::class, 'cablePurchasePreview'])->name("business.cablePurchasePreview");

        Route::get('/buy-cable/preview/{id}', [App\Http\Controllers\Business\UtilityController::class, 'cablePreview'])->name("business.cablePreview");

        Route::get('/cable-purchase/wallet/{id}', [App\Http\Controllers\Business\UtilityController::class, 'walletCablePurchase'])->name("business.walletCablePurchase");

        Route::get('/cable-purchase/points/{id}', [App\Http\Controllers\Business\UtilityController::class, 'pointsCablePurchase'])->name("business.pointsCablePurchase");

        Route::get('/buy-electricity', [App\Http\Controllers\Business\UtilityController::class, 'buyElectricity'])->name("business.buyElectricity");

        Route::post('electricityPurchasePreview', [App\Http\Controllers\Business\UtilityController::class, 'electricityPurchasePreview'])->name("business.electricityPurchasePreview");

        Route::get('/buy-electricity/preview/{id}', [App\Http\Controllers\Business\UtilityController::class, 'electricityPreview'])->name("business.electricityPreview");

        Route::get('/electricity-purchase/points/{id}', [App\Http\Controllers\Business\UtilityController::class, 'pointsElectricityPurchase'])->name("business.pointsElectricityPurchase");

        Route::get('/electricity-purchase/wallet/{id}', [App\Http\Controllers\Business\UtilityController::class, 'walletElectricityPurchase'])->name("business.walletElectricityPurchase");

        Route::get('/jobs/initialize', [App\Http\Controllers\Business\JobListingController::class, 'initializeNewJob'])->name("business.initializeNewJob");

        Route::get('/jobs/new', [App\Http\Controllers\Business\JobListingController::class, 'newJobListing'])->name("business.newJobListing");

        Route::get('/job/update/{id}', [App\Http\Controllers\Business\JobListingController::class, 'editJobDetails'])->name("business.updateJobDetails");

        Route::post('/jobs/save', [App\Http\Controllers\Business\JobListingController::class, 'storeJobListing'])->name("business.storeJobListing");

        Route::post('/jobs/update', [App\Http\Controllers\Business\JobListingController::class, 'updateJobListing'])->name("business.updateJobListing");

        Route::get('/subscription', [App\Http\Controllers\Business\SubscriptionController::class, 'subscription'])->name("business.subscription");

        Route::get('/initiate-subscription', [App\Http\Controllers\Business\SubscriptionController::class, 'initiateSubscription'])->name("business.subscribe");

        Route::get('/subscription/{id}', [App\Http\Controllers\Business\SubscriptionController::class, 'previewSubscription'])->name("business.previewSubscription");

        Route::post('/subscription/process', [App\Http\Controllers\Business\SubscriptionController::class, 'processSubscription'])->name("business.processSubscription");

        Route::post('/set-auto-renewal', [App\Http\Controllers\Business\SubscriptionController::class, 'setAutoRenewal'])->name("business.setAutoRenewal");

        Route::get('/default-card/{id}', [App\Http\Controllers\Business\SubscriptionController::class, 'defaultCard'])->name("business.defaultCard");

        Route::get('/delete-card/{id}', [App\Http\Controllers\Business\SubscriptionController::class, 'deleteCard'])->name("business.deleteCard");

        Route::post('/initiateCardAddition', [App\Http\Controllers\Business\SubscriptionController::class, 'initiateCardAddition'])->name("business.initiateCardAddition");

        Route::get('/delete-account', [App\Http\Controllers\Business\HomeController::class, 'deleteAccount'])->name("business.deleteAccount");

        Route::get('/process-account-deletion', [App\Http\Controllers\Business\HomeController::class, 'processAccountDeletion'])->name("business.processAccountDeletion");

        Route::get('/mini-store', [App\Http\Controllers\Business\HomeController::class, 'miniStore'])->name("business.miniStore");

        Route::get('/academy', [App\Http\Controllers\Business\HomeController::class, 'academy'])->name("business.academy");

        Route::get('/wallet', [App\Http\Controllers\Business\WalletController::class, 'myWallet'])->name("business.myWallet");

        Route::get('/wallet/withdrawals', [App\Http\Controllers\Business\WalletController::class, 'withdrawals'])->name("business.myWalletWithdrawals");

        Route::get('/wallet/points', [App\Http\Controllers\Business\WalletController::class, 'pointsTransaction'])->name("business.myWalletPoints");

        Route::post('initiateWalletTopup', [App\Http\Controllers\Business\WalletController::class, 'initiateWalletTopup'])->name("business.initiateWalletTopup");

        Route::post('initiateWalletWithdrawal', [App\Http\Controllers\Business\WalletController::class, 'initiateWalletWithdrawal'])->name("business.initiateWalletWithdrawal");

        Route::post('validateAccount', [App\Http\Controllers\Business\WalletController::class, 'validateBankAccount'])->name("business.validateAccount");

        Route::post('reviewBusiness', [App\Http\Controllers\Business\HomeController::class, 'reviewBusiness'])->name("business.reviewBusiness");

        Route::post('initiateBonusWithdrawal', [App\Http\Controllers\Business\WalletController::class, 'initiateBonusWithdrawal'])->name("business.initiateBonusWithdrawal");

    });

});

Route::group([
    'prefix'     => 'forum',
    'middleware' => ['forum'],
], function ($router) {
    Route::get('/', [ForumController::class, 'index'])->name("forum");

    Route::get('/user/{id?}', [ForumController::class, 'userDetails'])->name("userDetails");

    Route::get('/popular-posts', [ForumController::class, 'popularPosts'])->name("forum.popularPosts");

    Route::get('/bookmarks', [ForumController::class, 'bookmarks'])->name("forum.bookmarks");

    Route::get('/category/{category}/posts', [ForumController::class, 'categoryPosts']);

    Route::get('/topic/{topic}/posts', [ForumController::class, 'topicPosts']);

    Route::get('/details/{id}/{slug}', [ForumController::class, 'postDetails']);

    Route::post('/store-post', [ForumController::class, 'userPostStore'])->name("forum.userPostStore");

    Route::post('/update-post', [ForumController::class, 'userPostUpdate'])->name("forum.userPostUpdate");

    Route::get('/edit-post/{id}', [ForumController::class, 'userPostEdit'])->name("forum.userPostEdit");

    Route::post('/delete-image', [ForumController::class, 'userDeleteImage'])->name("forum.userDeleteImage");

    Route::post('/vote-post', [ForumController::class, 'votePost'])->name("forum.votePost");

    Route::post('/vote-comment', [ForumController::class, 'voteComment'])->name("forum.voteComment");

    Route::post('/report-post', [ForumController::class, 'reportPost'])->name("forum.reportPost");

    Route::post('/report-comment', [ForumController::class, 'reportComment'])->name("forum.reportComment");

    Route::post('/bookmark-post', [ForumController::class, 'bookmarkPost'])->name("forum.bookmarkPost");

    Route::post('/submit-comment', [ForumController::class, 'submitComment'])->name("forum.submitComment");

    Route::post('/update-comment', [ForumController::class, 'updateComment'])->name("forum.updateComment");

    Route::post('/delete-comment', [ForumController::class, 'deleteComment'])->name("forum.deleteComment");

    Route::post('/delete-reply', [ForumController::class, 'deleteReply'])->name("forum.deleteReply");

    Route::post('/reply-comment', [ForumController::class, 'replyComment'])->name("forum.replyComment");

    Route::get('/render-html', [ForumController::class, 'testHTMLRendering']);

    Route::post('/store-topic', [ForumController::class, 'storeTopic'])->name("forum.userTopicStore");

    Route::get('/login', [ForumController::class, 'login'])->name("forum.login");

    Route::post('/process-login', [ForumLoginController::class, 'login'])->name("forum.processLogin");

    Route::get('/change/{lang}', [ForumController::class, 'changeLanguage']);
});

Route::group([
    'prefix'     => 'mobile/view',

    'middleware' => ['forum'],
], function ($router) {
    Route::get('/business-details/{id}', [App\Http\Controllers\MobileViews::class, 'businessDetails']);

    Route::get('/load-forum/{id}', [App\Http\Controllers\MobileViews::class, 'loadForum']);

    Route::get('/forum', [App\Http\Controllers\MobileViews::class, 'forum'])->name("mobileView.forum");

    Route::get('/forum/popular-posts', [App\Http\Controllers\MobileViews::class, 'popularPosts'])->name("mobileView.popularPosts");

    Route::get('/forum/bookmarks', [App\Http\Controllers\MobileViews::class, 'bookmarks'])->name("mobileView.bookmarks");

    Route::get('/forum/category/{category}/posts', [App\Http\Controllers\MobileViews::class, 'categoryPosts'])->name("mobileView.categoryPosts");

    Route::get('/forum/topic/{topic}/posts', [App\Http\Controllers\MobileViews::class, 'topicPosts'])->name("mobileView.topicPosts");

    Route::get('/forum/details/{id}/{slug}', [App\Http\Controllers\MobileViews::class, 'postDetails'])->name("mobileView.postDetails");

    Route::get('/forum/edit-post/{id}', [App\Http\Controllers\MobileViews::class, 'userPostEdit'])->name("mobileView.userPostEdit");

    Route::get('/forum/render-html', [App\Http\Controllers\MobileViews::class, 'testHTMLRendering'])->name("mobileView.testHTMLRendering");

    Route::get('/terms', [App\Http\Controllers\MobileViews::class, 'terms']);

    Route::get('/privacy', [App\Http\Controllers\MobileViews::class, 'privacyPolicy']);

});

Route::post('/login/2fa', [App\Http\Controllers\Business\TwofactorController::class, 'verify2FA'])->name('login.2fa');

Route::post('/login/validate2fa', [App\Http\Controllers\Business\TwofactorController::class, 'validate2fa'])->name('login.validate2fa');

Route::get('/resolve-bank', [App\Http\Controllers\FrontEndController::class, 'bankList']);

Route::get('/paystack/topup/callback', [App\Http\Controllers\Business\WalletController::class, 'handlePaystackCallback']);

Route::get('/paystack/subscription/callback', [App\Http\Controllers\Business\SubscriptionController::class, 'handlePaystackCallback']);

Route::get('/cron/renew-subscription', [App\Http\Controllers\CronController::class, 'renewSubscription']);

Route::get('/cron/expired-subscriptions', [App\Http\Controllers\CronController::class, 'expiredSubscriptions']);

Route::get('/cron/close-transactions', [App\Http\Controllers\CronController::class, 'closeInitiatedTransactions']);

Route::get('/cron/pending-airtime', [App\Http\Controllers\CronController::class, 'checkPendingAirtime']);

Route::get('/cron/pending-data', [App\Http\Controllers\CronController::class, 'checkPendingData']);

Route::get('/cron/pending-electricity', [App\Http\Controllers\CronController::class, 'checkPendingElectricity']);

Route::get('/cron/pending-cable', [App\Http\Controllers\CronController::class, 'checkPendingCable']);
