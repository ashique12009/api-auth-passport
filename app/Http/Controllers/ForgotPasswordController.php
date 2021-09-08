<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetRequest;
use App\Mail\ForgotPasswordEmail;
use App\Models\User;
use DB;
use Mail;
use Str;
use Hash;

class ForgotPasswordController extends Controller {
  public function forgot(ForgotPasswordRequest $request) {
    $email = $request->input('email');

    if (User::where('email', $email)->doesntExist()) {
      return response([
        'message' => 'User unavailable',
      ], 404);
    }

    $token = Str::random(10);

    try {
      DB::table('password_resets')->insert([
        'email' => $email,
        'token' => $token,
      ]);

      // Send email
      $details = [
        'token'    => $token,
        'base_url' => env('APP_URL'),
      ];

      Mail::to('test_receiver_email@gmail.com')->send(new ForgotPasswordEmail($details));

      return response([
        'message' => 'Check your email',
      ]);
    } catch (\Exception $exception) {
      return response([
        'message' => $exception->getMessage(),
      ], 400);
    }
  }

  public function reset(ResetRequest $request) {
    $token = $request->input('token');

    $passwordResets = DB::table('password_resets')->where('token', $token)->first();
    if (!$passwordResets) {
      return response([
        'message' => 'Invalid token'
      ], 400);
    }

    $user = User::where('email', $passwordResets->email)->first();
    if (!$user) {
      return response([
        'message' => 'User unavailable'
      ], 400);
    }

    $user->password = Hash::make($request->input('password'));
    $user->save();

    return response([
      'message' => 'success'
    ]);
  }
}
