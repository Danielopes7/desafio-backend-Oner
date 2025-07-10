<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Http\Requests\WithdrawRequest;
use App\Actions\DepositAction;
use App\Actions\WithdrawAction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        private DepositAction $depositAction,
        private WithdrawAction $withdrawAction
    ) {}

    public function deposit(DepositRequest $request)
    {
        try{
            $transaction = $this->depositAction->handle($request);
    
            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Deposit completed successfully',
                'data' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 400,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function withdraw(WithdrawRequest $request)
    {
        try{
            $transaction = $this->withdrawAction->handle($request);

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Withdraw completed successfully',
                'data' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' => 400,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
