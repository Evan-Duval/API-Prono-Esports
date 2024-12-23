<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Mail\PasswordResetNotification;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
    * Create user
    *
    * @param  [string] name
    * @param  [string] email
    * @param  [string] password
    * @param  [string] password_confirmation
    * @return [string] message
    */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email'=>'required|string|unique:users',
            'password'=>'required|string|min:8',
            'c_password' => 'required|same:password'
        ]);

        $user = new User([
            'name'  => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if($user->save()){
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
            'message' => 'Successfully created user!',
            'accessToken'=> $token,
            ],201);
        }
        else{
            return response()->json(['error'=>'Provide proper details']);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
    */

    public function login(Request $request)
    {
        $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
        'remember_me' => 'boolean'
        ]);

        $credentials = request(['email','password']);
        if(!Auth::attempt($credentials))
        {
        return response()->json([
            'message' => 'Unauthorized'
        ],401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
        'accessToken' =>$token,
        'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
    */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
    */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
        'message' => 'Successfully logged out'
        ]);

    }

    /**
     * Change le mot de passe d'un utilisateur
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        // Validation des données
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'current_password' => ['required'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],
            'new_password_confirmation' => ['required']
        ]);

        try {
            // Recherche de l'utilisateur par email
            $user = User::where('email', $request->email)->first();

            // Vérification du mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Le mot de passe actuel est incorrect',
                    'status' => 'error'
                ], 422);
            }

            // Mise à jour du mot de passe
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Mot de passe modifié avec succès',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors du changement de mot de passe',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * The function `resetpassword` resets a user's password, sends a notification email with the new
     * password, and handles exceptions.
     * 
     * @param Request request The code you provided is a PHP function that resets a user's password. It
     * first validates the email provided in the request, then searches for the user with that email,
     * resets the password to a default value 'Password1234', saves the new password hashed, sends an
     * email notification with the new password
     * 
     * @return `resetpassword` function returns a JSON response with a success message if the
     * password reset and email sending were successful. If an error occurs during the process, it
     * returns a JSON response with an error message and the specific error message caught in the catch
     * block.
     */
    public function resetpassword(Request $request)
    {
        // Validation de l'email
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            // Recherche de l'utilisateur
            $user = User::where('email', $request->email)->first();
            
            // Reset du mot de passe
            $newPassword = 'Password1234';
            $user->password = Hash::make($newPassword);
            $user->save();

            // Envoi de l'email
            Mail::to($user->email)->send(new PasswordResetNotification($newPassword));

            return response()->json([
                'message' => 'Mot de passe réinitialisé avec succès et email envoyé',
                'status' => 'success'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue lors de la réinitialisation du mot de passe',
                'error' => $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

}