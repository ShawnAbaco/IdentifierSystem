<?php
// app/Helpers/MailHelper.php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailHelper
{
    /**
     * Send email using PHPMailer
     *
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @return array ['success' => bool, 'message' => string]
     */
    public static function sendEmail($to, $name, $subject, $body)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output (set to DEBUG_SERVER for testing)
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_USERNAME');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', PHPMailer::ENCRYPTION_SMTPS);
            $mail->Port       = env('MAIL_PORT', 465);

            // Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@identifier.com'), env('MAIL_FROM_NAME', 'Plant Identifier System'));
            $mail->addAddress($to, $name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return ['success' => true, 'message' => 'Email sent successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"];
        }
    }

    /**
     * Generate OTP email body
     *
     * @param string $name User name
     * @param string $otp OTP code
     * @return string HTML email body
     */
    public static function getOtpEmailBody($name, $otp)
    {
        $appName = env('APP_NAME', 'Plant Identifier System');
        $year = date('Y');

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Email Verification OTP</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                }
                .header {
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                    border-radius: 10px 10px 0 0;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                }
                .content {
                    background: #f8f9fa;
                    padding: 30px;
                    border-radius: 0 0 10px 10px;
                }
                .otp-box {
                    background: white;
                    border: 2px dashed #28a745;
                    border-radius: 10px;
                    padding: 20px;
                    text-align: center;
                    margin: 20px 0;
                }
                .otp-code {
                    font-size: 36px;
                    font-weight: bold;
                    color: #28a745;
                    letter-spacing: 5px;
                }
                .footer {
                    text-align: center;
                    margin-top: 20px;
                    color: #6c757d;
                    font-size: 12px;
                }
                .button {
                    display: inline-block;
                    padding: 12px 30px;
                    background: #28a745;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .warning {
                    color: #dc3545;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1><i class='fas fa-leaf'></i> {$appName}</h1>
                </div>
                <div class='content'>
                    <h2>Hello, {$name}!</h2>
                    <p>Thank you for using {$appName}. To verify your email address, please use the following OTP code:</p>

                    <div class='otp-box'>
                        <div class='otp-code'>{$otp}</div>
                    </div>

                    <p>This OTP is valid for <strong>10 minutes</strong>. Please do not share this code with anyone.</p>

                    <p>If you didn't request this verification, please ignore this email or contact support.</p>

                    <p class='warning'><strong>Note:</strong> For security reasons, this OTP will expire in 10 minutes.</p>
                </div>
                <div class='footer'>
                    <p>&copy; {$year} {$appName}. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Generate welcome email after verification
     */
    public static function getWelcomeEmailBody($name)
    {
        $appName = env('APP_NAME', 'Plant Identifier System');
        $year = date('Y');

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Welcome Email</title>
            <style>
                body { font-family: 'Segoe UI', sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 20px; color: #6c757d; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to {$appName}! 🌿</h1>
                </div>
                <div class='content'>
                    <h2>Hi {$name},</h2>
                    <p>Your email has been successfully verified! You can now enjoy all features of our Plant & Flower Identifier system.</p>
                    <p>Start identifying plants and flowers today!</p>
                    <p style='text-align: center; margin: 30px 0;'>
                        <a href='" . route('identify') . "' class='button' style='background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px;'>Start Identifying</a>
                    </p>
                </div>
                <div class='footer'>
                    <p>&copy; {$year} {$appName}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
