<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use FOS\UserBundle\Model\UserManagerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(Generator $faker, UserManagerInterface $userManager)
    {
        $this->faker = $faker;
        $this->userManager = $userManager;
    }

    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager)
    {
        $user = $this->userManager->createUser();
        $user->setUsername('admin');
        $user->setEmail($this->faker->safeEmail);
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);

        $this->userManager->updateUser($user);

        $user = $this->userManager->createUser();
        $user->setUsername('secure');
        $user->setEmail($this->faker->safeEmail);
        $user->setPlainPassword('secure');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        // google chart qr code : https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/secure@http://demo.sonata-project.org%3Fsecret%3D4YU4QGYPB63HDN2C
        $user->setTwoStepVerificationCode('4YU4QGYPB63HDN2C');

        $this->userManager->updateUser($user);

        $this->addReference('user-admin', $user);

        foreach (range(1, 20) as $id) {
            $user = $this->userManager->createUser();
            $user->setUsername($this->faker->userName.$id);
            $user->setEmail($this->faker->safeEmail);
            $user->setPlainPassword($this->faker->randomNumber());
            $user->setEnabled(true);

            $this->userManager->updateUser($user);
        }

        $user = $this->userManager->createUser();
        $user->setUsername('johndoe');
        $user->setEmail($this->faker->safeEmail);
        $user->setPlainPassword('johndoe');
        $user->setEnabled(true);
        $user->setSuperAdmin(false);

        $this->setReference('user-johndoe', $user);

        $this->userManager->updateUser($user);

        // Behat testing purpose
        $user = $this->userManager->createUser();
        $user->setUsername('behat_user');
        $user->setEmail($this->faker->safeEmail);
        $user->setEnabled(true);
        $user->setPlainPassword('behat_user');

        $this->userManager->updateUser($user);
    }
}
