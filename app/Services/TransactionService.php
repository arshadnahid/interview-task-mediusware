<?php

namespace App\Services;


use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
class TransactionService
{

    public function transaction_list($user_id, $type = '')
    {
        $transaction_list = Transaction::query();
        if ($type == 'deposit') {
            $transaction_list = $transaction_list->where('transaction_type', 'deposit');
        } else if ($type == 'withdrawal') {
            $transaction_list = $transaction_list->where('transaction_type', 'withdrawal');
        }
        return $transaction_list->where('user_id', $user_id)->get();

    }
    public function deposit_store($request)
    {
        $date = Carbon::now()->format('Y-m-d');
        $transection = new Transaction();
        $transection->user_id = $request->user_id;
        $transection->transaction_type = 'deposit';
        $transection->amount = $request->amount;
        $transection->fee = 0;
        $transection->date = $date;
        $transection->save();
        $this->update_balance($request->user_id, $request->amount, 'deposit');
        return $transection;
    }
    public function check_withdrawal_possible($user_id, $withdrawal_amount)
    {
        $withdrawal_fee = $this->calculate_withdrawal_fee($user_id, $withdrawal_amount);
        $user_info = User::findOrFail($user_id);
        $newBalance = $user_info->balance - ($withdrawal_amount + $withdrawal_fee);
        return $newBalance;
    }
    public function calculate_withdrawal_fee($user_id, $amount)
    {
        $user = User::findOrFail($user_id);
        $accountType = $user->account_type;

        // Apply appropriate withdrawal rate based on account type
        $withdrawalRate = ($accountType === 'Individual') ? 0.015 : 0.025;

        // Check if it's a Friday (5) for free withdrawal
        $isFriday = Carbon::today()->dayOfWeek === Carbon::FRIDAY;

        // Check if the amount is within the free withdrawal limit
        $isFreeWithdrawal = ($accountType === 'Individual') && ($isFriday || $amount <= 1000);

        // Apply the first 5K free withdrawal per month for Individual accounts
        if ($accountType === 'Individual' && $user->withdrawalsThisMonth() < 5000) {
            $remainingFreeWithdrawal = 5000 - $user->withdrawalsThisMonth();
            if ($amount <= $remainingFreeWithdrawal) {
                $isFreeWithdrawal = true;
            }
        }

        // Apply the decrease in withdrawal fee for Business accounts
        if ($accountType === 'Business' && $user->totalWithdrawalAmount() >= 50000) {
            $withdrawalRate = 0.015;
        }
        // Calculate withdrawal fee
        return $isFreeWithdrawal ? 0 : ($amount * $withdrawalRate);

    }
    public function withdrawal_store($request)
    {
        $date = Carbon::now()->format('Y-m-d');
        $user_id = $request->user_id;
        $withdrawal_amount = $request->amount;
        $withdrawal_fee = $this->calculate_withdrawal_fee($user_id, $withdrawal_amount);
        $newBalance = $this->check_withdrawal_possible($user_id, $withdrawal_amount);
        if ($newBalance > 0) {
            $transection = new Transaction();
            $transection->user_id = $user_id;
            $transection->transaction_type = 'withdrawal';
            $transection->amount = $withdrawal_amount;
            $transection->fee = $withdrawal_fee;
            $transection->date = $date;
            $transection->save();
            $this->update_balance($request->user_id, $request->amount, 'withdrawal');
            return $transection;
        }
    }
    public function update_balance($user_id, $balance, $type)
    {
        $user_info = User::findOrFail($user_id);
        if ($type == 'deposit') {
            $user_info->balance = $user_info->balance + $balance;
        } else {
            $user_info->balance = $user_info->balance - $balance;
        }
        $user_info->save();
    }
    public function current_balance($user_id)
    {
        $user = User::findOrFail($user_id);
        return $user->balance;
    }
}
