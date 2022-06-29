<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\User;
//use Illuminate\Foundation\Auth\RedirectsUsers;
//use Illuminate\Foundation\Auth\VerifiesEmails;
//use Illuminate\Http\Request;
//
//class VerificationController extends Controller
//{
//    /*
//    |--------------------------------------------------------------------------
//    | Email Verification Controller
//    |--------------------------------------------------------------------------
//    |
//    | This controller is responsible for handling email verification for any
//    | user that recently registered with the application. Emails may also
//    | be re-sent if the user didn't receive the original email message.
//    |
//    */
//
//    use VerifiesEmails, RedirectsUsers;
//
//    /**
//     * Where to redirect users after verification.
//     *
//     * @var string
//     */
//    protected $redirectTo = '/';
//
//    /**
//     * Create a new controller instance.
//     *
//     * @return void
//     */
//    public function __construct()
//    {
//        $this->middleware('signed')->only('verify');
//        $this->middleware('throttle:6,1')->only('verify', 'resend');
//    }
//
//    /**
//     * Show the email verification notice.
//     *
//     * @param  \Illuminate\Http\Request  $request
//     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
//     */
//    public function show(Request $request)
//    {
//        return $request->user()->hasVerifiedEmail()
//            ? redirect($this->redirectPath())
//            : view('verification.notice', [
//                'pageTitle' => __('Account Verification')
//            ]);
//    }
//
//    public function verify($user_id, Request $request) {
//        if (!$request->hasValidSignature()) {
//            return redirect()->to(config("url_constants.front_end_url").'/auth/verification-expired');
//            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
//        }
//
//        $user = User::findOrFail($user_id);
//        if (!$user->hasVerifiedEmail()) {
//            $user->markEmailAsVerified();
//        }
//
//        return redirect()->to(config("url_constants.front_end_url"));
//    }
//}
