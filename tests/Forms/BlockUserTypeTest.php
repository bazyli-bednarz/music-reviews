<?php
///**
// * Block user type test.
// */
//
//namespace App\Tests\Forms;
//
//use App\Entity\Category;
//use App\Form\Type\BlockUserType;
//use App\Form\Type\CategoryType;
//use Symfony\Component\Form\Test\TypeTestCase;
//
///**
// * Block user type test.
// */
//class BlockUserTypeTest extends TypeTestCase
//{
//
//    public function testSubmitValidData()
//    {
//        $time = new \DateTimeImmutable('now');
//        $formatData = [];
//
//        $model = new BlockUserType();
//        $form = $this->factory->create(BlockUserType::class, $model);
//
//        $expected = new BlockUserType();
//
//        $form->submit($formatData);
//        $this->assertTrue($form->isSynchronized());
//
//        $this->assertEquals($expected, $model);
//    }
//
//}
