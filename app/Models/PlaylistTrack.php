<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistTrack extends Model
{
  
    protected $fillable = [
        'track_id', 
        'track_name', 
        'artist_name', 
        'album_name', 
        'track_image'
    ];
 
     protected $table = 'playlist_tracks';
}
