<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Particulier;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\ResetPasswordMail;

class AuthWebController extends Controller
{
    public function showLogin()  { 
        return view('auth.login'); 
    }

    public function showRegister(){
        return view('auth.register'); 
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pass'  => 'required',
        ]);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->pass, $user->pass)) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        if ($user->role === 'bloque') {
            return back()->withErrors([
                'email' => 'Votre compte a été suspendu. Contactez l\'administrateur.',
            ])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->intended($this->redirectBasedOnRole($user));
    }

    public function register(Request $request)
    {
        $request->validate([
            'email'             => 'required|email|unique:utilisateurs,email',
            'pass'              => 'required|min:8|confirmed',
            'nom'               => 'required|string|max:100',
            'prenom'            => 'required|string|max:100',
            'role'              => 'required|in:particulier,entreprise',
        ]);

        $user = Utilisateur::create([
            'email'  => $request->email,
            'pass'   => Hash::make($request->pass),
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'role'   => $request->role,
        ]);

        if ($request->role === 'particulier') {
            Particulier::create(['utilisateur_id' => $user->id]);
        } else {
            Entreprise::create(['utilisateur_id' => $user->id, 'nom' => $request->nom]);
        }

        Auth::login($user);
        return redirect($this->redirectBasedOnRole($user))->with('success', 'Bienvenue sur JobConnect !');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:utilisateurs,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Le lien de réinitialisation est invalide ou expiré.']);
        }

        // Expiration (par exemple 60 minutes)
        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Ce lien a expiré. Veuillez refaire une demande.']);
        }

        // Mise à jour du mot de passe
        $user = Utilisateur::where('email', $request->email)->first();
        $user->pass = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé. Connectez-vous.');
    }

    private function redirectBasedOnRole(Utilisateur $user): string
    {
        return match($user->role) {
            'admin'      => route('admin.dashboard'),
            'entreprise' => route('entreprise.dashboard'),
            default      => route('particulier.profil'),
        };
    }
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:utilisateurs,email']);

        $token = Str::random(64);

        // Supprimer les anciens tokens pour cet email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Stocker le token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
        Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));

        return back()->with('status', 'Un lien de réinitialisation vous a été envoyé par email.');
    }


}