<?php
// app/Http/Controllers/Auth/ForgotPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerification;
use App\Helpers\MailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /**
     * Show forgot password form
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We could not find a user with that email address.'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        try {
            // Delete old OTPs
            EmailVerification::where('user_id', $user->id)->delete();

            // Generate new OTP
            $verification = EmailVerification::generateForUser($user->id);

            // Send OTP email
            $subject = 'Password Reset OTP - ' . env('APP_NAME');
            $body = MailHelper::getOtpEmailBody($user->name, $verification->otp);

            $result = MailHelper::sendEmail(
                $user->email,
                $user->name,
                $subject,
                $body
            );

            if ($result['success']) {
                // Store email in session for OTP verification
                session(['reset_email' => $user->email]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'OTP sent successfully to your email.'
                    ]);
                }

                return redirect()->route('password.verify.form')
                    ->with('success', 'OTP sent successfully to your email.');
            } else {
                throw new \Exception($result['message']);
            }
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again.'
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to send OTP. Please try again.']);
        }
    }

    /**
     * Show OTP verification form
     */
    public function showOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Please request a password reset first.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|array|size:6',
            'otp.*' => 'required|string|size:1|regex:/^[0-9]$/'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $otp = implode('', $request->otp);
        $user = User::where('email', $request->email)->first();

        // Verify OTP
        if (EmailVerification::verify($user->id, $otp)) {
            // Store verified status in session
            session(['reset_verified' => true, 'reset_user_id' => $user->id]);

            return redirect()->route('password.reset.form');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.'])->withInput();
    }

    /**
     * Show reset password form
     */
    public function showResetForm()
    {
        if (!session('reset_verified') || !session('reset_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Please verify your OTP first.']);
        }

        return view('auth.reset-password');
    }

    /**
     * Reset password
     */
    public function reset(Request $request)
    {
        if (!session('reset_verified') || !session('reset_user_id')) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'Invalid password reset session.']);
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[A-Z])(?=.*[0-9])/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter and one number.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::find(session('reset_user_id'));

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['error' => 'User not found.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session
        session()->forget(['reset_email', 'reset_verified', 'reset_user_id']);

        // Delete used OTPs
        EmailVerification::where('user_id', $user->id)->delete();

        // Send confirmation email
        $this->sendPasswordChangedEmail($user);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully! You can now login with your new password.');
    }

    /**
     * Send password changed confirmation email
     */
    private function sendPasswordChangedEmail($user)
    {
        $subject = 'Password Changed - ' . env('APP_NAME');
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #28a745; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Password Changed Successfully</h2>
                </div>
                <div class='content'>
                    <p>Hello {$user->name},</p>
                    <p>Your password has been changed successfully.</p>
                    <p>If you did not make this change, please contact support immediately.</p>
                    <p>You can login with your new password now.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        MailHelper::sendEmail($user->email, $user->name, $subject, $body);
    }
}
