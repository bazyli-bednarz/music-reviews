<?php
///**
// * Album type test.
// */
//
//namespace App\Tests\Forms;
//
//use App\Entity\Album;
//use App\Entity\Category;
//use App\Form\Type\AlbumType;
//use App\Form\Type\CategoryType;
//use Symfony\Component\Form\Test\TypeTestCase;
//
///**
// * Album type test class.
// */
//class AlbumTypeTest extends TypeTestCase
//{
//    public function testSubmitValidData()
//    {
//        $time = new \DateTimeImmutable('now');
//        $category = new Category();
//        $category->setTitle('Test album category');
//        $category->setDescription('Test album category description');
//        $formatData = [
//            'title' => 'Album Type Test',
//            'description' => 'Category Type description',
//            'category' => $category,
//            'createdAt' => $time,
//            'updatedAt' => $time,
//        ];
//
//        $model = new Album();
//        $form = $this->factory->create(AlbumType::class, $model);
//
//        $expected = new Album();
//        $expected->setTitle('Album Type Test');
//        $expected->setDescription('Album Type description');
//        $expected->setCreatedAt($time);
//        $expected->setUpdatedAt($time);
//        $expected->setCategory($category);
//        $form->submit($formatData);
//        $this->assertTrue($form->isSynchronized());
//
//        $this->assertEquals($expected->getTitle(), $model->getTitle());
//        $this->assertEquals($expected->getId(), $model->getId());
//    }
//
//}
