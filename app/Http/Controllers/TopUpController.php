<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Deposit;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;

class TopUpController extends Controller
{
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('TopUp Request Start', [
            'user_id' => Auth::id(),
            'inputs' => $request->all(),
            'has_file' => $request->hasFile('proof_image'),
            'content_length' => $_SERVER['CONTENT_LENGTH'] ?? 'unknown',
        ]);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:10000'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'proof_image' => ['required', 'image', 'max:2048'], // Mandatory proof
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('payment_proofs', 'public');
        }

        $deposit = Deposit::create([
            'user_id' => Auth::id(),
            'bank_account_id' => $request->bank_account_id,
            'amount' => $request->amount,
            'status' => 'pending',
            'proof_image' => $proofPath,
        ]);
        
        $bank = BankAccount::find($request->bank_account_id);

        return back()->with('success', "Permintaan Top Up Rp " . number_format($request->amount, 0, ',', '.') . " berhasil dibuat. Silakan transfer ke " . $bank->bank_name . " (" . $bank->account_number . " a.n " . $bank->account_holder . "). Tunggu konfirmasi Admin.");
    }
}
