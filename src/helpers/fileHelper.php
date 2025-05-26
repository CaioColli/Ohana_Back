<?php

namespace helpers;

class FileHelper
{
    public static function DeleteUserImage($patch)
    {
        $fullPatch = __DIR__ . '../../../public/uploads/' . $patch;

        if ($fullPatch && file_exists($fullPatch)) {
            unlink($fullPatch);
        }
    }
}
