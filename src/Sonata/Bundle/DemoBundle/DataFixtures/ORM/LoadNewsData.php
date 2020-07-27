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
use Sonata\ClassificationBundle\Model\CollectionManagerInterface;
use Sonata\ClassificationBundle\Model\TagManagerInterface;
use Sonata\FormatterBundle\Formatter\Pool;
use Sonata\FormatterBundle\Formatter\PoolInterface;
use Sonata\NewsBundle\Model\CommentInterface;
use Sonata\NewsBundle\Model\CommentManagerInterface;
use Sonata\NewsBundle\Model\PostManagerInterface;
use Twig\Environment;

class LoadNewsData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var Generator
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
     * @var PoolInterface
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

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        Generator $faker,
        CollectionManagerInterface $collectionManager,
        CommentManagerInterface $commentManager,
        Pool $formatterPool,
        PostManagerInterface $postManager,
        TagManagerInterface $tagManager,
        Environment $twig
    ) {
        $this->faker = $faker;
        $this->collectionManager = $collectionManager;
        $this->commentManager = $commentManager;
        $this->formatterPool = $formatterPool;
        $this->postManager = $postManager;
        $this->tagManager = $tagManager;
        $this->twig = $twig;
    }

    public function getOrder(): int
    {
        return 6;
    }

    public function load(ObjectManager $manager): void
    {
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
            $post->setAbstract($this->faker->sentence(30));
            $post->setEnabled(true);
            $post->setTitle($this->faker->sentence(6));
            $post->setPublicationDateStart($this->faker->dateTimeBetween('-30 days', '-1 days'));

            $id = $this->getReference('sonata-media-0')->getId();

            $raw = $this->twig->render('@SonataDemo/fixtures/news_gist_formatter.md.twig', ['id' => $id]);

            $raw .= sprintf("### %s\n\n%s\n\n### %s\n\n%s",
                $this->faker->sentence(random_int(3, 6)),
                $this->faker->text(1000),
                $this->faker->sentence(random_int(3, 6)),
                $this->faker->text(1000)
            );

            $post->setRawContent($raw);
            $post->setContentFormatter('markdown');

            $post->setContent($this->formatterPool->transform($post->getContentFormatter(), $post->getRawContent()));
            $post->setCommentsDefaultStatus(CommentInterface::STATUS_VALID);

            foreach ($tags as $tag) {
                $post->addTags($tag);
            }

            foreach (range(1, $this->faker->randomDigit + 2) as $commentId) {
                $comment = $this->commentManager->create();
                $comment->setEmail($this->faker->email);
                $comment->setName($this->faker->name);
                $comment->setStatus(CommentInterface::STATUS_VALID);
                $comment->setMessage($this->faker->sentence(25));
                $comment->setUrl($this->faker->url);

                $post->addComments($comment);
            }

            $this->postManager->save($post);
        }
    }
}
