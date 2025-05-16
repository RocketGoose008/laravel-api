<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\SellOrders;

class SellOrdersController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/sell_orders/create",
     *     summary="Create Sell Orders",
     *     tags={"Sell Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                  "member_id",
     *                  "token_type",
     *                  "amount",
     *                  "per_price"
     *             },
     *             @OA\Property(property="member_id", type="string", example="abc527bd-79d0-4799-a883-9c7257990d3b"),
     *             @OA\Property(property="token_type", type="string", example="BTC,ETH,XRP,DOGE"),
     *             @OA\Property(property="amount", type="integer", example=10),
     *             @OA\Property(property="per_price", type="number", format="float", example=1999.99),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="9390cea0-6fa6-41f3-a003-b5c3fb69db3a"),
     *                 @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-16 11:35:59")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Create sale order failed"
     *     )
     * )
     */
    public function sellOrdersCreate(Request $request)
    {
        // JWT check [TODO]

        // Validate input
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|string',
            'token_type' => 'required|string',
            'amount' => 'required|numeric|min:0.0001',
            'per_price' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'msg' => 'Validation failed',
                'errors' => $validator->errors()
            ], 500);
        }

        $sellOrders = SellOrders::all();

        $newSellOrder = [
            'id' => Str::uuid()->toString(),
            'seller_id' => $request->member_id,
            'token_type' => $request->token_type,
            'amount' => $request->amount,
            'per_price' => $request->per_price,
            'create_date' => now()->toDateTimeString(),
            'update_date' => now()->toDateTimeString(),
        ];

        $sellOrders->push($newSellOrder);
        SellOrders::save($sellOrders);

        $responseSellOrders = [
            'id' => $newSellOrder['id'],
            'create_date' => $newSellOrder['create_date'],
        ];

        return response()->json([
            'code' => 201,
            'msg' => 'Create sell orders Success',
            'data' => $responseSellOrders,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/sell_orders/list",
     *     summary="Sell Orders List",
     *     tags={"Sell Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="keyword", type="string", example="Search by id"),
     *             @OA\Property(property="sort_by", type="string", example="per_price,update_date"),
     *             @OA\Property(property="sort", type="string", example="asc,desc"),
     *             @OA\Property(property="page", type="integer", example="1"),
     *             @OA\Property(property="limit", type="integer", example="10"),
     *             @OA\Property(property="token_type", type="string", example="BTC,ETH,XRP,DOGE"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="msg", type="string", example="Success"),
     *             @OA\Property(property="total", type="integer", example=2),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                      @OA\Property(property="sell_orders_id", type="string", example="5ca2d56d-79ad-41e3-889f-0cdc12c77014"),
     *                      @OA\Property(property="seller_id", type="string", example="9390cea0-6fa6-41f3-a003-b5c3fb69db3a"),
     *                      @OA\Property(property="token_type", type="string", example="XRP"),
     *                      @OA\Property(property="amount", type="integer", example=1),
     *                      @OA\Property(property="per_price", type="number", format="float", example=1999.99),
     *                      @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-16 10:23:53"),
     *                      @OA\Property(property="update_date", type="string", format="date-time", example="2025-05-16 10:23:53"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Not found data"
     *     )
     * )
     */
    public function sellOrdersList(Request $request)
    {
        // JWT check [TODO]

        $sellOrders = SellOrders::all();

        $keyword = strtolower($request->input('keyword', ''));
        $tokenType = $request->input('token_type', '');
        $sortBy = $request->input('sort_by', 'per_price');
        $sortOrder = strtolower($request->input('sort', 'desc'));
        $page = max(1, (int)$request->input('page', 1));
        $limit = max(1, (int)$request->input('limit', 10));

        // Filter
        $filtered = $sellOrders->filter(function ($order) use ($keyword, $tokenType) {
            $matchKeyword = true;
            if ($keyword !== '') {
                $matchKeyword = str_contains(strtolower($order['id']), $keyword);
            }
            $matchTokenType = $tokenType ? ($order['token_type'] === $tokenType) : true;
            return $matchKeyword && $matchTokenType;
        });

        // Sort
        $allowedSortFields = ['per_price', 'update_date'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'per_price';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $sorted = $filtered->sortBy(function ($order) use ($sortBy) {
            if ($sortBy === 'per_price') {
                return (float)$order[$sortBy];
            }
            if ($sortBy === 'update_date') {
                return strtotime($order[$sortBy]);
            }
            return $order[$sortBy];
        }, SORT_REGULAR, $sortOrder === 'desc');

        // Pagination
        $total = $sorted->count();
        $pagedData = $sorted->slice(($page - 1) * $limit, $limit)->values();

        return response()->json([
            'code' => 200,
            'msg' => 'Success',
            'total' => $total,
            'data' => $pagedData,
        ], 200);
    }
}
