<?php
/**
 * ChangePasswordType test.
 */

namespace App\Tests\Forms;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\Type\BlockUserType;
use App\Form\Type\CategoryType;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * ChangePasswordType test.
 */
class BlockUserTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        $model = new User();
        $model->setUsername('blockeduser');
        $model->setPassword('password');
        $model->setEmail('block@example.com');
        $model->setBlocked(true);
        $form = $this->factory->create(BlockUserType::class);

        $form->submit([$model]);
        $this->assertTrue($form->isSynchronized());
    }

}
