<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'amount',
        'status',
        'proof_image',
        'admin_note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
