<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $track['name'] }} - Track Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background-color: #121212;
        }
    </style>
</head>
<body class="min-h-screen text-white p-4 bg-gray-900">
    <div class="container mx-auto max-w-4xl">
        <div class="bg-gray-800 rounded-3xl shadow-2xl p-6 relative">
            <a href="{{ route('spotify.index') }}" class="absolute top-4 left-4 text-green-400 hover:text-green-300">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            
            <div class="text-center mb-6">
                <img 
                    src="{{ $track['album']['images'][0]['url'] }}" 
                    alt="{{ $track['name'] }}" 
                    class="w-64 h-64 mx-auto rounded-xl shadow-2xl object-cover"
                >
            </div>
            
            <div>
                <h1 class="text-3xl font-bold text-green-400 mb-4 text-center">
                    {{ $track['name'] }}
                </h1>
                <p class="text-center text-gray-300 text-lg mb-6">
                    {{ implode(', ', array_column($track['artists'], 'name')) }}
                </p>

                <div class="space-y-4 text-center bg-gray-700 rounded-xl p-6">
                    <div class="flex justify-between items-center border-b border-gray-600 pb-3">
                        <span class="font-semibold text-gray-300">Album</span>
                        <span class="text-green-400 truncate max-w-[200px]">
                            {{ $track['album']['name'] }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-gray-600 pb-3">
                        <span class="font-semibold text-gray-300">Tanggal Rilis</span>
                        <span class="text-green-400">
                            {{ \Carbon\Carbon::parse($track['album']['release_date'])->format('d M Y') }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center border-b border-gray-600 pb-3">
                        <span class="font-semibold text-gray-300">Popularitas</span>
                        <span class="{{ 
                            ($track['popularity'] ?? 0) >= 80 ? 'text-green-500' : 
                            (($track['popularity'] ?? 0) >= 50 ? 'text-yellow-500' : 'text-red-500')
                        }}">
                            {{ number_format($track['popularity'] ?? 0, 0) }}%
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-300">Durasi</span>
                        <span class="text-green-400">
                            {{ gmdate("i:s", $track['duration_ms'] / 1000) }}
                        </span>
                    </div>
                </div>

                @if($track['preview_url'])
                <div class="mt-6 bg-gray-700 rounded-xl p-4">
                    <h3 class="text-xl font-bold text-center mb-4 text-green-400">
                        <i class="fas fa-music mr-2"></i>Track Preview
                    </h3>
                    <audio controls class="w-full rounded-lg">
                        <source src="{{ $track['preview_url'] }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
                @else
                <p class="text-yellow-500 text-center mt-6 bg-gray-700 rounded-xl p-4">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Preview tidak tersedia
                </p>
                @endif

                <!-- Tautan Spotify -->
                <div class="mt-6 text-center space-y-4">
                    <a 
                        href="{{ $track['external_urls']['spotify'] }}" 
                        target="_blank" 
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors inline-block mr-4"
                    >
                        <i class="fab fa-spotify mr-2"></i>Buka di Spotify
                    </a>

                    <!-- Button Tambah Playlist -->
                    <button 
                        id="addToPlaylistBtn"
                        onclick="addToPlaylist(
                            '{{ $track['id'] }}', 
                            '{{ addslashes($track['name']) }}', 
                            '{{ addslashes(implode(', ', array_column($track['artists'], 'name'))) }}'
                        )" 
                        class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors"
                    >
                        <i class="fas fa-plus-circle mr-2"></i>Tambah ke Playlist
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Tambah Playlist -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Set CSRF Token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function addToPlaylist(trackId, trackName, artistName) {
            // Nonaktifkan tombol saat proses
            const btn = document.getElementById('addToPlaylistBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menambahkan...';

            // Cek terlebih dahulu apakah track sudah ada di playlist
            axios.get(`/playlist/check/${trackId}`)
                .then(response => {
                    if (response.data.exists) {
                        // Jika track sudah ada di playlist
                        alert('Lagu sudah ada di playlist');
                        resetButton(btn);
                        return;
                    }

                    // Jika track belum ada, tambahkan ke playlist
                    return axios.post('/playlist/add', {
                        track_id: trackId,
                        track_name: trackName,
                        artist_name: artistName,
                        album_name: '{{ addslashes($track['album']['name']) }}',
                        track_image: '{{ $track['album']['images'][0]['url'] }}'
                    });
                })
                .then(response => {
                    if (response && response.data.success) {
                        alert('Lagu berhasil ditambahkan ke playlist');
                    }
                    resetButton(btn);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menambahkan lagu ke playlist');
                    resetButton(btn);
                });
        }

        function resetButton(btn) {
            // Kembalikan tombol ke kondisi semula
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus-circle mr-2"></i>Tambah ke Playlist';
        }
    </script>
</body>
</html>