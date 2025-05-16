<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/member/register",
     *     summary="Register new member",
     *     tags={"Member"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={
     *                  "personal_id",
     *                  "firstname",
     *                  "lastname",
     *                  "username",
     *                  "email",
     *                  "phone_number",
     *                  "password",
     *                  "password_confirmation",
     *                  "language",
     *                  "accept_consent"
     *             },
     *             @OA\Property(property="personal_id", type="string", example="1198899232111"),
     *             @OA\Property(property="member_type", type="string", example="", description="value: internal,external"),
     *             @OA\Property(property="firstname", type="string", example="Member"),
     *             @OA\Property(property="lastname", type="string", example="K"),
     *             @OA\Property(property="username", type="string", example="member_K"),
     *             @OA\Property(property="email", type="string", format="email", example="member_K@hotmail.com"),
     *             @OA\Property(property="phone_number", type="string", example="0980000011"),
     *             @OA\Property(property="password", type="string", format="password", example="P@ssw0rd"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="P@ssw0rd"),
     *             @OA\Property(property="language", type="string", format="language", example="THA"),
     *             @OA\Property(property="accept_consent", type="string", format="accept_consent", example="T"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Register successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="Member registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="username", type="string", example="member_K"),
     *                 @OA\Property(property="email", type="string", example="member_K@hotmail.com"),
     *                 @OA\Property(property="create_date", type="string", format="date-time", example="2025-05-15 14:23:45")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Register failed",
     *     )
     * )
     */
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'personal_id' => 'required|string|max:13',
            'member_type' => 'required|string|max:15',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|min:10|max:15',
            'password' => 'required|string|min:6|confirmed',
            'language' => 'required|string|min:3',
            'accept_consent' => 'required|string|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 500,
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], 500);
        }

        if ($request->accept_consent !== 'T') {
            return response()->json([
                'code' => 500,
                'msg' => 'Please consent to the collection of your personal data.',
                'err' => ['accept_consent' => ['Consent is required.']]
            ], 500);
        }

        // Get all member from Model
        $members = Member::allMembers();

        // Check duplicate
        foreach ($members as $member) {
            if ($member['personal_id'] === $request->personal_id) {
                return response()->json(['personal_id' => ['Personal ID already registered']], 500);
            }
            if ($member['email'] === $request->email) {
                return response()->json(['email' => ['Email already registered']], 500);
            }
            if ($member['phone_number'] === $request->phone_number) {
                return response()->json(['phone_number' => ['Phone number already registered']], 500);
            }
            if ($member['username'] === $request->username) {
                return response()->json(['username' => ['Username already registered']], 500);
            }
        }

        // Insert
        $newMember = [
            'id' => Str::uuid()->toString(),
            'personal_id' => $request->personal_id,
            'member_type' => $request->member_type,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname, 
            'username' => $request->username, 
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => bcrypt($request->password),
            'create_date' => now()->toDateTimeString(),
            'update_date' => now()->toDateTimeString(),
            'language' => $request->language,
            'accept_consent' => $request->accept_consent,
        ];
        $members->push($newMember);
        Member::saveMembers($members);

        $responseMember = [
            'username' => $newMember['username'],
            'email' => $newMember['email'],
            'create_date' => $newMember['create_date'],
        ];

        return response()->json([
            'code' => 201,
            'msg' => 'Member registered successfully',
            'data' => $responseMember,
        ], 201);
    }
}
