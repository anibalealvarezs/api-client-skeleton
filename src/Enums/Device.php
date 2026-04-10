<?php

namespace Anibalealvarezs\ApiSkeleton\Enums;

enum Device: string
{
    case DESKTOP = 'desktop';
    case MOBILE = 'mobile';
    case TABLET = 'tablet';
    case OTHER = 'other';
    case UNKNOWN = 'unknown';
}
