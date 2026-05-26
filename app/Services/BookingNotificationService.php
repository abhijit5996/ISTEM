<?php

namespace App\Services;

use App\Mail\BookingApprovedMail;
use App\Mail\BookingRejectedMail;
use App\Models\Booking;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingNotificationService
{
    public function sendApproval(Booking $booking): array
    {
        return $this->send($booking, 'approved', new BookingApprovedMail($booking));
    }

    public function sendRejection(Booking $booking): array
    {
        return $this->send($booking, 'rejected', new BookingRejectedMail($booking));
    }

    private function send(Booking $booking, string $status, Mailable $mailable): array
    {
        $booking->loadMissing('instrument');

        $recipient = $this->resolveRecipient($booking);
        $mailer = $this->resolveMailer();
        $mailerConfig = $this->currentMailerConfig($mailer);

        $context = [
            'booking_id' => $booking->id,
            'recipient_email' => $recipient,
            'mailer' => $mailer,
            'booking_status' => $status,
            'instrument_id' => $booking->instrument_id,
            'instrument_name' => $booking->instrument?->name,
            'booking_period' => $booking->start_date . ' -> ' . $booking->end_date,
        ];

        if (! $recipient) {
            Log::warning('Booking email skipped because no recipient email was found', $context);

            return [
                'sent' => false,
                'mailer' => $mailer,
                'recipient' => null,
                'error' => 'missing_recipient',
            ];
        }

        if (! $this->mailerIsUsable($mailer, $mailerConfig)) {
            Log::warning('Booking email skipped because mailer configuration is incomplete', $context + [
                'mailer_config' => $mailerConfig,
            ]);

            return [
                'sent' => false,
                'mailer' => $mailer,
                'recipient' => $recipient,
                'error' => 'mailer_not_configured',
            ];
        }

        try {
            Mail::mailer($mailer)->to($recipient)->send($mailable);

            Log::info('Booking email sent successfully', $context + [
                'smtp_response' => 'sent via ' . $mailer,
            ]);

            return [
                'sent' => true,
                'mailer' => $mailer,
                'recipient' => $recipient,
                'error' => null,
            ];
        } catch (\Throwable $exception) {
            Log::error('Booking email failed to send', $context + [
                'error' => $exception->getMessage(),
                'exception' => class_basename($exception),
                'mailer_config' => $mailerConfig,
            ]);

            return [
                'sent' => false,
                'mailer' => $mailer,
                'recipient' => $recipient,
                'error' => $exception->getMessage(),
            ];
        }
    }

    private function resolveRecipient(Booking $booking): ?string
    {
        $recipient = $booking->email ?: $booking->user_email;

        if (! $recipient || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $recipient;
    }

    private function resolveMailer(): string
    {
        if ($this->hasSmtpConfiguration()) {
            return 'smtp';
        }

        if (filled(config('services.resend.key'))) {
            return 'resend';
        }

        return (string) config('mail.default', 'log');
    }

    private function hasSmtpConfiguration(): bool
    {
        $smtp = (array) config('mail.mailers.smtp', []);

        return filled($smtp['host'] ?? null)
            && filled($smtp['port'] ?? null)
            && (filled($smtp['username'] ?? null) || filled($smtp['password'] ?? null) || filled($smtp['url'] ?? null));
    }

    private function currentMailerConfig(string $mailer): array
    {
        $config = (array) config('mail.mailers.' . $mailer, []);

        return [
            'transport' => $config['transport'] ?? $mailer,
            'host' => $config['host'] ?? null,
            'port' => $config['port'] ?? null,
            'username' => $config['username'] ?? null,
            'scheme' => $config['scheme'] ?? null,
            'timeout' => $config['timeout'] ?? null,
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
    }

    private function mailerIsUsable(string $mailer, array $mailerConfig): bool
    {
        if ($mailer === 'log') {
            return true;
        }

        if ($mailer === 'resend') {
            return filled(config('services.resend.key'));
        }

        return filled($mailerConfig['host'] ?? null) && filled($mailerConfig['port'] ?? null);
    }
}