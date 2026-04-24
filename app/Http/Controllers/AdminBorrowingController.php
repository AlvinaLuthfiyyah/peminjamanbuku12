<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Borrowing;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminBorrowingController extends Controller
{
    public function index()
    {
        $borrowings = Borrowing::with(['user', 'book'])->latest()->get();
        return view('admin.borrowings.index', compact('borrowings'));
    }

    // ✅ RETURN BUKU + DENDA
    public function return($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if ($borrowing->status === 'dikembalikan') {
            return back()->with('error', 'Sudah dikembalikan');
        }

        // 🔥 HITUNG TELAT (POSITIF = TELAT)
        $telat = Carbon::parse($borrowing->tanggal_kembali_rencana)
            ->diffInDays(now(), false);

        $denda = 0;
        if ($telat > 0) {
            $denda = $telat * 1000; // 1000/hari
        }

        $borrowing->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now(),
            'denda' => $denda
        ]);

        // 🔥 KEMBALIKAN STOK
        $borrowing->book->increment('stok');

        return back()->with('success', 'Buku dikembalikan');
    }

    // ✅ APPROVE (BUAT TOKEN DOANG)
    public function approve($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        $book = $borrowing->book;

        if ($book->stok <= 0) {
            return back()->with('error', 'Stok habis!');
        }

        $token = 'TRX-' . strtoupper(Str::random(8));

        $borrowing->update([
            'status' => 'approved',
            'token' => $token,
            'token_expired_at' => now()->addDay(),
            'token_used' => false
        ]);

        return back()->with('success', 'Disetujui + token dibuat');
    }

    // ✅ VALIDASI TOKEN = BUKU RESMI DIPINJAM
    public function validasiToken(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $borrowing = Borrowing::where('token', $request->token)->first();

        if (!$borrowing) {
            return back()->with('error', 'Token tidak ditemukan');
        }

        if ($borrowing->token_used) {
            return back()->with('error', 'Token sudah digunakan');
        }

        if (now()->gt($borrowing->token_expired_at)) {
            return back()->with('error', 'Token sudah expired');
        }

        // 🔥 FINAL STEP
        $borrowing->update([
            'token_used' => true,
            'status' => 'dipinjam'
        ]);

        // 🔥 STOK BARU DIKURANGI DI SINI
        $borrowing->book->decrement('stok');

        return back()->with('success', 'Token valid, buku sudah dipinjam');
    }

    // ================= LAPORAN =================

    public function laporan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $borrowings = Borrowing::with(['user', 'book'])
            ->whereMonth('tanggal_pinjam', $bulan)
            ->whereYear('tanggal_pinjam', $tahun)
            ->latest()
            ->get();

        return view('admin.laporan.index', compact('borrowings', 'bulan', 'tahun'));
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $borrowings = Borrowing::with(['user', 'book'])
            ->whereMonth('tanggal_pinjam', $bulan)
            ->whereYear('tanggal_pinjam', $tahun)
            ->get();

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('borrowings', 'bulan', 'tahun'));

        return $pdf->download('laporan-peminjaman.pdf');
    }
}