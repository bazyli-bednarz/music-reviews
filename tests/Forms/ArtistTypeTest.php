<?php
/**
 * Artist type test.
 */

namespace App\Tests\Forms;

use App\Entity\Artist;
use App\Form\Type\ArtistType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Artist type test.
 */
class ArtistTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $time = new \DateTimeImmutable('now');
        $formatData = [
            'name' => 'Artist Type Test',
            'description' => 'Artist Type description',
            'createdAt' => $time,
            'updatedAt' => $time,
        ];

        $model = new Artist();
        $form = $this->factory->create(ArtistType::class, $model);

        $expected = new Artist();
        $expected->setName('Artist Type Test');
        $expected->setDescription('Artist Type description');
        $expected->setCreatedAt($time);
        $expected->setUpdatedAt($time);
        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals($expected->getName(), $model->getName());
        $this->assertEquals($expected->getId(), $model->getId());
    }

}
