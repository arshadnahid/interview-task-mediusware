<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'account_type',
        'balance',
        'email',
        'password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function withdrawalsThisMonth()
    {
        $startOfMonth = now()->startOfMonth();
        return $this->transactions()
            ->where('type', 'withdrawal')
            ->where('created_at', '>=', $startOfMonth)
            ->sum('amount');
    }

    public function totalWithdrawalAmount()
    {
        return $this->transactions()
            ->where('type', 'withdrawal')
            ->sum('amount');
    }
}
