<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use Throwable;

class SafeFilesystem extends Filesystem
{
    public function replace($path, $content, $mode = null): void
    {
        clearstatcache(true, $path);

        try {
            parent::replace($path, $content, $mode);
        } catch (Throwable $exception) {
            file_put_contents($path, $content, LOCK_EX);

            if (! is_null($mode)) {
                chmod($path, $mode);
            }
        }
    }
}
