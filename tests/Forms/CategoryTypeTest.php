<?php
/**
 * Category type test.
 */

namespace App\Tests\Forms;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Category type test.
 */
class CategoryTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $time = new \DateTimeImmutable('now');
        $formatData = [
            'title' => 'Category Type Test',
            'description' => 'Category Type description',
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Category();
        $form = $this->factory->create(CategoryType::class, $model);

        $expected = new Category();
        $expected->setTitle('Category Type Test');
        $expected->setDescription('Category Type description');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);
        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected->getTitle(), $model->getTitle());
        $this->assertEquals($expected->getId(), $model->getId());
    }

}
