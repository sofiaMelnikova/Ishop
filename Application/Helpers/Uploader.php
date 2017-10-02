<?php
/**
 * Created by PhpStorm.
 * User: smelnikova
 * Date: 28.09.17
 * Time: 14:19
 */

namespace Application\Helpers;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    /**
     * @param UploadedFile $file
     * @param string $uploadDir
     * @return string
     */
    public function upload (UploadedFile $file, string $uploadDir) {
        if (!is_dir($uploadDir)) {
            $uploadDir = '/home/smelnikova/Downloads';
        }
        $chmod = substr(sprintf('%o', fileperms('/home/smelnikova/Downloads')), -4);
        if ($chmod < 0755) {
            $uploadDir = '/home/smelnikova/Downloads';
        }
        $fileName = $this->createUniqueName($file->getClientOriginalName());
        $file->move($uploadDir, $fileName);
        return $fileName;
    }

    /**
     * @param string $originalName
     * @return string
     */
    private function createUniqueName (string $originalName):string {
        $newName = $originalName . (new RandomString())->get();
        return md5($newName);
    }
}