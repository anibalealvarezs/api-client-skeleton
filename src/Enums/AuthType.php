<?php

namespace Anibalealvarezs\ApiSkeleton\Enums;

enum AuthType: string
{
    case apiKey = 'apiKey';
    case oAuthV1 = 'oAuthV1';
    case oAuthV2 = 'oAuthV2';
    case bearerToken = 'bearerToken';
    case basic = 'basic';
    case none = 'none';
}
