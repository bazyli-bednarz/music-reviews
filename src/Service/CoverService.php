<?php
/**
 * Cover service.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Cover;
use App\Repository\CoverRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class Cover service.
 */
class CoverService implements CoverServiceInterface
{
    /**
     * Target directory.
     */
    private string $targetDirectory;

    /**
     * Cover repository.
     */
    private CoverRepository $coverRepository;

    /**
     * File upload service.
     */
    private FileUploadServiceInterface $fileUploadService;

    /**
     * File system service.
     */
    private Filesystem $filesystem;

    /**
     * Constructor.
     *
     * @param string                     $targetDirectory   Target directory
     * @param CoverRepository            $coverRepository   CoverRepository
     * @param FileUploadServiceInterface $fileUploadService FileUploadServiceInterface
     * @param Filesystem                 $filesystem        Filesystem
     */
    public function __construct(string $targetDirectory, CoverRepository $coverRepository, FileUploadServiceInterface $fileUploadService, Filesystem $filesystem)
    {
        $this->targetDirectory = $targetDirectory;
        $this->coverRepository = $coverRepository;
        $this->fileUploadService = $fileUploadService;
        $this->filesystem = $filesystem;
    }

    /**
     * Create cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     * @param Album        $album        Album interface
     */
    public function create(UploadedFile $uploadedFile, Cover $cover, Album $album): void
    {
        $coverFilename = $this->fileUploadService->upload($uploadedFile);

        $cover->setAlbum($album);
        $cover->setFilename($coverFilename);
        $this->coverRepository->save($cover);
    }

    /**
     * Update cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover
     * @param Album        $album        Album
     */
    public function update(UploadedFile $uploadedFile, Cover $cover, Album $album): void
    {
        $filename = $cover->getFilename();

        if (null !== $filename) {
            $this->filesystem->remove(
                $this->targetDirectory.'/'.$filename
            );
        }

        $this->create($uploadedFile, $cover, $album);
    }
}
