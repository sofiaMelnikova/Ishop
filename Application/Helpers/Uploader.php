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
     * @throws \Exception
     */
    public function upload (UploadedFile $file, string $uploadDir) {
        if (!is_writable($uploadDir)) {
            throw new \Exception('Incorrect direction for download file.');
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
        $newName = $originalName . (new RandomString())::get();
        return md5($newName);
    }
}