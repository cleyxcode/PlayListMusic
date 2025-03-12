<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlaylistTrack;

class PlaylistController extends Controller
{
   // Fungsi addTrack digunakan untuk menambahkan lagu ke playlist
public function addTrack(Request $request)
{
    // Validasi data yang dikirimkan oleh pengguna
    // Jika data tidak valid, maka akan mengembalikan respons error
    $validatedData = $request->validate([
        // ID lagu harus diisi dan berupa string
        'track_id' => 'required|string',
        // Nama lagu harus diisi dan berupa string
        'track_name' => 'required|string',
        // Nama artis harus diisi dan berupa string
        'artist_name' => 'required|string',
        // Nama album boleh kosong dan berupa string
        'album_name' => 'nullable|string',
        // Gambar lagu boleh kosong dan berupa string
        'track_image' => 'nullable|string'
    ]);

    // Periksa apakah lagu sudah ada di playlist
    // Jika lagu sudah ada, maka akan mengembalikan respons error
    if (PlaylistTrack::where('track_id', $validatedData['track_id'])->exists()) {
        // Mengembalikan respons error dengan kode status 400
        return response()->json(['success' => false, 'message' => 'Lagu sudah ada di playlist'], 400);
    }

    // Coba menambahkan lagu ke playlist
    try {
        // Menambahkan lagu ke playlist
        $track = PlaylistTrack::create($validatedData);
        // Mengembalikan respons sukses dengan data lagu yang baru ditambahkan
        return response()->json(['success' => true, 'message' => 'Lagu berhasil ditambahkan ke playlist', 'track' => $track]);
    } catch (\Exception $e) {
        // Jika terjadi kesalahan, maka akan mengembalikan respons error
        // Dengan kode status 500 dan pesan kesalahan
        return response()->json(['success' => false, 'message' => 'Gagal menambahkan lagu: ' . $e->getMessage()], 500);
    }
}
   // Fungsi index digunakan untuk menampilkan semua lagu di playlist
public function index()
{
    // Mengembalikan view playlist dengan data semua lagu di playlist
    // Data lagu diambil dari database menggunakan metode all()
    return view('playlist', ['tracks' => PlaylistTrack::all()]);
}

// Fungsi deleteTrack digunakan untuk menghapus lagu dari playlist
public function deleteTrack($id)
{
    // Coba menghapus lagu dari playlist
    try {
        // Menggunakan metode findOrFail untuk mencari lagu berdasarkan ID
        // Jika lagu tidak ditemukan, maka akan mengembalikan respons error
        PlaylistTrack::findOrFail($id)->delete();
        // Mengembalikan respons sukses dengan pesan bahwa lagu berhasil dihapus
        return response()->json(['success' => true, 'message' => 'Lagu berhasil dihapus dari playlist']);
    } catch (\Exception $e) {
        // Jika terjadi kesalahan, maka akan mengembalikan respons error
        // Dengan kode status 500 dan pesan kesalahan
        return response()->json(['success' => false, 'message' => 'Gagal menghapus lagu: ' . $e->getMessage()], 500);
    }
}

// Fungsi checkTrack digunakan untuk memeriksa apakah lagu sudah ada di playlist
public function checkTrack($trackId)
{
    // Mengembalikan respons JSON dengan data apakah lagu sudah ada di playlist
    // Data diambil dari database menggunakan metode where dan exists()
    return response()->json(['exists' => PlaylistTrack::where('track_id', $trackId)->exists()]);
}
}