<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Transactions;

class TransactionsController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/transactions/insert",
     *     summary="Insert new transaction (mockup api)",
     *     tags={"Transactions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                 "transaction_type", "token_type", "status",
     *                 "payment_type", "system_type", "pay_currency",
     *                 "amount", "per_price", "total_price",
     *                 "sender_id", "receiver_id", "sell_order_id"
     *             },
     *             @OA\Property(property="transaction_type", type="string", example="buy", description="value: buy,sell,transfer,receive"),
     *             @OA\Property(property="token_type", type="string", example="XRP", description="value: BTC,ETH,XRP,DOGE"),
     *             @OA\Property(property="status", type="string", example="pending", description="value: pending,processing,completed,failed"),
     *             @OA\Property(property="payment_type", type="string", example="credit", description="value: qrcode,credit"),
     *             @OA\Property(property="system_type", type="string", example="internal", description="value: internal,external"),
     *             @OA\Property(property="pay_currency", type="string", example="USD"),
     *             @OA\Property(property="amount", type="integer", example=2),
     *             @OA\Property(property="per_price", type="decimal", example=500.00),
     *             @OA\Property(property="total_price", type="number", example=1000.00),
     *             @OA\Property(property="sender_id", type="string", example="abc527bd-79d0-4799-a883-9c7257990d3a"),
     *             @OA\Property(property="receiver_id", type="string", example="3535a148-2431-48ea-a1bf-314e52a8fb56"),
     *             @OA\Property(property="sell_order_id", type="string", example="5ca2d56d-79ad-41e3-889f-0cdc12c77014")
     *         )
     *     ),
    *     @OA\Response(
     *         response=201,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(property="msg", type="string", example="Success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="9390cea0-6fa6-41f3-a003-b5c3fb69db3a"),
     *                 @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-16 11:35:59")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Insert transaction failed"
     *     )
     * )
     */
    public function transactionsInsert(Request $request)
    {
        // JWT check [TODO]

        // Validate input
        $validator = Validator::make($request->all(), [
            'transaction_type' => 'required|string',
            'token_type' => 'required|string',
            'status' => 'required|string',
            'payment_type' => 'required|string',
            'system_type' => 'required|string',
            'pay_currency' => 'required|string',
            'amount' => 'required|numeric|min:0.0001',
            'per_price' => 'required|numeric|min:0.01',
            'total_price' => 'required|numeric|min:0.01',
            'sender_id' => 'required|string',
            'receiver_id' => 'required|string',
            'sell_order_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'msg' => 'Validation failed',
                'errors' => $validator->errors()
            ], 500);
        }

        $transactions = Transactions::all();

        $newTransaction = [
            'id' => Str::uuid()->toString(),
            'transaction_type' => $request->transaction_type,
            'token_type' => $request->token_type,
            'status' => $request->status,
            'payment_type' => $request->payment_type,
            'system_type' => $request->system_type,
            'pay_currency' => $request->pay_currency,
            'amount' => $request->amount,
            'per_price' => $request->per_price,
            'total_price' => $request->total_price,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'sell_order_id' => $request->sell_order_id,
            'create_date' => now()->toDateTimeString(),
            'update_date' => now()->toDateTimeString(),
        ];

        $transactions->push($newTransaction);
        Transactions::save($transactions);

        return response()->json([
            'code' => 201,
            'msg' => 'Transaction created successfully',
            'data' => [
                'id' => $newTransaction['id'],
                'create_date' => $newTransaction['create_date']
            ]
        ], 201);
    }

     /**
     * @OA\Post(
     *     path="/api/transactions/list",
     *     summary="Transactions List",
     *     tags={"Transactions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="member_id", type="string", example="abc527bd-79d0-4799-a883-9c7257990d3a"),
     *             @OA\Property(property="keyword", type="string", example="", description="Search by transaction_id, receiver_name"),
     *             @OA\Property(property="sort", type="string", example="", description="Sort by last update_date => value: asc,desc"),
     *             @OA\Property(property="page", type="string", example="1"),
     *             @OA\Property(property="limit", type="string", example="10"),
     *             @OA\Property(property="transaction_type", type="string", example="", description="value: buy,sell,transfer,receive"),
     *             @OA\Property(property="token_type", type="string", example="", description="value: BTC,ETH,XRP,DOGE"),
     *             @OA\Property(property="status", type="string", example="", description="value: pending,processing,completed,failed"),
     *             @OA\Property(property="payment_type", type="string", example="", description="value: qrcode,credit"),
     *             @OA\Property(property="system_type", type="string", example="", description="value: internal,external"),
     *             @OA\Property(property="pay_currency", type="string", example="THB"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="transaction_id", type="string", example="abc527bd-79d0-4799-a883-9c7257990d3ิb"),
     *                 @OA\Property(property="transaction_type", type="string", example="buy"),
     *                 @OA\Property(property="token_type", type="string", example="BTC"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="payment_type", type="string", example="qrcode"),
     *                 @OA\Property(property="system_type", type="string", example="internal"),
     *                 @OA\Property(property="pay_currency", type="string", example="THB"),
     *                 @OA\Property(property="amount", type="integer", example="2"),
     *                 @OA\Property(property="per_price", type="decimal", example="3457601.59"),
     *                 @OA\Property(property="total_price", type="decimal", example="6915203.18"),
     *                 @OA\Property(property="sender_id", type="string", example="abc527bd-79d0-4799-a883-9c7257990d3a"),
     *                 @OA\Property(property="receiver_id", type="string", example="abc527bd-79d0-4799-a883-9c7257990d3b"),
     *                 @OA\Property(property="sell_order_id", type="string", example="9390cea0-6fa6-41f3-a003-b5c3fb69db3a"),
     *                 @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-15 19:32:20"),
     *                 @OA\Property(property="update_date", type="string", format="date-time", example="2025-05-15 19:32:20"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Not found data."
     *     )
     * )
     */
    public function transactionsList(Request $request)
    {
        // JWT check [TODO]

        $transactions = Transactions::all();

        // Relation test 
        $memberId = $request->input('member_id', '');

        $keyword = strtolower($request->input('keyword', ''));
        $transactionType = $request->input('transaction_type', '');
        $tokenType = $request->input('token_type', '');
        $status = $request->input('status', '');
        $paymentType = $request->input('payment_type', '');
        $systemType = $request->input('system_type', '');
        $payCurrency = $request->input('pay_currency', '');

        $sortField = 'update_date';
        $sortOrder = strtolower($request->input('sort', 'desc'));
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $filtered = $transactions->filter(function ($tx) use ($keyword, $transactionType, $tokenType, $status, $paymentType, $systemType, $payCurrency) {
            // where member.id
            $matchMember = true;
            if (!empty($memberId)) {
                $matchMember = ($tx['sender_id'] === $memberId || $tx['receiver_id'] === $memberId);
            }
            
            $matchKeyword = true;
            if ($keyword !== '') {
                $matchKeyword = (
                    str_contains(strtolower($tx['id']), $keyword) ||
                    str_contains(strtolower($tx['receiver_name'] ?? ''), $keyword)
                );
            }

            $matchTransactionType = $transactionType ? $tx['transaction_type'] === $transactionType : true;
            $matchTokenType = $tokenType ? $tx['token_type'] === $tokenType : true;
            $matchStatus = $status ? $tx['status'] === $status : true;
            $matchPaymentType = $paymentType ? $tx['payment_type'] === $paymentType : true;
            $matchSystemType = $systemType ? $tx['system_type'] === $systemType : true;
            $matchPayCurrency = $payCurrency ? $tx['pay_currency'] === $payCurrency : true;

            return $matchKeyword && $matchTransactionType && $matchTokenType && $matchStatus && $matchPaymentType && $matchSystemType && $matchPayCurrency;
        });

        $sorted = $filtered->sortBy(function ($tx) use ($sortField) {
            return strtotime($tx[$sortField]);
        }, SORT_REGULAR, $sortOrder === 'desc');

        $total = $sorted->count();
        $page = max(1, (int)$request->input('page', 1));
        $limit = max(1, (int)$request->input('limit', 10));
        $pagedData = $sorted->slice(($page - 1) * $limit, $limit)->values();

        return response()->json([
            'code' => 200,
            'msg' => 'Success',
            'total' => $total,
            'data' => $pagedData,
        ], 200);
    }
}
