<?php

namespace App\Enums;

enum PlexEvent: string
{
    case LIBRARY_ON_DECK = 'library.on.deck';
    case LIBRARY_NEW = 'library.new';

    case MEDIA_PAUSE = 'media.pause';
    case MEDIA_PLAY = 'media.play';
    case MEDIA_RATE = 'media.rate';
    case MEDIA_RESUME = 'media.resume';
    case MEDIA_SCROBBLE = 'media.scrobble';
    case MEDIA_STOP = 'media.stop';

    case ADMIN_DATABASE_BACKUP = 'admin.database.backup';
    case ADMIN_DATABASE_CORRUPTED = 'admin.database.corrupted';

    case DEVICE_NEW = 'device.new';

    case PLAYBACK_STARTED = 'playback.started';
}
