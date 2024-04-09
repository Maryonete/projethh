<?php

namespace App\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(private SluggerInterface $slugger, private string $upload_directory)
    {

    }

    public function upload(UploadedFile $file, ?string $directory = null): string
    {
        $original_filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safe_filename = $this->slugger->slug($original_filename);

        $explode = explode('.', $file->getClientOriginalName());
        $extension = '' !== $file->getExtension() ? $file->getExtension() : end($explode);

        $filename = $safe_filename.'-'.uniqid().'.'.$extension;

        try {
            $full_dir = $this->upload_directory.($directory ? '/'.$directory : '');
            $fs = new Filesystem();

            if (!$fs->exists($full_dir)) {
                $fs->mkdir($full_dir);
            }

            $file->move($full_dir, $filename);
        } catch (\Exception $e) {
            // renvoyer et gérer les erreur
        }

        return $filename;
    }
}
