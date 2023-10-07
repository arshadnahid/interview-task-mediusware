<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private TransactionService $transactionService;
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $data = array();
        $data['current_balance']=$this->transactionService->current_balance(Auth::id())?$this->transactionService->current_balance(Auth::id()):0;
        $data['all_transaction'] = $this->transactionService->transaction_list(Auth::id(), '');
        return view('home', $data);

    }
}
