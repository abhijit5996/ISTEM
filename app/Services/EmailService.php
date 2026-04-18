<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public static function sendOTP(string $to, string $otp): bool
    {
        try {
            if (filter_var(env('OTP_DISABLE_SEND', false), FILTER_VALIDATE_BOOL)) {
                Log::info('OTP email sending disabled by configuration', [
                    'to' => $to,
                    'otp' => $otp,
                ]);

                return true;
            }

            $provider = strtolower((string) env('OTP_MAIL_PROVIDER', 'smtp'));
            $hasResend = (bool) env('RESEND_API_KEY');

            if ($provider === 'smtp') {
                self::sendViaSmtp($to, $otp);
                return true;
            }

            if ($provider === 'resend') {
                self::sendViaResend($to, $otp);
                return true;
            }

            // auto mode: try Resend first if key exists, then fallback to SMTP.
            if ($provider === 'auto' && $hasResend) {
                try {
                    self::sendViaResend($to, $otp);
                    return true;
                } catch (\Throwable $resendError) {
                    Log::warning('OTP resend provider failed; falling back to SMTP', [
                        'error' => $resendError->getMessage(),
                    ]);
                }
            }

            self::sendViaSmtp($to, $otp);
            return true;
        } catch (\Throwable $e) {
            Log::error('OTP email failed', ['error' => $e->getMessage()]);
            throw new \Exception('otp_send_failed');
        }
    }

    private static function sendViaResend(string $to, string $otp): void
    {
        $resend = \Resend::client(env('RESEND_API_KEY'));

        $response = $resend->emails->send([
            'from' => env('RESEND_FROM_ADDRESS', 'onboarding@resend.dev'),
            'to' => [$to],
            'subject' => 'Your OTP Code',
            'html' => self::otpHtml($otp),
        ]);

        Log::info('OTP email sent via Resend', ['response' => $response]);
    }

    private static function sendViaSmtp(string $to, string $otp): void
    {
        Mail::html(self::otpHtml($otp), function ($message) use ($to) {
            $message->to($to)
                ->subject('Your OTP Code');
        });

        Log::info('OTP email sent via SMTP/mailer', ['to' => $to]);
    }

    private static function otpHtml(string $otp): string
    {
        return "
            <h2>Your OTP Code</h2>
            <p>Your OTP is:</p>
            <h1>{$otp}</h1>
            <p>This OTP will expire in 5 minutes.</p>
        ";
    }
}
