<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Borrowing\BorrowingInterface;

class AdminBorrowingController extends Controller
{
    private BorrowingInterface $borrowingService;

    public function __construct(BorrowingInterface $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }

    public function index()
    {
        $borrowings = $this->borrowingService->getAllWithRelations();
        return view('admin.borrowings.index', compact('borrowings'));
    }

    // =========================================================
    // APPROVE
    // =========================================================
    public function approve(string $id)
    {
        try {
            $result = $this->borrowingService->approveBorrowing($id);
            $token = $result['token'];
            return back()->with('success', "Peminjaman disetujui. Token pengambilan: {$token} (berlaku 24 jam).");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =========================================================
    // VALIDASI TOKEN
    // =========================================================
    public function validasiToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        try {
            $result = $this->borrowingService->validateToken($request->token);
            $nama = $result['nama'];
            $judul = $result['judul'];
            return back()->with('success', "✅ Token valid. Buku \"{$judul}\" boleh diambil oleh {$nama}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =========================================================
    // RETURN BUKU
    // =========================================================
    public function return(string $id)
    {
        try {
            $result = $this->borrowingService->returnBorrowing($id);
            $denda = $result['denda'];
            $dendaMsg = $denda > 0 ? " Denda: Rp " . number_format($denda) : '';
            return back()->with('success', 'Buku berhasil dikembalikan.' . $dendaMsg);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =========================================================
    // BATAL PENGEMBALIAN / BATAL PINJAM (Bila expired)
    // =========================================================
    public function cancel(string $id)
    {
        try {
            $this->borrowingService->cancelBorrowing($id);
            return back()->with('success', 'Peminjaman berhasil dibatalkan dan stok dikembalikan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}