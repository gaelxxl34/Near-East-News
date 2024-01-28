<?php

// app/Http/Controllers/FirebaseRegisterController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;
use Session;
use Kreait\Firebase\Exception\FirebaseException;
use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Kreait\Firebase\Exception\Auth;


class FirebaseRegisterController extends Controller
{
    protected $auth;

    public function __construct()
    {
        if (env('FIREBASE_CREDENTIALS_BASE64')) {
            $firebaseCredentialsJson = base64_decode(env('FIREBASE_CREDENTIALS_BASE64'));
            if (!$firebaseCredentialsJson) {
                throw new \Exception('Failed to decode FIREBASE_CREDENTIALS_BASE64');
            }
            $serviceAccount = json_decode($firebaseCredentialsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to decode JSON: ' . json_last_error_msg());
            }
        } else {
            $firebaseCredentialsPath = env('FIREBASE_CREDENTIALS');
            if (!$firebaseCredentialsPath || !file_exists($firebaseCredentialsPath)) {
                throw new \Exception('Firebase credentials file path is not set or file does not exist');
            }
            $serviceAccount = $firebaseCredentialsPath;
        }
    
        $firebaseFactory = (new Factory)->withServiceAccount($serviceAccount)->withDatabaseUri(env('FIREBASE_DATABASE_URL'));
    
        $this->auth = $firebaseFactory->createAuth();
    }



    public function showRegistrationForm()
    {
        return view('register'); // Ensure this matches the name of your Blade template
    }
    


     public function Home()
     {
         return view('home');
     }



    public function register(Request $request)
    {
       
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        try {
            $userProperties = [
                'email' => $request->email,
                'emailVerified' => false,
                'password' => $request->password,
                'disabled' => false,
            ];
            $createdUser = $this->auth->createUser($userProperties);


            // Add user data to Firestore
            $firestore = app('firebase.firestore');
            $database = $firestore->database();
            $usersRef = $database->collection('Users');
            $usersRef->document($createdUser->uid)->set([
                'email' => $request->email,
                'role' => 'user'
            ]);

            // Return a success response
            session(['user_email' => $request->email]); // Store email in session
            return redirect()->intended('home'); // Redirect to 'home' or any other route
        } catch (\Throwable $e) {
            // If there was an error creating the user, return an error response
             return back()->withErrors(['register_error' => 'Email already exists.'])->with('message', 'Error creating user');
         }
    }




}


