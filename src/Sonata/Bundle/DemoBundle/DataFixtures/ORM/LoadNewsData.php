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
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;

class LoadNewsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return Generator
     */
    private $faker;

    /**
     * @var CollectionManagerInterface
     */
    private $collectionManager;

    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * @var \Sonata\FormatterBundle\Formatter\PoolInterface
     */
    private $formatterPool;

    /**
     * @var PostManagerInterface
     */
    private $postManager;

    /**
     * @var TagManagerInterface
     */
    private $tagManager;

    public function __construct(
        Generator $faker,
        CollectionManagerInterface $collectionManager,
        CommentManagerInterface $commentManager,
        Pool $formatterPool,
        PostManagerInterface $postManager,
        TagManagerInterface $tagManager
    ) {
        $this->faker = $faker;
        $this->collectionManager = $collectionManager;
        $this->commentManager = $commentManager;
        $this->formatterPool = $formatterPool;
        $this->postManager = $postManager;
        $this->tagManager = $tagManager;
    }

    public function getOrder()
    {
        return 6;
    }

    public function load(ObjectManager $manager)
    {
        //        $userManager = $this->getUserManager();
        $postManager = $this->postManager;

        $faker = $this->faker;

        $tags = [
            'symfony' => null,
            'form' => null,
            'general' => null,
            'web2' => null,
        ];

        foreach ($tags as $tagName => $null) {
            $tag = $this->tagManager->create();
            $tag->setEnabled(true);
            $tag->setName($tagName);

            $tags[$tagName] = $tag;
            $this->tagManager->save($tag);
        }

        $collection = $this->collectionManager->create();
        $collection->setEnabled(true);
        $collection->setName('General');
        $this->collectionManager->save($collection);

        foreach (range(1, 20) as $id) {
            $post = $this->postManager->create();
            $post->setAuthor($this->getReference('user-johndoe'));

            $post->setCollection($collection);
            $post->setAbstract($faker->sentence(30));
            $post->setEnabled(true);
            $post->setTitle($faker->sentence(6));
            $post->setPublicationDateStart($faker->dateTimeBetween('-30 days', '-1 days'));

            $id = $this->getReference('sonata-media-0')->getId();

            //TODO: fix raw
            $raw = '';
            /*
                        $raw = <<<RAW
            ### Gist Formatter

            Now a specific gist from github

            <% gist '1552362' 'gistfile1.txt' %>

            ### Media Formatter

            Load a media from a <code>SonataMediaBundle</code> with a specific format

            <% media $id, 'big' %>

            RAW
            ;
            */
            $raw .= sprintf("### %s\n\n%s\n\n### %s\n\n%s",
                $faker->sentence(random_int(3, 6)),
                $faker->text(1000),
                $faker->sentence(random_int(3, 6)),
                $faker->text(1000)
            );

            $post->setRawContent($raw);
            $post->setContentFormatter('markdown');

            $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
            $post->setCommentsDefaultStatus(CommentInterface::STATUS_VALID);

            foreach ($tags as $tag) {
                $post->addTags($tag);
            }

            foreach (range(1, $faker->randomDigit + 2) as $commentId) {
                $comment = $this->commentManager->create();
                $comment->setEmail($faker->email);
                $comment->setName($faker->name);
                $comment->setStatus(CommentInterface::STATUS_VALID);
                $comment->setMessage($faker->sentence(25));
                $comment->setUrl($faker->url);

                $post->addComments($comment);
            }

            $postManager->save($post);
        }
    }
}
