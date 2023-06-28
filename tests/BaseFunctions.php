<?php

namespace App\Tests;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseFunctions extends WebTestCase
{
    /**
     * Create album.
     */
    public function createAlbum(Category $category, Artist $artist, User $user): Album
    {
        $album = new Album();
        $album->setCreatedAt(new \DateTimeImmutable('now'));
        $album->setUpdatedAt(new \DateTimeImmutable('now'));
        $album->setTitle('Album title');
        $album->setDescription('Album description');
        $album->setYear(1234);
        $album->setMark(4);
        $album->setCategory($category);
        $album->addArtist($artist);
        $album->setAuthor($user);

        $albumRepository = self::getContainer()->get(AlbumRepository::class);
        $albumRepository->save($album);

        return $album;
    }
}
