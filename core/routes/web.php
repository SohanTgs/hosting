<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/  


Route::get('/cron', 'CronController@index')->name('index');
 

Route::namespace('Gateway')->prefix('ipn')->name('ipn.')->group(function () {
    Route::post('paypal', 'Paypal\ProcessController@ipn')->name('Paypal');
    Route::get('paypal-sdk', 'PaypalSdk\ProcessController@ipn')->name('PaypalSdk');
    Route::post('perfect-money', 'PerfectMoney\ProcessController@ipn')->name('PerfectMoney');
    Route::post('stripe', 'Stripe\ProcessController@ipn')->name('Stripe');
    Route::post('stripe-js', 'StripeJs\ProcessController@ipn')->name('StripeJs');
    Route::post('stripe-v3', 'StripeV3\ProcessController@ipn')->name('StripeV3');
    Route::post('skrill', 'Skrill\ProcessController@ipn')->name('Skrill');
    Route::post('paytm', 'Paytm\ProcessController@ipn')->name('Paytm');
    Route::post('payeer', 'Payeer\ProcessController@ipn')->name('Payeer');
    Route::post('paystack', 'Paystack\ProcessController@ipn')->name('Paystack');
    Route::post('voguepay', 'Voguepay\ProcessController@ipn')->name('Voguepay');
    Route::get('flutterwave/{trx}/{type}', 'Flutterwave\ProcessController@ipn')->name('Flutterwave');
    Route::post('razorpay', 'Razorpay\ProcessController@ipn')->name('Razorpay');
    Route::post('instamojo', 'Instamojo\ProcessController@ipn')->name('Instamojo');
    Route::get('blockchain', 'Blockchain\ProcessController@ipn')->name('Blockchain');
    Route::get('blockio', 'Blockio\ProcessController@ipn')->name('Blockio');
    Route::post('coinpayments', 'Coinpayments\ProcessController@ipn')->name('Coinpayments');
    Route::post('coinpayments-fiat', 'Coinpayments_fiat\ProcessController@ipn')->name('CoinpaymentsFiat');
    Route::post('coingate', 'Coingate\ProcessController@ipn')->name('Coingate');
    Route::post('coinbase-commerce', 'CoinbaseCommerce\ProcessController@ipn')->name('CoinbaseCommerce');
    Route::get('mollie', 'Mollie\ProcessController@ipn')->name('Mollie');
    Route::post('cashmaal', 'Cashmaal\ProcessController@ipn')->name('Cashmaal');
    Route::post('authorize-net', 'AuthorizeNet\ProcessController@ipn')->name('AuthorizeNet');
    Route::post('2check-out', 'TwoCheckOut\ProcessController@ipn')->name('TwoCheckOut');
    Route::post('mercado-pago', 'MercadoPago\ProcessController@ipn')->name('MercadoPago');
});

// User Support Ticket
Route::prefix('ticket')->group(function () {
    Route::get('/', 'TicketController@supportTicket')->name('ticket');
    Route::get('/new', 'TicketController@openSupportTicket')->name('ticket.open');
    Route::post('/create', 'TicketController@storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
    Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
});

/*
|--------------------------------------------------------------------------
| Start Admin Area
|--------------------------------------------------------------------------
*/

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');
        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetCodeEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify.code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });

    Route::middleware('admin')->group(function () { 
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');

        Route::post('add/new/hosting/demo','HostingPlanController@demo')->name('demo');
  
        //Service Cancel Request   
        Route::get('cancel/request/pending', 'CancelRequestController@pending')->name('cancel.request.pending');
        Route::get('cancel/request/completed', 'CancelRequestController@completed')->name('cancel.request.completed');
        Route::post('cancel/request', 'CancelRequestController@cancel')->name('cancel.request');
        Route::post('cancel/request/delete', 'CancelRequestController@delete')->name('cancel.request.delete');

        //Order   
        Route::get('all/order', 'OrderController@all')->name('order.all');
        Route::get('pending/order', 'OrderController@pending')->name('order.pending');
        Route::get('active/order', 'OrderController@active')->name('order.active');
        Route::get('cancelled/order', 'OrderController@cancelled')->name('order.cancelled');
        Route::get('order/details/{id}', 'OrderController@details')->name('order.details');
        Route::post('accept/order', 'OrderController@accept')->name('order.accept');
        Route::post('cancel/order', 'OrderController@cancel')->name('order.cancel');
        Route::post('mark-as-pending/order', 'OrderController@markPending')->name('order.mark.pending');
        Route::post('order/notes', 'OrderController@orderNotes')->name('order.notes');

        Route::get('hosting/details/{id}', 'ServiceController@hostingDetails')->name('order.hosting.details');
        Route::post('hosting/update/', 'ServiceController@hostingUpdate')->name('order.hosting.update');

        Route::get('domain/details/{id}', 'ServiceController@domainDetails')->name('order.domain.details');
        Route::post('domain/update', 'ServiceController@domainUpdate')->name('order.domain.update'); 

        Route::get('change/order/hosting/product/{hostingId}/{productId}', 'ServiceController@changeHostingProduct')->name('change.order.hosting.product'); 
 
        //Module Command for CPANEL
        Route::post('module/command', 'CpanelModuleController@moduleCommand')->name('module.command');
        Route::post('login/cPanel', 'CpanelModuleController@loginCpanel')->name('module.cpanel.login');

        //Module Command for DOMAIN
        Route::post('domain/module/command', 'DomainModuleController@moduleCommand')->name('domain.module.command');

        Route::get('domain/contact/details/{id}', 'ServiceController@domainContact')->name('order.domain.contact');

        //Invoice     
        Route::get('all/invoice', 'InvoiceController@all')->name('invoice.all');
        Route::get('cancelled/invoice', 'InvoiceController@cancelled')->name('invoice.cancelled');
        Route::get('paid/invoice', 'InvoiceController@paid')->name('invoice.paid');
        Route::get('unpaid/invoice', 'InvoiceController@unpaid')->name('invoice.unpaid');
        Route::get('payment-pending/invoice', 'InvoiceController@paymentPending')->name('invoice.payment.pending');
        Route::get('refunded/invoice', 'InvoiceController@refunded')->name('invoice.refunded');
        Route::get('invoice/details/{id}', 'InvoiceController@details')->name('invoice.details');
        Route::post('invoice/update', 'InvoiceController@updateInvoice')->name('invoice.update');
        Route::get('download/{id}/{view?}', 'InvoiceController@download')->name('invoice.download');
        Route::post('delete/invoice/item', 'InvoiceController@deleteInvoiceItem')->name('invoice.item.delete');
        Route::post('refund/invoice', 'InvoiceController@refundInvoice')->name('invoice.refund');
  
        Route::get('domain/{id}/invoices', 'InvoiceController@domainInvoices')->name('invoice.domain.all');
        Route::get('hosting/{id}/invoices', 'InvoiceController@hostingInvoices')->name('invoice.hosting.all');

        //Coupon    
        Route::get('all/coupon', 'CouponController@all')->name('coupon.all');
        Route::post('add/coupon', 'CouponController@add')->name('coupon.add');
        Route::post('update/coupon', 'CouponController@update')->name('coupon.update');

        //Domain   
        Route::get('all/domain', 'DomainController@all')->name('domain.all');
        Route::post('add/domain', 'DomainController@add')->name('domain.add');
        Route::post('update/domain', 'DomainController@update')->name('domain.update');
        Route::post('update/domain/pricing', 'DomainController@updatePricing')->name('domain.update.pricing');

        //Domain Register
        Route::get('domain/registers', 'DomainRegisterController@all')->name('register.domain.all');
        Route::post('domain/register/change/status', 'DomainRegisterController@changeStatus')->name('register.domain.change.status');
        Route::post('domain/register/update', 'DomainRegisterController@update')->name('register.domain.update');

        //Hosting Plan
        Route::get('all/hosting/plan','HostingPlanController@all')->name('hosting.plan.all');
        Route::get('add/new/hosting/plan','HostingPlanController@newPage')->name('hosting.plan.new');
        Route::post('add/new/hosting/plan','HostingPlanController@add')->name('hosting.plan.add');
        Route::get('edit/hosting/plan/{id}','HostingPlanController@editPage')->name('hosting.plan.edit');
        Route::post('update/hosting/plan','HostingPlanController@update')->name('hosting.plan.update');

        //Check Slug  
        Route::post('check/slug','AdminController@checkSlug')->name('check.slug');

        //Service Category  
        Route::get('all/category','ServiceCategoryController@all')->name('service.category.all');
        Route::post('add/category','ServiceCategoryController@add')->name('service.category.add');
        Route::post('update/category','ServiceCategoryController@update')->name('service.category.update');
    
        //Configuration   
        Route::get('all/configurable/group','ConfigurableController@group')->name('configurable.group.all');
        Route::post('add/configurable/group','ConfigurableController@addGroup')->name('configurable.group.add');
        Route::post('update/configurable/group','ConfigurableController@updateGroup')->name('configurable.group.update');

        Route::get('configurable/group/{id}/options','ConfigurableController@allOption')->name('configurable.group.all.option');
        Route::post('add/configurable/group/option','ConfigurableController@addOption')->name('configurable.group.add.option');
        Route::post('update/configurable/group/option','ConfigurableController@updateOption')->name('configurable.group.update.option');
    
        Route::get('configurable/group/{groupId}/{optionId}/sub/options','ConfigurableController@allSubOption')->name('configurable.group.all.sub.option');
        Route::post('configurable/group/add/sub/option','ConfigurableController@addSubOption')->name('configurable.group.add.sub.option');
        Route::post('configurable/group/update/sub/option','ConfigurableController@updateSubOption')->name('configurable.group.update.sub.option');

        //Server 
        Route::get('all/server','ServerController@allServer')->name('server.all');
        Route::get('add/server','ServerController@addServerPage')->name('server.add.page');
        Route::post('add/server','ServerController@addServer')->name('server.add');
        Route::get('edit/server/{id}','ServerController@editServerPage')->name('server.edit.page');
        Route::post('update/server','ServerController@updateServer')->name('server.update');
        Route::get('login/WHM/{id}','ServerController@loginWHM')->name('server.login.WHM');

        //Server Group
        Route::get('all/group/server','ServerController@allGroupServer')->name('group.server.all');
        Route::post('add/group/server','ServerController@addGroupServer')->name('group.server.add'); 
        Route::post('update/group/server','ServerController@updateGroupServer')->name('group.server.update'); 
 
        //Product
        Route::get('all/product','ProductController@allProduct')->name('product.all');
        Route::get('add/product','ProductController@addProductPage')->name('product.add.page');
        Route::post('add/product','ProductController@addProduct')->name('product.add');
        Route::get('edit/product/{id}','ProductController@editProductPage')->name('product.update.page');
        Route::post('update/product','ProductController@updateProduct')->name('product.update');

        Route::post('get/whm/package','AdminController@getWhmPackage')->name('get.whm.package');

        //Notification
        Route::get('notifications','AdminController@notifications')->name('notifications');
        Route::get('notification/read/{id}','AdminController@notificationRead')->name('notification.read');
        Route::get('notifications/read-all','AdminController@readAll')->name('notifications.readAll');

        //Report Bugs
        Route::get('request-report','AdminController@requestReport')->name('request.report');
        Route::post('request-report','AdminController@reportSubmit');

        Route::get('system-info','AdminController@systemInfo')->name('system.info');


        // Users Manager
        Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('users/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.email.verified');
        Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.email.unverified');
        Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.sms.unverified');
        Route::get('users/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.sms.verified');
        Route::get('users/with-balance', 'ManageUsersController@usersWithBalance')->name('users.with.balance');

        Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('user/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::post('user/update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::post('user/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.add.sub.balance');
        Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
        Route::get('user/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::get('user/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
        Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
        Route::get('user/deposits/via/{method}/{type?}/{userId}', 'ManageUsersController@depositViaMethod')->name('users.deposits.method');
        Route::get('user/withdrawals/{id}', 'ManageUsersController@withdrawals')->name('users.withdrawals');
        Route::get('user/withdrawals/via/{method}/{type?}/{userId}', 'ManageUsersController@withdrawalsViaMethod')->name('users.withdrawals.method');
        // Login History
        Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');

        Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send');
        Route::get('users/email-log/{id}', 'ManageUsersController@emailLog')->name('users.email.log');
        Route::get('users/email-details/{id}', 'ManageUsersController@emailDetails')->name('users.email.details');

        // Subscriber
        Route::get('subscriber', 'SubscriberController@index')->name('subscriber.index');
        Route::get('subscriber/send-email', 'SubscriberController@sendEmailForm')->name('subscriber.sendEmail');
        Route::post('subscriber/remove', 'SubscriberController@remove')->name('subscriber.remove');
        Route::post('subscriber/send-email', 'SubscriberController@sendEmail')->name('subscriber.sendEmail');


        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function(){
            // Automatic Gateway
            Route::get('automatic', 'GatewayController@index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'GatewayController@edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'GatewayController@update')->name('automatic.update');
            Route::post('automatic/remove/{code}', 'GatewayController@remove')->name('automatic.remove');
            Route::post('automatic/activate', 'GatewayController@activate')->name('automatic.activate');
            Route::post('automatic/deactivate', 'GatewayController@deactivate')->name('automatic.deactivate');


            // Manual Methods
            Route::get('manual', 'ManualGatewayController@index')->name('manual.index');
            Route::get('manual/new', 'ManualGatewayController@create')->name('manual.create');
            Route::post('manual/new', 'ManualGatewayController@store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'ManualGatewayController@edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'ManualGatewayController@update')->name('manual.update');
            Route::post('manual/activate', 'ManualGatewayController@activate')->name('manual.activate');
            Route::post('manual/deactivate', 'ManualGatewayController@deactivate')->name('manual.deactivate');
        });


        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('deposit')->group(function(){
            Route::get('/', 'DepositController@deposit')->name('list');
            Route::get('pending', 'DepositController@pending')->name('pending');
            Route::get('rejected', 'DepositController@rejected')->name('rejected');
            Route::get('approved', 'DepositController@approved')->name('approved');
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');

            Route::post('reject', 'DepositController@reject')->name('reject');
            Route::post('approve', 'DepositController@approve')->name('approve');
            Route::get('via/{method}/{type?}', 'DepositController@depositViaMethod')->name('method');
            Route::get('/{scope}/search', 'DepositController@search')->name('search');
            Route::get('date-search/{scope}', 'DepositController@dateSearch')->name('dateSearch');

        });


        // WITHDRAW SYSTEM
        Route::name('withdraw.')->prefix('withdraw')->group(function(){
            Route::get('pending', 'WithdrawalController@pending')->name('pending');
            Route::get('approved', 'WithdrawalController@approved')->name('approved');
            Route::get('rejected', 'WithdrawalController@rejected')->name('rejected');
            Route::get('log', 'WithdrawalController@log')->name('log');
            Route::get('via/{method_id}/{type?}', 'WithdrawalController@logViaMethod')->name('method');
            Route::get('{scope}/search', 'WithdrawalController@search')->name('search');
            Route::get('date-search/{scope}', 'WithdrawalController@dateSearch')->name('dateSearch');
            Route::get('details/{id}', 'WithdrawalController@details')->name('details');
            Route::post('approve', 'WithdrawalController@approve')->name('approve');
            Route::post('reject', 'WithdrawalController@reject')->name('reject');


            // Withdraw Method
            Route::get('method/', 'WithdrawMethodController@methods')->name('method.index');
            Route::get('method/create', 'WithdrawMethodController@create')->name('method.create');
            Route::post('method/create', 'WithdrawMethodController@store')->name('method.store');
            Route::get('method/edit/{id}', 'WithdrawMethodController@edit')->name('method.edit');
            Route::post('method/edit/{id}', 'WithdrawMethodController@update')->name('method.update');
            Route::post('method/activate', 'WithdrawMethodController@activate')->name('method.activate');
            Route::post('method/deactivate', 'WithdrawMethodController@deactivate')->name('method.deactivate');
        });

        // Report
        Route::get('report/transaction', 'ReportController@transaction')->name('report.transaction');
        Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');
        Route::get('report/login/history', 'ReportController@loginHistory')->name('report.login.history');
        Route::get('report/login/ipHistory/{ip}', 'ReportController@loginIpHistory')->name('report.login.ipHistory');
        Route::get('report/email/history', 'ReportController@emailHistory')->name('report.email.history');


        // Admin Support
        Route::get('tickets', 'SupportTicketController@tickets')->name('ticket');
        Route::get('tickets/pending', 'SupportTicketController@pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'SupportTicketController@closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'SupportTicketController@answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'SupportTicketController@ticketReply')->name('ticket.view');
        Route::post('ticket/reply/{id}', 'SupportTicketController@ticketReplySend')->name('ticket.reply');
        Route::get('ticket/download/{ticket}', 'SupportTicketController@ticketDownload')->name('ticket.download');
        Route::post('ticket/delete', 'SupportTicketController@ticketDelete')->name('ticket.delete');


        // Language Manager
        Route::get('/language', 'LanguageController@langManage')->name('language.manage');
        Route::post('/language', 'LanguageController@langStore')->name('language.manage.store');
        Route::post('/language/delete/{id}', 'LanguageController@langDel')->name('language.manage.del');
        Route::post('/language/update/{id}', 'LanguageController@langUpdate')->name('language.manage.update');
        Route::get('/language/edit/{id}', 'LanguageController@langEdit')->name('language.key');
        Route::post('/language/import', 'LanguageController@langImport')->name('language.importLang');



        Route::post('language/store/key/{id}', 'LanguageController@storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'LanguageController@deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'LanguageController@updateLanguageJson')->name('language.update.key');



        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');
        Route::get('optimize', 'GeneralSettingController@optimize')->name('setting.optimize');

        // Billing Setting
        Route::get('billing-setting', 'BillingSettingController@index')->name('billing.setting.index');
        Route::post('billing-setting', 'BillingSettingController@update')->name('billing.setting.update');
        Route::post('update/billing/invoice', 'BillingSettingController@updateBillingInvoice')->name('billing.setting.update.invoice');

        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo.icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo.icon');

        //Custom CSS
        Route::get('custom-css','GeneralSettingController@customCss')->name('setting.custom.css');
        Route::post('custom-css','GeneralSettingController@customCssSubmit');


        //Cookie
        Route::get('cookie','GeneralSettingController@cookie')->name('setting.cookie');
        Route::post('cookie','GeneralSettingController@cookieSubmit');


        // Plugin
        Route::get('extensions', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');



        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.test.mail');


        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsTemplate')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsTemplateUpdate')->name('sms.template.global');
        Route::get('sms-template/setting','SmsTemplateController@smsSetting')->name('sms.templates.setting');
        Route::post('sms-template/setting', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.setting');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.test.sms');

        // SEO
        Route::get('seo', 'FrontendController@seoEdit')->name('seo');


        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {


            Route::get('templates', 'FrontendController@templates')->name('templates');
            Route::post('templates', 'FrontendController@templatesActive')->name('templates.active');
            

            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');

            // Page Builder
            Route::get('manage-pages', 'PageBuilderController@managePages')->name('manage.pages');
            Route::post('manage-pages', 'PageBuilderController@managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'PageBuilderController@managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete', 'PageBuilderController@managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'PageBuilderController@manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'PageBuilderController@manageSectionUpdate')->name('manage.section.update');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Start User Area
|--------------------------------------------------------------------------
*/


Route::name('user.')->group(function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register')->middleware('regStatus');
    Route::post('check-mail', 'Auth\RegisterController@checkUser')->name('checkUser');

    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetCodeEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code.verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify.code');
}); 

Route::name('user.')->prefix('user')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify.email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify.sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::middleware(['checkStatus'])->group(function () {
            Route::get('dashboard', 'UserController@home')->name('home');

            Route::get('profile-setting', 'UserController@profile')->name('profile.setting');
            Route::post('profile-setting', 'UserController@submitProfile');
            Route::get('change-password', 'UserController@changePassword')->name('change.password');
            Route::post('change-password', 'UserController@submitPassword'); 

            //2FA
            Route::get('twofactor', 'UserController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'UserController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'UserController@disable2fa')->name('twofactor.disable');


            // Deposit
            Route::any('/deposit', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('deposit/insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('deposit/preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('deposit/confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');
            Route::get('deposit/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');
            Route::any('deposit/history', 'UserController@depositHistory')->name('deposit.history');
 
            // Withdraw
            Route::get('/withdraw', 'UserController@withdrawMoney')->name('withdraw');
            Route::post('/withdraw', 'UserController@withdrawStore')->name('withdraw.money');
            Route::get('/withdraw/preview', 'UserController@withdrawPreview')->name('withdraw.preview');
            Route::post('/withdraw/preview', 'UserController@withdrawSubmit')->name('withdraw.submit');
            Route::get('/withdraw/history', 'UserController@withdrawLog')->name('withdraw.history');
  
            Route::get('shopping/cart', 'UserController@cart')->name('shopping.cart'); 
            Route::get('add/shopping/cart', 'UserController@addCart')->name('shopping.cart.add'); 
            Route::get('delete/shopping/cart/{id?}/{billing_type?}', 'UserController@deleteCart')->name('shopping.cart.delete');
            Route::get('delete/shopping/cart/domain/{id}/{domain}', 'UserController@deleteDomainCart')->name('shopping.cart.delete.domain');
            Route::get('config/domain/{id}/{domain}/{regPeriod}', 'UserController@configDomain')->name('config.domain');
            Route::post('config/domain', 'UserController@configDomainUpdate')->name('config.domain.update');
            Route::post('coupon', 'UserController@coupon')->name('coupon');
   
            Route::post('create/invoice', 'UserController@createInvoice')->name('create.invoice');
            Route::get('view/invoice/{id}', 'UserController@viewInvoice')->name('view.invoice');

            Route::post('payment', 'UserController@payment')->name('payment');  
 
            Route::get('my/services', 'UserController@myServices')->name('my.services');
            Route::get('service/details/{id}', 'UserController@serviceDetails')->name('service.details');
            Route::post('service/cancel/request', 'UserController@serviceCancelRequest')->name('service.cancel.request');
            
            Route::get('login/cPanel/{id}', 'UserController@loginCpanel')->name('login.cpanel');

            Route::get('my/domains', 'UserController@myDomains')->name('my.domains');
            Route::get('domain/details/{id}', 'UserController@domainDetails')->name('domain.details');
            Route::post('domain/nameserver/update', 'UserController@domainNameserverUpdate')->name('domain.nameserver.update');
            Route::get('domain/contact/{id}', 'UserController@domainContact')->name('domain.contact');
            Route::post('domain/contact/update', 'UserController@domainContactUpdate')->name('domain.contact.update');
            Route::post('domain/renew', 'UserController@domainRenew')->name('domain.renew');
 
            Route::get('my/invoices', 'UserController@myInvoices')->name('my.invoices');
            Route::get('invoices/download/{id}/{view?}', 'UserController@invoiceDownload')->name('invoice.download');
 
            Route::get('/delete', function(){
                \DB::statement('TRUNCATE TABLE deposits');
                \DB::statement('TRUNCATE TABLE orders');
                \DB::statement('TRUNCATE TABLE invoices');
                \DB::statement('TRUNCATE TABLE hostings');
                \DB::statement('TRUNCATE TABLE hosting_configs');
                \DB::statement('TRUNCATE TABLE invoice_items');
                \DB::statement('TRUNCATE TABLE transactions');
                \DB::statement('TRUNCATE TABLE domains');

                return 200;
            });

        });
    }); 
});

Route::get('/product/configure/{id}', 'SiteController@productConfigure')->name('product.configure');

Route::get('/contact', 'SiteController@contact')->name('contact');
Route::post('/contact', 'SiteController@contactSubmit');
Route::get('/change/{lang?}', 'SiteController@changeLanguage')->name('lang');

Route::get('/cookie/accept', 'SiteController@cookieAccept')->name('cookie.accept');

Route::get('blog/{id}/{slug}', 'SiteController@blogDetails')->name('blog.details');

Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholder.image');
 
Route::get('/{slug}', 'SiteController@pages')->name('pages');
Route::get('/', 'SiteController@index')->name('home');