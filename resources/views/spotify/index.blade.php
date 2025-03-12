<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Music Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #121212, #1e1e1e);
        }
    </style>
</head>
<body class="min-h-screen text-white">
    <div class="container mx-auto px-4 py-8">
       
        <nav class="mb-8 bg-gray-800/50 backdrop-blur-md rounded-full px-6 py-3 shadow-lg">
            <div class="flex justify-between items-center">
                <div class="flex space-x-6 items-center">
                    <a href="{{ route('spotify.index') }}" class="
                        text-white/70 hover:text-green-400 
                        transition duration-300 
                        {{ request()->routeIs('spotify.index') ? 'text-green-500 font-semibold' : '' }}
                        relative group
                    ">
                        <span class="relative">
                            Home
                            <span class="absolute -bottom-1 left-0 w-0 group-hover:w-full transition-all duration-300 h-0.5 bg-green-500"></span>
                        </span>
                    </a>
                    <a href="{{ route('playlist.index') }}" class="
                        text-white/70 hover:text-green-400 
                        transition duration-300 
                        {{ request()->routeIs('playlist.index') ? 'text-green-500 font-semibold' : '' }}
                        relative group
                    ">
                        <span class="relative">
                            My Playlist
                            <span class="absolute -bottom-1 left-0 w-0 group-hover:w-full transition-all duration-300 h-0.5 bg-green-500"></span>
                        </span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white/70"></i>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Header -->
        <header class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-green-500 mb-4 tracking-tight">
                Spotify Music Explorer
            </h1>
            <p class="text-gray-400 max-w-xl mx-auto">
                Temukan lagu favorit Anda, jelajahi artis baru, dan nikmati musik tanpa batas.
            </p>
        </header>

        <!-- Search Form -->
        <form action="{{ route('spotify.index') }}" method="GET" class="mb-12 max-w-2xl mx-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="query" 
                    placeholder="Cari lagu, artis atau album..." 
                    value="{{ $query ?? '' }}"
                    class="w-full pl-10 pr-4 py-3 bg-gray-800 text-white rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-300"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button type="submit" class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition duration-300">
                    Cari
                </button>
            </div>
        </form>

        <!-- Track Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @forelse($tracks as $track)
                <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg transform hover:scale-105 transition duration-300 ease-in-out">
                    <a href="{{ route('track.show', $track['id']) }}" class="block">
                        <div class="relative">
                            <img 
                                src="{{ $track['album']['images'][0]['url'] ?? 'https://via.placeholder.com/300' }}" 
                                alt="{{ $track['name'] }}"
                                class="w-full h-48 object-cover"
                            >
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2">
                                <h3 class="font-bold text-green-300 truncate text-sm">{{ $track['name'] }}</h3>
                                <p class="text-gray-300 text-xs truncate">{{ $track['artists'][0]['name'] }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="mx-auto h-16 w-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-4 text-xl text-gray-400">Tidak ada lagu ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            @if ($tracks->lastPage() > 1)
                <div class="flex items-center space-x-2 bg-gray-800 rounded-full p-2">
                    @if ($tracks->currentPage() > 1)
                        <a href="{{ $tracks->previousPageUrl() }}" class="px-4 py-2 hover:bg-gray-700 rounded-full transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif

                    @for ($i = 1; $i <= $tracks->lastPage(); $i++)
                        <a href="{{ $tracks->url($i) }}" 
                           class="px-4 py-2 {{ $tracks->currentPage() == $i ? 'bg-green-600 text-white' : 'text-gray-300 hover:bg-gray-700' }} rounded-full transition">
                            {{ $i }}
                        </a>
                    @endfor

                    @if ($tracks->currentPage() < $tracks->lastPage())
                        <a href="{{ $tracks->nextPageUrl() }}" class="px-4 py-2 hover:bg-gray-700 rounded-full transition">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</body>
</html>