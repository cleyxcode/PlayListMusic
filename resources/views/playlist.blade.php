<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Playlist</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-900 text-white min-h-screen p-6">
    <div class="container mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-green-400">My Playlist</h1>
            <a href="{{ route('spotify.index') }}" class="text-green-400 hover:text-green-300">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @forelse($tracks as $track)
                <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg relative">
                    <!-- Tambahkan pengecekan untuk gambar -->
                    @if($track->track_image)
                        <img 
                            src="{{ $track->track_image }}" 
                            alt="{{ $track->track_name }}"
                            class="w-full h-48 object-cover"
                        >
                    @else
                        <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-music text-gray-500 text-3xl"></i>
                        </div>
                    @endif
                    
                    <div class="p-4">
                        <h3 class="font-bold text-green-300 truncate">{{ $track->track_name }}</h3>
                        <p class="text-gray-400 text-sm truncate">{{ $track->artist_name }}</p>
                        
                        <button 
                            onclick="deleteTrack({{ $track->id }})" 
                            class="mt-2 w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-music text-6xl text-gray-500 mb-4"></i>
                    <p class="text-gray-400">Playlist Anda kosong</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function deleteTrack(trackId) {
            axios.delete(`/playlist/track/${trackId}`)
                .then(response => {
                    window.location.reload();
                })
                .catch(error => {
                    alert('Gagal menghapus lagu');
                });
        }
    </script>
</body>
</html>