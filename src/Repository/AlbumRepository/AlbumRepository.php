<?php
/**
 * Album repository.
 */

namespace App\Repository\AlbumRepository;

/**
 * Class AlbumRepository.
 */
class AlbumRepository
{
    /**
     * Album data.
     *
     * @var array|array[]
     */
    private array $data = [
        1 => [
            'id' => 1,
            'title' => 'Master of Puppets',
            'year' => 1986,
            'description' => 'Lorem ipsum, dolor sit amet',
            'mark' => 5,
        ],
        2 => [
            'id' => 2,
            'title' => 'Dummy',
            'year' => 1995,
            'description' => 'Lorem ipsum, dolor sit amet',
            'mark' => 5,
        ],
        3 => [
            'id' => 3,
            'title' => 'Unknown Pleasures',
            'year' => 1979,
            'description' => 'Lorem ipsum, dolor sit amet',
            'mark' => 5,
        ],
    ];

    /**
     * Find all.
     *
     * @return array|array[]
     */
    public function findAll(): array
    {
        return $this->data;
    }

    /**
     * Find one by id.
     */
    public function findOneById(int $id): ?array
    {
        return count($this->data) && isset($this->data[$id]) ? $this->data[$id] : null;
    }
}
