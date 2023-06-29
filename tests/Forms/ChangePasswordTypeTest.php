<?php
/**
 * ChangePasswordType test.
 */

namespace App\Tests\Forms;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\Type\CategoryType;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * ChangePasswordType test.
 */
class ChangePasswordTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {


        $formatData = [
            'oldPassword' => 'password',
            'password[first]' => 'passwordNew',
            'password[second]' => 'passwordNew',
        ];

        $model = new User();
        $model->setUsername('changepassworduser');
        $model->setPassword('password');
        $model->setEmail('changepassworduser@example.com');
        $form = $this->factory->create(ChangePasswordType::class);

        $form->submit($formatData);
        $this->assertTrue($form->isSynchronized());
    }

}
