<?php

namespace App\Support;

use Illuminate\Http\Request;

final class PlatformContext
{
    public static function user(?Request $request = null): PlatformUser
    {
        $request ??= request();
        $user = $request->attributes->get('platform_user');

        return $user instanceof PlatformUser
            ? $user
            : new PlatformUser('system@henrik.local', 'Sistema Henrik', 'system');
    }
}
