<?php

namespace Anibalealvarezs\ApiSkeleton\Enums;

enum EncodingMethod: string
{
    case base64 = 'base64';
    case url = 'url';
    case none = 'none';
}
