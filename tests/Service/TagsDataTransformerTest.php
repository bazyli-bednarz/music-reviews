<?php
/**
 * TagsDataTransformerTest.
 */

namespace App\Tests\Service;


use App\Entity\Tag;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Service\TagService;
use App\Service\TagServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagsDataTransformerTest.
 */
class TagsDataTransformerTest extends KernelTestCase
{
    /**
     * Category repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Category service.
     */
    private ?TagServiceInterface $tagService;

    /**
     * Tags data transformer.
     */
    private ?TagsDataTransformer $dataTransformer;

    /**
     * Set up test.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->tagService = $container->get(TagService::class);
        $this->dataTransformer = $container->get(TagsDataTransformer::class);
    }


    /**
     * Test empty transform.
     */
    public function testEmptyDataTransformer(): void
    {
        // given
        $tags = new ArrayCollection();

        // when
        $result = $this->dataTransformer->transform($tags);

        // then
        $this->assertEquals('', $result);
    }

    /**
     * Test empty transform.
     */
    public function testNotEmptyDataTransformer(): void
    {
        // given
        $tag = new Tag();
        $tag->setTitle('tag-data-transformer');

        $this->tagService->save($tag);

        $tags = new ArrayCollection([$tag]);

        // when
        $result = $this->dataTransformer->transform($tags);

        // then
        $this->assertEquals('tag-data-transformer', $result);
    }

    /**
     * Test empty transform.
     */
    public function testNotEmptyDataTransformerReverse(): void
    {
        // given
        $tagString = 'tag-reverse';
        $tag = new Tag();
        $tag->setTitle('tag-reverse');

        $this->tagService->save($tag);

        $tags = [$tag];

        // when
        $result = $this->dataTransformer->reverseTransform($tagString);

        // then
        $this->assertEquals($tags[0]->getTitle(), $result[0]->getTitle());
    }
}
