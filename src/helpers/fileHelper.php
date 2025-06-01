<?php

namespace helpers;

use response\Response;

class FileHelper
{
    public static function UploadFile($request, $response, $patch, $folder)
    {
        $uploadFile = $request->getUploadedFiles();

        if (empty($uploadFile['User_Image'])) {
            return Response::Return400($response, 'Nenhuma imagem enviada');
        }

        $image = $uploadFile['User_Image'];

        $maxFileSize = 500 * 1024;

        if ($image->getSize() > $maxFileSize) {
            return Response::Return400($response, 'Arquivo muito grande. Tamanho máximo de 500kb.');
        }

        $fileName = $image->getClientFilename();
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array(strtolower($extension), $allowedExtensions)) {
            return Response::Return400($response, 'Formato de imagem não aceito');
        }

        $directory = realpath(__DIR__ . $patch);

        if (!is_dir($directory)) {
            echo 'Pasta não criada, criando uma...';
            mkdir($directory, 0775, true);
        }

        $baseName = bin2hex(random_bytes(8));
        $fileName = sprintf('%s.%s', $baseName, $extension);

        $image->moveTo($directory . '/' . $fileName);

        $patchToSave = $folder . $fileName;

        return $patchToSave;
    }

    public static function DeleteUserImage($patch)
    {
        var_dump($patch);

        if ($patch && file_exists($patch)) {
            unlink($patch);
        }
    }
}
