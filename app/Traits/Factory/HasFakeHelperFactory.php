<?php

namespace App\Traits\Factory;

use Illuminate\Http\UploadedFile;

trait HasFakeHelperFactory
{
    private function generateBase64Image(): string
    {
        return 'data:image/jpg;base64,' . base64_encode(
            file_get_contents('https://api.lorem.space/image/movie?w=450&h=700')
        );
    }

    private function generateFilmPlaceholderBase64Image(): string
    {
        return 'data:image/jpg;base64,' . base64_encode(
            file_get_contents('https://api.lorem.space/image/movie?w=450&h=700')
        );
    }

    private function generateAvatarBase64Image(): string
    {
        return 'data:image/jpg;base64,' . base64_encode(
            file_get_contents('https://dummyimage.com/512x512/000000/fff&text=John+Wick')
        );
    }

    private function avatarBase64(): string
    {
        $path = storage_path('seeders/avatars/avatar_1.jpg');

        return 'data:image/jpg;base64,' . base64_encode(file_get_contents($path));
    }

    private function getTestPdfFile(): UploadedFile
    {
        $filename = storage_path('seeders/documents/pdf-test.pdf');

        return new UploadedFile($filename, 'pdf-test.pdf', 'application/pdf', filesize($filename), 0, true);
    }
}
