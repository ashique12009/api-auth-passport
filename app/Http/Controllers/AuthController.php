<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller {

  public function login(Request $request) {
    if (Auth::attempt($request->only('email', 'password'))) {
      $user = Auth::user();
      return $user;
    }

    return response([
      'message' => 'Invalid username/password'
    ], 401);
  }

}
