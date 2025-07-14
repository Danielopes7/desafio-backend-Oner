<?php

namespace App\Http\Controllers;

use App\Http\Requests\RefundRequest;
use App\Http\Requests\TransferRequest;
use App\Services\TransferService;

class TransferController extends Controller
{
    public function __construct(protected TransferService $transferService) {}

    public function transfer(TransferRequest $request)
    {
        try {
            $transaction = $this->transferService->execTransfer($request);

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Transfer completed successfully',
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

    public function refund(RefundRequest $request)
    {
        try {
            $transaction = $this->transferService->execRefund($request);

            return response()->json([
                'response_code' => 200,
                'status' => 'success',
                'message' => 'Refund completed successfully',
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
