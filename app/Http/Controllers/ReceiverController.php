<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Receiver;

class ReceiverController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/receiver/create",
     *     summary="Create new receiver",
     *     tags={"Receiver"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                  "account_no",
     *                  "firstname",
     *                  "lastname",
     *                  "phone_number"
     *             },
     *             @OA\Property(property="account_no", type="string", example="0234245345"),
     *             @OA\Property(property="firstname", type="string", example="Receiver"),
     *             @OA\Property(property="lastname", type="string", example="A"),
     *             @OA\Property(property="phone_number", type="string", example="0812221111"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Create new receiver successfully"),
     *             @OA\Property(property="receiver", type="object",
     *                 @OA\Property(property="account_no", type="string", example="0234245345"),
     *                 @OA\Property(property="fullname", type="string", example="Receiver A"),
     *                 @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-15 14:23:45")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Create failed"
     *     )
     * )
     */
    public function createReceiver(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'account_no' => 'required|string|max:10',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone_number' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], 500);
        }

        // Get all receiver from Model
        $receivers = Receiver::allReceivers();

        // Check duplicate
        foreach ($receivers as $receiver) {
            if ($receiver['account_no'] === $request->account_no) {
                return response()->json(['account_no' => ['Account No. already registered']], 500);
            }
            if ($receiver['phone_number'] === $request->phone_number) {
                return response()->json(['phone_number' => ['Phone number already registered']], 500);
            }
        }

        // Insert
        $newReceiver = [
            'id' => Str::uuid()->toString(),
            'account_no' => $request->account_no,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone_number' => $request->phone_number,
            'create_date' => now()->toDateTimeString(),
            'update_date' => now()->toDateTimeString(),
        ];
        $receivers->push($newReceiver);
        Receiver::saveReceivers($receivers);

        $responseReceiver = [
            'account_no' => $newReceiver['account_no'],
            'fullname' => trim($newReceiver['firstname'] . ' ' . $newReceiver['lastname']),
            'create_date' => $newReceiver['create_date'],
        ];

        return response()->json([
            'code' => 201,
            'msg' => 'Create new receiver successfully',
            'data' => $responseReceiver,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/receiver/list",
     *     summary="Receiver List",
     *     tags={"Receiver"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="keyword", type="string", example="", description="Search by account_no, phone_number, firstname, lastname, fullname"),
     *             @OA\Property(property="sort", type="string", example="asc", description="Sort by firstname => value: asc,desc"),
     *             @OA\Property(property="page", type="integer", example="1"),
     *             @OA\Property(property="limit", type="integer", example="10"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(property="msg", type="string", example="Success"),
     *             @OA\Property(property="data", type="array",
     *             @OA\Property(property="total", type="integer", example=12),
     *                  @OA\Items(
     *                      @OA\Property(property="account_no", type="string", example="0234245345"),
     *                      @OA\Property(property="firstname", type="string", example="Receiver"),
     *                      @OA\Property(property="lastname", type="string", example="A"),
     *                      @OA\Property(property="phone_number", type="string", example="0812221111"),
     *                      @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-15 14:23:45"),
     *                      @OA\Property(property="update_date", type="string", format="date-time", example="2025-05-15 14:23:45")
     *                  )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Not found data"
     *     )
     * )
     */
    public function receiverList(Request $request)
    {
        // JWT check [TODO]

        $receivers = Receiver::allReceivers();

        $keyword = strtolower($request->input('keyword', ''));
        $sort = strtolower($request->input('sort', 'desc'));
        $page = max(1, (int)$request->input('page', 1));
        $limit = max(1, (int)$request->input('limit', 10));

        // Filter
        $filtered = collect($receivers)->filter(function ($receiver) use ($keyword) {
            if ($keyword === '') return true;

            $keyword = strtolower($keyword);
            $fullname = strtolower(trim(($receiver['firstname'] ?? '') . ' ' . ($receiver['lastname'] ?? '')));

            return str_contains(strtolower($receiver['firstname'] ?? ''), $keyword)
                || str_contains(strtolower($receiver['lastname'] ?? ''), $keyword)
                || str_contains(strtolower($receiver['account_no'] ?? ''), $keyword)
                || str_contains(strtolower($receiver['phone_number'] ?? ''), $keyword)
                || str_contains($fullname, $keyword);
        });

        // Sort
        $sortBy = 'firstname';
        if (!in_array($sort, ['asc', 'desc'])) {
            $sort = 'desc';
        }

        $sorted = $filtered->sortBy(function ($receiver) use ($sortBy) {
            return $receiver[$sortBy];
        }, SORT_REGULAR, $sort === 'desc');

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
