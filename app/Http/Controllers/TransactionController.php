<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;

class TransactionController extends Controller
{


    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function deposit_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Can not Deposit');
        }
        try {
            DB::beginTransaction();
            $request->request->add(['user_id' => Auth::id()]);
            $this->transactionService->deposit_store($request);
            DB::commit();
            return redirect()->route('deposit_list')->with('success', 'Deposit successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function deposit_list(Request $request)
    {
        $data = array();
        $data['deposit_list'] = $this->transactionService->transaction_list(Auth::id(), 'deposit');
        return view('pages.deposit', $data);
    }

    public function withdrawal_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Can not Withdrawal');
        }
        try {
            DB::beginTransaction();
            $request->request->add(['user_id' => Auth::id()]);
            $newBalance = $this->transactionService->check_withdrawal_possible(Auth::id(), $request->amount);
            if ($newBalance > 0) {
                $this->transactionService->withdrawal_store($request);
            }else{
                return redirect()->route('withdrawal_list')->with('error', 'You do not have enough balance');
            }

            DB::commit();
            return redirect()->route('withdrawal_list')->with('success', 'Withdrawal successful');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', $e->getMessage());
        }
    }

    public function withdrawal_list(Request $request)
    {
        $data = array();
        $data['withdrawal_list'] = $this->transactionService->transaction_list(Auth::id(), 'withdrawal');
        return view('pages.withdrawal', $data);
    }



}
