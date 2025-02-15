<?php
/**
 * Copyright (C) MUNPANEL
 * This file is part of MUNPANEL System.
 *
 * Open-sourced under AGPL v3 License.
 */

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['domain' => config('munpanel.landingDomain')], function () {
    Route::get('/', function () {
        return view('landing');
    })->name('landing');
    Route::get('/maintenance', function() {
        $data = json_decode(file_get_contents($this->app->storagePath().'/framework/down'), true);
        return $data['message'];
    })->name('maintenance');
});

Route::group(['domain' => config('munpanel.payDomain')], function() {
    Route::get('/result', 'PayController@payResult')->name('payResult');
});

//Route::group(['domain' => 'portal.munpanel.com'], function () {
Route::group(['domain' => config('munpanel.portalDomain')], function () {
    Route::get('/', function () {
        return redirect(secure_url('home'));
    });

    Route::get('/home', 'PortalController@index')->name('portal');
    Route::get('/schools/new.modal', 'PortalController@newTeamModal');
    Route::get('/schools/join.modal/{id?}', 'SchoolController@joinTeamModal')->middleware('auth');
    Route::get('/school/{id}', 'SchoolController@schoolIndex');
    Route::get('/school/{id}/details.modal', 'PortalController@detailsModal');
    Route::post('/school/{id}/doUpdate', 'PortalController@updateTeam')->middleware('auth');
    Route::get('/school/{id}/admin', 'PortalController@teamAdmin')->middleware('auth');
    Route::get('/school/{id}/admin/members', 'PortalController@teamMembers')->middleware('auth');
    Route::get('/school/{id}/admin/members.ajax', 'PortalController@groupMemberTable')->middleware('auth');
    Route::get('/school/{id}/admin/conferences', 'PortalController@groupConferences');
    Route::get('/school/{id}/admin/conferences.ajax', 'PortalController@groupConferencesTable');
    Route::get('/schools/{gid}/groupMember/{uid}/addAdmin.modal', 'PortalController@groupMemberAddAdminModal');
    Route::get('/schools/{gid}/groupMember/{uid}/delAdmin.modal', 'PortalController@groupMemberDelAdminModal');
    Route::get('/school/{id}/admin/conferences/add.modal', 'PortalController@groupAddConferenceModal');
    Route::post('/school/{id}/admin/doAddConference', 'PortalController@groupAddConf');
    Route::post('/schools/doCreateSchool', 'SchoolController@createTeam')->middleware('auth');
    Route::post('/schools/doAddAdmin', 'PortalController@addAdmin')->middleware('auth');
    Route::post('/schools/doDelAdmin', 'PortalController@delAdmin')->middleware('auth');
    Route::post('/schools/doJoinSchool', 'SchoolController@joinTeam')->name('doJoinTeam')->middleware('auth');
    Route::get('/ajax/schoollist', 'SchoolController@teamsTable');

    // Authentication Routes...
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Auth\LoginController@login');//->middleware('recaptcha');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/loginViaConsoleMail', 'Auth\\LoginController@loginConsoleMail');
    Route::post('/loginMail', 'Auth\\LoginController@doLoginMail');//->middleware('recaptcha');


    // Registration Routes...
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');//->middleware('recaptcha');

    // Password Reset Routes...
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::get('/verifyEmail', 'HomeController@verifyEmail')->name('verifyEmail');
    Route::get('/verifyTel', 'HomeController@verifyTel')->name('verifyTel');
    Route::get('/verifyEmail/{email}/{token}', 'UserController@doVerifyEmail');
    Route::get('/verifyTel.modal/{method}/{tel?}', 'UserController@verifyTelModal');
    Route::post('/verifyTel', 'UserController@doVerifyTel');
    Route::get('/verifyEmail/resend', 'UserController@resendRegMail');

    Route::post('/doSwitchIdentity', 'UserController@doSwitchIdentity');
    Route::get('/doSwitchIdentity/{reg}', 'UserController@doSwitchIdentity');
    Route::get('/selectIdentityModal', 'HomeController@selectIdentityModal');

    Route::get('/ajax/store', 'DatatablesController@goods');
    Route::get('/ajax/orders/{id}', 'DatatablesController@orders');
    Route::get('/store', 'StoreController@home');
    Route::get('/store/cart', 'StoreController@displayCart');
    Route::post('/store/cart/add/{id}', 'StoreController@addCart');
    Route::get('/store/cart/remove/{id}', 'StoreController@removeCart');
    Route::get('/store/cart/empty', 'StoreController@emptyCart');
    Route::get('/store/orders/{id?}', 'StoreController@ordersList');
    Route::get('/store/order/{id}', 'StoreController@displayOrder');
    Route::get('/store/orderAdmin.modal/{id}', 'StoreController@orderAdmin');
    Route::post('/store/manualPay/{id}', 'StoreController@manualPay');
    Route::get('/store/deleteOrder/{id}/{confirm?}', 'StoreController@deleteOrder');
    Route::get('/store/checkout', 'StoreController@checkout');
    Route::get('/store/shipment.modal', 'StoreController@shipmentModal');
    Route::post('/store/doCheckout', 'StoreController@doCheckout');
    Route::get('/store/goodimg/{id}', 'StoreController@goodImage');
    Route::get('/store/good.modal/{id}', 'StoreController@goodModal');
    Route::get('/allOrders/{id}', 'StoreController@viewAllOrders');
    Route::get('/store/cart', 'StoreController@displayCart');
    Route::post('/store/cart/add/{id}', 'StoreController@addCart');
    Route::get('/store/cart/remove/{id}', 'StoreController@removeCart');
    Route::get('/store/cart/empty', 'StoreController@emptyCart');
    Route::get('/store/orders/{id?}', 'StoreController@ordersList');
    Route::get('/store/order/{id}', 'StoreController@displayOrder');
    Route::get('/store/orderAdmin.modal/{id}', 'StoreController@orderAdmin');
    Route::post('/store/manualPay/{id}', 'StoreController@manualPay');
    Route::get('/store/deleteOrder/{id}/{confirm?}', 'StoreController@deleteOrder');
    Route::get('/store/checkout', 'StoreController@checkout');
    Route::get('/store/shipment.modal', 'StoreController@shipmentModal');
    Route::post('/store/doCheckout', 'StoreController@doCheckout');
    Route::get('/store/goodimg/{id}', 'StoreController@goodImage');
    Route::get('/store/good.modal/{id}', 'StoreController@goodModal');
    Route::get('/pay/checkout.modal/{id}', 'HomeController@checkout');
    Route::get('/ajax/payWait/{oid}', 'PayController@resultAjax');
    Route::post('/payInfo', 'PayController@payInfo')->name('payInfo');
    Route::post('/mpPayInfo', 'PayController@mpPayInfo')->name('mpPayInfo');

    Route::get('/keepalive', 'SessionController@keepalive');
});

Route::group(['domain' => 'static.munpanel.com'], function () {
    Route::get('/', function () {
        return "MUNPANEL static caching service provided by Akamai and ChinaNetCenter (网宿科技)";
    });
    Route::get('/showEmail/{id}', 'EmailController@showEmail');
    Route::get('/emailLogo.png', 'EmailController@emailLogo');
});


//Route::group(['domain' => '{domain}'], function () {
    Route::get('/', function () {
        return redirect(secure_url('home'));
    });
    
    //Route::get('/login', 'HomeController@loginRedirect');

    Route::get('500', function()
    {
        //return trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD'));
        return 0/0;
    });

    Route::get('/keepalive', 'SessionController@keepalive');
    Route::get('/disabled', 'UserController@disabledhome');
    Route::get('/logout.reg', 'Auth\LoginController@logoutReg')->name('logoutReg');

    Route::get('/aboutDebug', 'HomeController@aboutDebug');
    Route::get('/aboutSudo', 'HomeController@aboutSUDO');

    //Route::get('/sendDaisResult', 'EmailController@sendDaisResult');
    Route::get('/resendMail/{id}', 'EmailController@resend');
    //Route::get('/daisBJMUN', 'EmailController@daisBJMUN');

    //Route::get('/startCaptchaServlet', 'GeeTestController@startCaptcha');

    Route::post('/doSwitchIdentity', 'UserController@doSwitchIdentity');
    Route::get('/doSwitchIdentity/{reg}', 'UserController@doSwitchIdentity');
    Route::get('/selectIdentityModal', 'HomeController@selectIdentityModal');

    Route::get('/reg.first.modal', 'HomeController@firstModal');
    Route::get('/regAssignment.modal', 'HomeController@regAssignmentModal');
    Route::post('/doSelectTeam', 'UserController@doSelectTeam');
    Route::get('/createTeamAdmin', 'UserController@createTeamAdmin');

    Route::get('/home', 'HomeController@index');
    Route::get('/changePwd.modal', 'HomeController@changePwd');
    Route::post('/changePwd', 'UserController@doChangePwd');

    Route::get('/schools', 'SchoolController@schoolPortal');
    Route::get('/schools/list', 'SchoolController@schools');

    // TODO: 判定 - 代表 or 学团 or 组委？
    Route::get('/assignments', 'HomeController@assignmentsList');
    Route::get('/assignment/{id}/{action?}', 'HomeController@assignment');
    Route::post('/assignment/{id}/upload', 'HomeController@uploadAssignment');
    Route::any('/assignment/{id}/formSubmit/{submit?}', 'HomeController@formAssignmentSubmit');
    Route::get('/formHandinWindow/{id}', 'FormController@showFormWindow');

    // TODO: 判定 - 代表 or 学团 or 组委？
    Route::get('/documents', 'HomeController@documentsList');
    Route::get('/document/{id}/{action?}', 'HomeController@document');
    Route::get('/documentDetails.modal/{id}', 'HomeController@documentDetailsModal');
    Route::post('/document/upload', 'HomeController@uploadDocument');

    Route::get('/pages', function() {
        return view('notavailable');
    });

    Route::get('/chair', function() {
        return view('notavailable');
    });

    Route::get('/fb', function() {
        return view('notavailable');
    });

    Route::get('/ddltimer', function() {
        return view('notavailable');
    });

    Route::get('/confConfig', function() {
        return view('notavailable');
    });

    Route::get('/reg2.modal/{regType}', 'HomeController@reg2Modal');
    Route::get('/daisreg.modal', 'HomeController@daisregModal');
    Route::get('/daisregForm', 'HomeController@daisregForm');
    Route::any('/daisregForm/formSubmit/{submit?}', 'HomeController@daisregFormSubmit');
    Route::get('/reg.modal/{id?}', 'HomeController@regModal');
    Route::get('/ot/userDetails.modal/{id}', ['middleware' => ['permission:edit-users'], 'uses' => 'HomeController@userDetailsModal']);
    Route::get('/ot/schoolDetails.modal/{id}', ['middleware' => ['permission:edit-schools'], 'uses' => 'HomeController@schoolDetailsModal']);
    Route::get('/ot/committeeDetails.modal/{id}', ['middleware' => ['permission:edit-committees'], 'uses' => 'HomeController@committeeDetailsModal']);

    Route::post('/saveReg2', 'UserController@reg2');
    Route::post('/saveRegDel', 'UserController@regSaveDel');
    Route::post('/saveRegVol', 'UserController@regSaveVol');
    Route::post('/saveRegObs', 'UserController@regSaveObs');
    
    Route::post('/doPair', 'UserController@pairAction');
    Route::post('/genPaircode', 'UserController@generatePaircode');
    Route::get('/deletePaircode/{code}', 'UserController@deletePaircode');

    Route::get('/ecosocEasterEgg.modal', 'HomeController@ecosocEasterEggModal');
    Route::get('/regNotVerified.modal', 'HomeController@regNotVerifiedModal');
    Route::get('/resetReg/{force?}', 'UserController@resetReg');
    Route::get('/setAccomodation.modal', 'HomeController@setAccomodationModal');
    Route::post('/setAccomodate', 'UserController@setAccomodation');
    Route::get('/paircode.modal', 'HomeController@pairingModal');
    Route::post('/doPair', 'UserController@pairAction');
    Route::post('/doAutoAssign', 'UserController@autoAssign');

    Route::get('/roleList', 'HomeController@roleList');
    Route::get('/roleAlloc', 'HomeController@roleAlloc');

    Route::get('/regManage', 'HomeController@regManage');
    Route::get('/teamManage', 'HomeController@teamManage');
    Route::get('/regManage/imexport.modal', 'HomeController@imexportRegistrations');
    Route::get('/regManage/export/{flag?}', 'ExcelController@exportRegistrations');
    Route::post('/regManage/import', 'ExcelController@importRegistrations');
    Route::get('/userManage', ['middleware' => ['permission:edit-users'], 'uses' => 'HomeController@userManage']);
    Route::get('/schoolManage', ['middleware' => ['permission:edit-schools'], 'uses' => 'HomeController@schoolManage']);
    Route::get('/committeeManage', ['middleware' => ['permission:edit-committees'], 'uses' => 'HomeController@committeeManage']);
    Route::get('/nationManage', ['middleware' => ['permission:edit-nations'], 'uses' => 'HomeController@nationManage']);

    Route::get('/school/verify/{id}', 'UserController@schoolVerify');
    Route::get('/school/unverify/{id}', 'UserController@schoolUnverify');
    Route::get('/ot/verify/{id}/{status}', 'UserController@setStatus');
    Route::post('/ot/update/user/{id}', ['middleware' => ['permission:edit-users'], 'uses' => 'UserController@updateUser']);
    Route::post('/ot/update/committee/{id}', ['middleware' => ['permission:edit-committees'], 'uses' => 'UserController@updateCommittee']);
    Route::post('/ot/update/interview/{id}', 'InterviewController@updateInterview');
    Route::post('/ot/update/reg/{id}', 'UserController@updateReg');

    Route::get('/ot/delete/user/{id}', ['middleware' => ['permission:edit-users'], 'uses' => 'UserController@deleteUser']);
    Route::get('/ot/delete/school/{id}', ['middleware' => ['permission:edit-schools'], 'uses' => 'UserController@deleteSchool']);
    Route::get('/ot/delete/committee/{id}', ['middleware' => ['permission:edit-committees'], 'uses' => 'UserController@deleteCommittee']);

    // TODO: 添加权限控制
    Route::get('/dais/lockAlloc/{confirm?}', 'RoleAllocController@lockAlloc');
    Route::get('/dais/removeSeat/{id}', 'RoleAllocController@removeDelegate');
    Route::any('/dais/addSeat/{id}/{action?}', 'RoleAllocController@addDelegate');
    Route::get('/dais/freeNation/{id}', 'RoleAllocController@freeNation');
    Route::get('/dais/nationDetails.modal/{id}', 'RoleAllocController@nationDetailsModal');
    Route::post('/dais/update/nation/{id}', 'RoleAllocController@updateNation');
    Route::get('/dais/delete/nation/{id}/{confirm?}', 'RoleAllocController@deleteNation');
    Route::get('/dais/linkPartner/{id1}/{id2}', 'RoleAllocController@linkPartner');
    Route::get('/dais/linkPartner.modal', 'RoleAllocController@linkPartnerModal');
    Route::get('/dais/seatSMS.modal/{id}/{confirm?}', 'RoleAllocController@sendSMS');
    Route::get('/delBizCard.modal/{id}', 'RoleAllocController@getDelegateBizcard');
    Route::get('/ot/regInfo.modal/{id}', 'HomeController@regInfoModal');
    Route::get('/ot/daisregInfo.modal/{id}', 'HomeController@daisregInfoModal');
    Route::post('/ot/setDelgroup', 'UserController@setDelgroup');
    Route::post('/ot/changeCommittee', 'UserController@setCommittee');
    Route::post('/ot/changePairing', 'UserController@setPairing');
    Route::post('/ot/updateSeat', 'RoleAllocController@updateSeat');
    Route::get('/ot/seatLock/{id}', 'RoleAllocController@lockSeat');
    Route::get('/ot/seatUnLock/{id}', 'RoleAllocController@unlockSeat');

    Route::get('/ot/oVerify/{id}', 'UserController@oVerify');
    Route::get('/ot/oNoVerify/{id}', 'UserController@oNoVerify');
    Route::get('/ot/oReVerify/{id}', 'UserController@oReVerify');
    Route::get('/school/sVerify/{id}', 'UserController@sVerify');
    Route::get('/school/sNoVerify/{id}', 'UserController@sNoVerify');
    Route::get('/school/sReVerify/{id}', 'UserController@sReVerify');

    Route::get('/interviews/{id?}', 'InterviewController@interviews');
    Route::any('/interview/{id}/{action}', 'InterviewController@interview');
    Route::post('/ot/assignInterview/{id}', 'InterviewController@assignInterview');
    Route::post('/ot/exemptInterview/{id}', 'InterviewController@exemptInterview');
    Route::get('/findInterviewer.modal', 'InterviewController@findInterviewerModal');
    Route::post('/gotoInterviewer', 'InterviewController@gotoInterviewer');
    Route::post('/newNote', 'NoteController@newNote');

    //Route::get('/dais/assignments', 'HomeController@assignment');

    Route::get('/regDais', 'UserController@regDais');
    //Route::get('/regschools', 'UserController@regSchool');
    Route::get('/test', 'UserController@test');
    Route::get('/blank', 'HomeController@blank');
    Route::get('/createPermissions', 'UserController@createPermissions');

    Route::get('/school/payment', 'HomeController@schoolPay');
    Route::get('/school/pay/change/{method}', 'HomeController@changeSchoolPaymentMethod');
    Route::get('/pay/invoice', 'HomeController@invoice');
    Route::get('/pay/checkout.modal/{id}', 'HomeController@checkout');
    Route::get('/ajax/payWait/{oid}', 'PayController@resultAjax');
    Route::post('/payInfo', 'PayController@payInfo')->name('payInfo');
    Route::post('/mpPayInfo', 'PayController@mpPayInfo')->name('mpPayInfo');

    Route::get('/store', 'StoreController@home');
    Route::get('/store/cart', 'StoreController@displayCart');
    Route::post('/store/cart/add/{id}', 'StoreController@addCart');
    Route::get('/store/cart/remove/{id}', 'StoreController@removeCart');
    Route::get('/store/cart/empty', 'StoreController@emptyCart');
    Route::get('/store/orders/{id?}', 'StoreController@ordersList');
    Route::get('/store/order/{id}', 'StoreController@displayOrder');
    Route::get('/store/orderAdmin.modal/{id}', 'StoreController@orderAdmin');
    Route::post('/store/manualPay/{id}', 'StoreController@manualPay');
    Route::get('/store/deleteOrder/{id}/{confirm?}', 'StoreController@deleteOrder');
    Route::get('/store/checkout', 'StoreController@checkout');
    Route::get('/store/shipment.modal', 'StoreController@shipmentModal');
    Route::post('/store/doCheckout', 'StoreController@doCheckout');
    Route::get('/store/goodimg/{id}', 'StoreController@goodImage');
    Route::get('/store/good.modal/{id}', 'StoreController@goodModal');
    Route::get('/allOrders/{id}', 'StoreController@viewAllOrders');
    Route::get('/shipOrder/{id}', 'StoreController@shipOrder');

    Route::get('/ajax/registrations', 'DatatablesController@reg2Table');
    Route::get('/ajax/teammembers', 'DatatablesController@teamTable');
    Route::get('/ajax/users', ['middleware' => ['permission:edit-users'], 'uses' => 'DatatablesController@users']);
    Route::get('/ajax/schools', ['middleware' => ['permission:edit-schools'], 'uses' => 'DatatablesController@schools']);
    Route::get('/ajax/committees', ['middleware' => ['permission:edit-committees'], 'uses' => 'DatatablesController@committees']);
    Route::get('/ajax/nations', 'DatatablesController@nations');
    Route::get('/ajax/assignments', 'DatatablesController@assignments');
    Route::get('/ajax/store', 'DatatablesController@goods');
    Route::get('/ajax/orders/{id}', 'DatatablesController@orders');
    Route::get('/ajax/documents', 'DatatablesController@documents');
    Route::get('/ot/generateBadge/{template}/{name}/{school}/{role}/{title}/{mode?}', 'ImageController@generateBadge');
    Route::get('/ot/generateBadgeCommittee/{cid}', 'ImageController@committeeBadge');
    Route::get('/ot/generateCardsDelegates', 'CardController@generateCardsDelegates');
    Route::get('/ot/generateCardsDais', 'CardController@generateCardsDais');
    Route::get('/ot/generateCardsVolunteers', 'CardController@generateCardsVolunteers');
    Route::get('/ot/generateCardBadges', 'CardController@generateCardbadges');
    Route::get('/ot/card/new/{template}/{uid}/{name}/{school}/{role}/{title}', 'CardController@newCard');
    Route::get('/ot/importCards', 'CardController@importCards');
    Route::get('/ot/regenerateCardBadge/{id}', 'CardController@regenerateCardBadge');
    Route::get('/ajax/roleAllocNations', 'DatatablesController@roleAllocNations');
    Route::get('/ajax/roleAllocDelegates', 'DatatablesController@roleAllocDelegates');
    Route::get('/ajax/roleListByNation', 'DatatablesController@roleListByNation');
    Route::get('/ajax/roleListByDelegate', 'DatatablesController@roleListByDelegate');
    Route::get('/ajax/atwhoList', 'UserController@atwhoList');

    Route::get('/chat', 'ChatController@getIndex');
    Route::post('/chat/message', 'ChatController@postMessage');

    Route::get('/bridge', function() {
        Pusher::trigger('my-channel', 'my-event', ['message' => 'gou']);


        return view('home');
    });

    Route::get('/testCurrentDomain', function() {
        return config('munpanel.conference_id');
    });
    /*
    Route::get('/avatar/{filename}', function ($filename) {
    // im not 100% sure about the $path thingy, you need to fiddle with this one around.
        $path = storage_path() . '/app/public/avatar/' . $filename;
        error_log($path);
        if(!File::exists($path)) abort(404);

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    });*/
//});
