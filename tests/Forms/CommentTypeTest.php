<?php
/**
 * Category type test.
 */

namespace App\Tests\Forms;

use App\Entity\Category;
use App\Entity\Comment;
use App\Form\Type\CategoryType;
use App\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Category type test.
 */
class CommentTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $time = new \DateTimeImmutable('now');
        $formatData = [
            'description' => 'Comment Type description',
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Comment();
        $form = $this->factory->create(CommentType::class, $model);

        $expected = new Comment();
        $expected->setDescription('Comment Type description');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);
        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected->getId(), $model->getId());
    }

}
