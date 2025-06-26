<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function indexGoogle()
    {
        return view('auth.login-google');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login_proses(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Keamanan sesi
            $user = Auth::user();

            // Jika role cocok (kepala_gudang, admin_gudang, kasir, pelanggan), redirect ke home.index
            if (in_array($user->role, ['kepala_gudang', 'admin_gudang', 'kasir', 'pelanggan'])) {
                return redirect()->route('home.index');
            }

            // Jika role tidak dikenali
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Role pengguna tidak diizinkan.',
            ]);
        }

        // Login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }


    /**
     * Store a newly created resource in storage.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Berhasil logout!');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(str()->random(24)), // password acak
                'role' => 'pelanggan', // Atur role sesuai kebutuhan
            ]
        );

        Auth::login($user);

        return redirect('/dashboard'); // arahkan sesuai kebutuhan
    }
}
