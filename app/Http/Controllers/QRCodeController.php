<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    private $key;

    public function __construct()
    {
        $this->key = env('APP_QR_KEY');
    }

    public function createForm()
    {
        return view('form');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        // Check if user exists by email or phone
        $user = User::where('email', $validated['email'])
            ->orWhere('phone', $validated['phone'])
            ->first();

        if (!$user) {
            // User not found, create new
            $user = User::create($validated);
        }

        // If qr_token not set, generate and save it
        if (!$user->qr_token) {
            $payload = json_encode([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]);

            $encrypted = $this->encrypt($payload);
            $user->qr_token = $encrypted;
            $user->save();
        }

        // Generate QR code from qr_token
        $qrCode = 'data:image/png;base64,' . base64_encode(
            QrCode::format('png')->size(300)->generate($user->qr_token)
        );

        return view('show', compact('qrCode', 'user'));
    }


    public function scan(Request $request)
    {
        $encryptedToken = $request->input('qr_token');
        if (!$encryptedToken) {
            return response()->json(['error' => 'No QR token provided'], 400);
        }

        $decrypted = $this->decrypt($encryptedToken);
        if (!$decrypted) {
            return response()->json(['error' => 'Invalid QR token'], 400);
        }

        $payload = json_decode($decrypted, true);
        $user = User::find($payload['user_id']);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->increment('scan_count');

        return response()->json(['user' => $user]);
    }


    public function reset(Request $request)
    {
        $user = User::where('id', $request->input('user_id'))->first();
        if ($user) {
            $user->scan_count = 0;
            $user->save();
            return response()->json(['message' => 'Scan count reset']);
        }
        return response()->json(['error' => 'User not found'], 404);
    }

    private function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes(16);
        $key = substr(hash('sha256', $this->key, true), 0, 32);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    private function decrypt($data)
    {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $key = substr(hash('sha256', $this->key, true), 0, 32);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
    // app/Http/Controllers/QRCodeController.php

    public function showQRCode(User $user)
    {
        if (!$user->qr_token) {
            return "No QR token found for user.";
        }

        $qrCode = 'data:image/png;base64,' . base64_encode(
            \QrCode::format('png')->size(300)->generate($user->qr_token)
        );

        return view('show-qr', compact('qrCode', 'user'));
    }
}
