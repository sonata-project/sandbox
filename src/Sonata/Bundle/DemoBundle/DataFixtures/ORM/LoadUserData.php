<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\Bundle\DemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    function getOrder()
    {
        return 1;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $manager = $this->getUserManager();
        $faker = $this->getFaker();

        $user = $manager->createUser();
        $user->setUsername('admin');
        $user->setEmail($faker->safeEmail);
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        $user->setLocked(false);

        $manager->updateUser($user);

        $user = $manager->createUser();
        $user->setUsername('secure');
        $user->setEmail($faker->safeEmail);
        $user->setPlainPassword('secure');
        $user->setEnabled(true);
        $user->setSuperAdmin(true);
        $user->setLocked(false);
        // google chart qr code : https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/secure@http://demo.sonata-project.org%3Fsecret%3D4YU4QGYPB63HDN2C
        $user->setTwoStepVerificationCode('4YU4QGYPB63HDN2C');

        $manager->updateUser($user);

        $this->addReference('user-admin', $user);

        foreach (range(1, 20) as $id) {
            $user = $manager->createUser();
            $user->setUsername($faker->userName . $id);
            $user->setEmail($faker->safeEmail);
            $user->setPlainPassword($faker->randomNumber());
            $user->setEnabled(true);
            $user->setLocked(false);

            $manager->updateUser($user);
        }

        $user = $manager->createUser();
        $user->setUsername('johndoe');
        $user->setEmail($faker->safeEmail);
        $user->setPlainPassword('johndoe');
        $user->setEnabled(true);
        $user->setSuperAdmin(false);
        $user->setLocked(false);

        $this->setReference('user-johndoe', $user);

        $manager->updateUser($user);

        // Behat testing purpose
        $user = $manager->createUser();
        $user->setUsername('behat_user');
        $user->setEmail($faker->safeEmail);
        $user->setEnabled(true);
        $user->setPlainPassword('behat_user');

        $manager->updateUser($user);
    }

    /**
     * @return \FOS\UserBundle\Model\UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }
}
