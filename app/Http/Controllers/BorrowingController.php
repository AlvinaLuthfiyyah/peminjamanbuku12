<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Borrowing\BorrowingInterface;

class BorrowingController extends Controller
{
    private BorrowingInterface $borrowingService;

    public function __construct(BorrowingInterface $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }

    public function index()
    {
        $borrowings = $this->borrowingService->getByUserId((string) auth()->id());
        return view('anggota.riwayat', compact('borrowings'));
    }

    public function store(Request $request, string $id)
    {
        $request->validate([
            'durasi' => 'required|integer|min:1|max:30'
        ]);

        try {
            $this->borrowingService->createBorrowing((string) auth()->id(), $id, (int) $request->durasi);
            return redirect()->route('riwayat')->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan admin.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}