<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpotifyController;
use App\Http\Controllers\PlaylistController;

// Redirect root route to SpotifyController@index
Route::get('/', [SpotifyController::class, 'index'])->name('home');

Route::get('/spotify', [SpotifyController::class, 'index'])->name('spotify.index');
Route::get('/search', [SpotifyController::class, 'searchTracks'])->name('spotify.search');
Route::get('/track/{id}', [SpotifyController::class, 'showTrack'])->name('track.show');

Route::post('/playlist/add', [PlaylistController::class, 'addTrack'])->name('playlist.add');
Route::get('/playlist', [PlaylistController::class, 'index'])->name('playlist.index');
Route::delete('/playlist/track/{id}', [PlaylistController::class, 'deleteTrack'])->name('playlist.delete');
Route::get('/playlist/check/{trackId}', [PlaylistController::class, 'checkTrack'])->name('playlist.check');
