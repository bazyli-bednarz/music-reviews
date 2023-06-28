<?php
/**
 * Cover service interface.
 */

namespace App\Service;

use App\Entity\Album;
use App\Entity\Cover;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Cover service.
 */
interface CoverServiceInterface
{
    /**
     * Create cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     * @param Album        $album        Album
     */
    public function create(UploadedFile $uploadedFile, Cover $cover, Album $album): void;

    /**
     * Update cover.
     *
     * @param UploadedFile $uploadedFile Uploaded file
     * @param Cover        $cover        Cover entity
     * @param Album        $album        Album
     */
    public function update(UploadedFile $uploadedFile, Cover $cover, Album $album): void;
}
