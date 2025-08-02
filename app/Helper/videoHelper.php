<?php

namespace App\Helper;

use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Log;

class videoHelper
{
    //helper function to convert seconds to H:M:S format
    public function secondsToHms($seconds): string
    {
        return gmdate('H:i:s', $seconds);
    }

    //helper function to convert H:M:S to seconds
    public function hmsToSeconds($hms): int
    {
        sscanf($hms, "%d:%d:%d", $hours, $minutes, $seconds);
        return $hours * 3600 + $minutes * 60 + $seconds;
    }

    public function getVideoDuration($videoUrl)
    {
            $ffprobe = FFProbe::create();
            $duration = $ffprobe
                ->format($videoUrl)
                ->get('duration');

            return $duration;
    }
}
