<?php
// app/Models/EmailVerification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = ['user_id', 'otp', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new OTP for user
     */
    public static function generateForUser($userId)
    {
        // Delete old OTPs
        self::where('user_id', $userId)->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return self::create([
            'user_id' => $userId,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);
    }

    /**
     * Verify OTP
     */
    public static function verify($userId, $otp)
    {
        $verification = self::where('user_id', $userId)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($verification) {
            $verification->delete();
            return true;
        }

        return false;
    }
}
