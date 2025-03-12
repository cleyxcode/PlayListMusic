<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class SpotifyController extends Controller
{
    // Fungsi index adalah fungsi yang akan dijalankan ketika pengguna mengakses halaman ini
public function index(Request $request)
{
    // Mengambil data yang dikirim oleh pengguna melalui form (misalnya: kata kunci pencarian)
    // Jika pengguna tidak mengirimkan data, maka akan menggunakan kata kunci "populer" sebagai default
    $query = $request->input('query', 'populer');
    
    // Mencoba menjalankan kode di dalam try-catch
    try {
        // Mengambil token akses untuk menggunakan API Spotify
        $token = $this->getAccessToken();
        
        // Menggunakan token akses untuk mengakses API Spotify dan melakukan pencarian lagu
        // Parameter 'q' adalah kata kunci pencarian, 'type' adalah jenis pencarian (dalam hal ini adalah lagu), dan 'limit' adalah jumlah hasil pencarian yang akan ditampilkan
        $response = Http::withToken($token)
            ->get('https://api.spotify.com/v1/search', [
                'q' => $query,
                'type' => 'track',
                'limit' => 50
            ]);

        // Mengambil data hasil pencarian dari API Spotify
        $tracksData = $response->json()['tracks']['items'];

        // Mengatur pagination (pembagian hasil pencarian menjadi beberapa halaman)
        // Mengambil nomor halaman yang sedang dilihat oleh pengguna
        $currentPage = $request->get('page', 1);
        // Mengatur jumlah hasil pencarian yang akan ditampilkan per halaman
        $perPage = 10;
        // Menghitung offset (posisi awal) hasil pencarian yang akan ditampilkan
        $offset = ($currentPage - 1) * $perPage;
        
        // Mengambil hasil pencarian yang akan ditampilkan pada halaman ini
        $paginatedTracks = array_slice($tracksData, $offset, $perPage);
        
       // Membuat objek pagination untuk mengatur hasil pencarian
$tracks = new LengthAwarePaginator(
    // Data lagu yang telah dipaginasi
    $paginatedTracks, 
    // Jumlah total data lagu
    count($tracksData), 
    // Jumlah data lagu yang ditampilkan per halaman
    $perPage, 
    // Nomor halaman yang sedang ditampilkan
    $currentPage,
    // Opsi untuk mengatur URL pagination
    [
        // URL dasar untuk pagination
        'path' => $request->url(),
        // Query string yang digunakan untuk pagination
        'query' => $request->query()
    ]
);
        // Mengembalikan hasil pencarian ke view (halaman yang akan ditampilkan kepada pengguna)
        return view('spotify.index', compact('tracks', 'query'));

    } catch (\Exception $e) {
        // Jika terjadi kesalahan, maka akan mencatat kesalahan tersebut
        Log::error('Spotify Search Error: ' . $e->getMessage());
        
        // Mengembalikan hasil pencarian yang kosong ke view, beserta pesan kesalahan
        return view('spotify.index', [
            'tracks' => new LengthAwarePaginator([], 0, 10),
            'query' => $query,
            'error' => 'Pencarian gagal: ' . $e->getMessage()
        ]);
    }
}
   // Fungsi showTrack digunakan untuk menampilkan detail lagu berdasarkan ID lagu yang diberikan
public function showTrack($id)
{
    // Mencoba menjalankan kode di dalam try-catch untuk menangani kesalahan
    try {
        // Mendapatkan token akses Spotify yang digunakan untuk mengakses API Spotify
        $token = $this->getAccessToken();
        
        // Menggunakan token akses untuk mengakses API Spotify dan mendapatkan detail lagu
        $response = Http::withToken($token)
            // Mengirimkan permintaan GET ke API Spotify untuk mendapatkan detail lagu
            ->get("https://api.spotify.com/v1/tracks/{$id}");

        // Mengambil data detail lagu dari respons API Spotify
        $track = $response->json();
        
        // Mengembalikan view spotify.show dengan data detail lagu yang telah diambil
        return view('spotify.show', compact('track'));

    } catch (\Exception $e) {
        // Jika terjadi kesalahan, maka akan mencatat kesalahan tersebut di log
        Log::error('Track Details Error: ' . $e->getMessage());
        // Mengembalikan pesan kesalahan ke pengguna dengan redirect ke halaman sebelumnya
        return back()->with('error', 'Gagal mengambil detail lagu');
    }
}

// Fungsi getAccessToken digunakan untuk mendapatkan token akses Spotify
protected function getAccessToken()
{
    // Menggunakan cache untuk menyimpan token akses sehingga tidak perlu meminta token akses setiap kali
    return Cache::remember('spotify_token', 3600, function () {
        // Mengirimkan permintaan POST ke API Spotify untuk mendapatkan token akses
        $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
            // Menentukan jenis permintaan token akses
            'grant_type' => 'client_credentials',
            // Menentukan ID klien Spotify
            'client_id' => env('SPOTIFY_CLIENT_ID'),
            // Menentukan rahasia klien Spotify
            'client_secret' => env('SPOTIFY_CLIENT_SECRET')
        ]);

        // Mengambil token akses dari respons API Spotify
        return $response->json()['access_token'];
    });
 }
}