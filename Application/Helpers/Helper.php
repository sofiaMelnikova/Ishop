<?php

namespace Application\Helpers;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Helper
{
    /**
     * @return string
     */
    public function generateRandomString ():string {
        $symbols = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $countSymbols = strlen($symbols);
        $result = '';
        for ($i = 0; $i < 12; $i++) {
            $result .= $symbols[rand(0, $countSymbols - 1)];
        }
        return $result;
    }

    /**
     * @param UploadedFile $file
     * @param string $uploadDir
     * @return string
     */
    public function savePhoto (UploadedFile $file, string $uploadDir):string {
        $uploadFile = time() . $file->getClientOriginalName();
        $file->move($uploadDir, $uploadFile);
        $filePath = 'pictures/' . $uploadDir;
        return $filePath;
    }
}