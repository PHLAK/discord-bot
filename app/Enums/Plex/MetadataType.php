<?php

namespace App\Enums\Plex;

enum MetadataType: string
{
    case ALBUM = 'album';
    case EPISODE = 'episode';
    case MOVIE = 'movie';
    case SHOW = 'show';
    case TRACK = 'track';
}
