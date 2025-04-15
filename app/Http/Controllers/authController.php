<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\user_profile;
use App\Models\verification_token;
use App\Notifications\verifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PhpParser\Node\Stmt\TryCatch;

class authController extends Controller
{
    public function login(Request $request){
        try {
            $credentials = $request->validate([
                'username' => "required|max:255",
                'password'=> "required",
            ]);

            $user = User::with('profile')->where('username', '=', $request->username)->first();
            if(!$user){
                return response()->json([
                    "success" => false,
                    'message' => "Usernam is not availible"
                ],422);
            }

            if(! Hash::check($request->password, $user->password)){
                return response()->json([
                    "success" => false,
                    'message' => "Wrong Password"
                ],422);
            }
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                "success" => true,
                "message" => "Login Berhasil",
                "user" => $user,
                "token" => $token,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ],422);
        }
    }

    public function register(Request $request){
        try {
            $credentials = $request->validate([
                'username' => "required|max:255",
                'full_name' => "required|max:255",
                'email' => "required|max:255|email",
                'password'=> "required",
                'confirm_password'=> "required",
            ]);

            $user = user_profile::where('email','=',$request->email)->first();
            $username = User::where('username','=',$request->username)->first();
            
            if($user){
                return response()->json([
                    "success" => false,
                    'message' => "Email already exist"
                ],422);
            }
            if($username){
                return response()->json([
                    "success" => false,
                    'message' => "Username already exist"
                ],422);
            }

            if($request->password !== $request->confirm_password){
                return response()->json([
                    "success" => false,
                    'message' => "Password not match"
                ],422);
            }

            $registerUser = DB::table("users")->insert([
                "username" => $request->username,
                "password" => Hash::make($request->password),
            ]);

            if($registerUser){
                $getID = User::where("username",'=',$request->username)->first();

                $IdTerbesar = user_profile::orderByDesc("employee_id")->first();
                $idEmployee = $IdTerbesar ? $IdTerbesar->employee_id + 1 : 100;
                $joinDate = date('Y-m-d');
                $registerProfile = DB::table("users_profile")->insert([
                    "user_id" => $getID->id,
                    "name" => $request->full_name,
                    "email" => $request->email,
                    "employee_id" => $idEmployee,
                    "join_date" => $joinDate,
                ]);
                if($registerProfile){
                    return response()->json([
                        "success" => true,
                        "message" => "Success register account",
                        "user" => $request->all()
                    ],201);
                }else{
                    $deleteUser = User::where("username",'=',$request->username)->delete();
                    if($deleteUser){
                        return response()->json([
                            "success" => false,
                            "message" => "Error register account",
                        ],422);
                    }else{
                        return response()->json([
                            "success" => false,
                            "message" => "Error deleting user",
                        ],422);
                    }
                }
            }


        } catch (\Exception $e) {
             //throw $th;
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ],422);
        }
    }

    public function sendVerifLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users_profile,email',
        ]);
        

        // cek apakah masi ada token aktif
        // $cekToken = DB::table("verification_tokens")->where("email",'=',$request->email)->orderByDesc("created_at")->first();

        // if($cekToken && Carbon::parse($cekToken->created_at)->addMinutes(60) > Carbon::now()){
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Masih terdapat URL Verifikasi Aktif, cek email atau tunggu ' . 
        //                       Carbon::parse($cekToken->created_at)->addMinutes(60)->diffForHumans() . ' lagi',
        //     ],422);
        // }

        $user = user_profile::where('email', $request->email)->first();
        
        $token = sha1(time().$user->email);

        DB::table('verification_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );
        
        $user->notify(new verifyEmail($token));
        
        return response()->json([
            'success' => true,
            'message' => 'URL verifikasi email telah dikirim.',
        ],200);
    }

    public function verifEmail($token){
        $cekToken = DB::table("verification_tokens")->where("token",'=',$token)->first();


        if(!$cekToken || Carbon::parse($cekToken->created_at)->addMinutes(60) < Carbon::now()){
            return view("verifPage")->with(["status" => "URL sudah kadaluarsa, silahkan melakukan verifikasi ulang"]);

        }
        $getIdUser = user_profile::where("email", $cekToken->email)->first();
        $updateuser = DB::table("users")->where('id',$getIdUser->user_id)->update([
            "email_verified_at" => Carbon::now()
        ]);
        if($updateuser){
            DB::table("verification_tokens")->where("token", '=',$token)->delete();
            return view("verifPage")->with(["status" => "Email Berhasil Diverifikasi"]);
        }else{
            return view("verifPage")->with(["status" => "Email Gagal Diverifikasi"]);
        }
    }
}
