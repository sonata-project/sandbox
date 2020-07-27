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
use Sonata\UserBundle\Model\User;

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

    public function getOrder(): int
    {
        return 5;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUser('admin', true, true);

        $secureUser = $this->createUser('secure', true, true);
        // google chart qr code : https://www.google.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/secure@http://demo.sonata-project.org%3Fsecret%3D4YU4QGYPB63HDN2C
        $secureUser->setTwoStepVerificationCode('4YU4QGYPB63HDN2C');
        $this->userManager->updateUser($secureUser);

        $this->addReference('user-admin', $secureUser);

        foreach (range(1, 20) as $id) {
            $this->createUser($this->faker->userName.$id);
        }

        $johndoeUser = $this->createUser('johndoe');
        $this->setReference('user-johndoe', $johndoeUser);

        // Behat testing purpose
        $this->createUser('behat_user');
        $this->createUser('behat_disabled', false);
    }

    protected function createUser(string $usernameAndPassword, bool $enabled = true, bool $isSuperAdmin = false): User
    {
        $user = $this->userManager->createUser();
        $user->setUsername($usernameAndPassword);
        $user->setEmail($this->faker->safeEmail);
        $user->setPlainPassword($usernameAndPassword);
        $user->setEnabled($enabled);
        $user->setSuperAdmin($isSuperAdmin);

        $this->userManager->updateUser($user);

        return $user;
    }
}
