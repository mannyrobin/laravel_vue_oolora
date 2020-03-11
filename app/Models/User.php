<?php

namespace App\Models;

// Subscription
use DanTheCoder\SaaSCore\Subscription\Traits\PlanSubscriber;
use DanTheCoder\SaaSCore\Subscription\Contracts\PlanSubscriberInterface;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DanTheCoder\SaaSCore\Subscription\Models\PlanSubscription;
use DanTheCoder\SaaSCore\Account\Notifications\VerifyEmailNotification;
use DanTheCoder\SaaSCore\Account\Notifications\PasswordResetNotification;


class User extends Authenticatable implements MustVerifyEmail, PlanSubscriberInterface
{
    use PlanSubscriber, HasApiTokens, Notifiable, SoftDeletes, HasRoles;


    // Mass assignable attributes
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'timezone', 'stripe_id', 'stripe_card', 'paypal_id'
    ];


    // Hidden attributes for arrays
    protected $hidden = [
        'password', 'remember_token',
    ];


    // Soft delete
    protected $dates = [
        'deleted_at'
    ];


    // Get the user subscription info
    public function subscribable()
    {
        return $this->hasOne(
            PlanSubscription::class,
            'subscribable_id'
        )->latest();
    }


    // Format dates
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }


    /**
     * Override to send custom the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
    

    /**
     * Override to send custom the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }
}
