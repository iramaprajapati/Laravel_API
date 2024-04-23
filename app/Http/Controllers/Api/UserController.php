<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        // To get all users (Active And Inactive) flag value is 0.
        // To get only active users flag value is 1.

        // dataPrint("Get API is working..");
        $query = User::select('name', 'email', 'contact');
        if ($flag == 1) {
            $query->where('status', 1);
        } elseif ($flag == 0) {
            // $query->where('status', 0); // To get only inactive users
        } else {
            $resp = [
                'message' => "Invalid parameter passed, It can be either 1 or 0 at the last.",
                'status' => 0,
            ];
            return response($resp, 400);
        }
        $user = $query->get();
        if (count($user) > 0) {
            // Users exists in database
            $response = [
                'message' => count($user) . " Users found.",
                'status' => 1,
                'userData' => $user,
            ];
        } else {
            // Users not exists in database
            $response = [
                'message' => "No Users found.",
                'status' => 0,
            ];
        }
        return response($response, 200);
        // return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dataPrint($request->all());
        $validator = Validator::make($request->all(), [

            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'contact' => ['required', 'numeric'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'min:8'],

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        } else {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'contact' => $request->contact,
                'password' => Hash::make($request->password),
            ];
            // dataPrint($data);
            DB::beginTransaction();
            try {
                $user = User::create($data);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                dataPrint($e->getMessage());
                $user = null;
            }
            if ($user != null) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User registered successfully..!',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Internal Server Error..',
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // By default id is Primary Key in MySql database
        $user = User::find($id); // find() methods find the primary key column in the database.
        if (is_null($user)) {
            $response = [
                "message" => "User not found.",
                "status" => 0,
            ];
        } else {
            $response = [
                "message" => "User found.",
                "status" => 1,
                "userData" => $user,
            ];
        }
        return response($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            // user does not exists.
            return response()->json([
                "message" => "User does not exists.",
                "status" => 0,
            ], 404);
        } else {
            DB::beginTransaction();
            try {
                // $user->name = $request->name;            
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->address = $request['address'];
                $user->pincode = $request['pincode'];
                $user->status = $request['status'];
                $user->save();
                DB::commit();
            } catch (\Exception $err) {
                DB::rollBack();
                $user = null;
            }
            if (is_null($user)) {
                return response([
                    "message" => 'Internal server error.',
                    "status" => 0,
                    "error_msg" => $err->getMessage(),
                ], 500);
            } else {
                return response([
                    "message" => "User updated successfully.",
                    "status" => 1,
                    "userData" => $user,
                ], 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                "message" => "User not found.",
                "status" => 0,
            ];
            $respCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                $response = [
                    "message" => "User deleted successfully..",
                    "status" => 1,
                ];
                $respCode = 200;
            } catch (\Exception $err) {
                DB::rollBack();
                $response = [
                    "message" => $err->getMessage(),
                    "status" => 0,
                ];
                $respCode = 500;
            }
        }
        return response($response, $respCode);
    }

    /**
     * Change password for user in database.
     */
    public function changePassword(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                "message" => "User not found.",
                "status" => 0,
            ];
            $respCode = 404;
        } else {

            if ($user->password == $request['old_password']) {
                if ($request['new_password'] == $request['confirm_password']) {
                    DB::beginTransaction();
                    try {
                        $user->password = $request['new_password'];
                        $user->save();
                        DB::commit();
                        $response = [
                            "message" => "Password changed successfully..",
                            "status" => 1,
                        ];
                        $respCode = 200;
                    } catch (\Exception $err) {
                        DB::rollBack();
                        $user = null;
                        $response = [
                            "message" => $err->getMessage(),
                            "status" => 0,
                        ];
                        $respCode = 500;
                    }
                } else {
                    $response = [
                        "message" => "New password and confirm password does not match.",
                        "status" => 0,
                    ];
                    $respCode = 400;
                }
            } else {
                $response = [
                    "message" => "Old password does not match.",
                    "status" => 0,
                ];
                $respCode = 400;
            }
        }
        return response($response, $respCode);
    }

    // public function userRegister(Request $request)
    // {

    //     $validatedData = $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'contact' => 'required',
    //         'password' => 'required|min:8|confirmed',
    //         'password_confirmation' => 'required|min:8',
    //     ]);
    //     try {
    //         $user = User::create($validatedData);
    //         $accessToken = $user->createToken('authToken')->accessToken;
    //         return response(['message' => 'User registered successfully..', 'status' => 1, 'user' => $user, 'access_token' => $accessToken]);
    //     } catch (\Exception $err) {
    //         return response()->json(['message' => $err->getMessage(), 'status' => 0]);
    //     }
    // }

    public function userRegister(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'contact' => 'required',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            // Return a 422 response with validation errors
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 0
            ], 422);
        }

        try {
            // Create the user in the database
            $validatedData = $validator->validated(); // Get validated data
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'contact' => $validatedData['contact'],
                'password' => bcrypt($validatedData['password']), // Encrypt the password
            ]);

            // Generate an access token using Passport
            $accessToken = $user->createToken('authToken')->accessToken;

            // Return a success response with the user and the access token
            return response()->json([
                'message' => 'User registered successfully',
                'status' => 1,
                'user' => $user,
                'access_token' => $accessToken
            ]);
        } catch (\Exception $err) {
            // Return an error response with the exception message
            return response()->json([
                'message' => 'An error occurred: ' . $err->getMessage(),
                'status' => 0
            ], 500);
        }
    }
}
