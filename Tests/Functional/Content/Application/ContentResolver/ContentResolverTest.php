<?php

declare(strict_types=1);

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\ContentBundle\Tests\Functional\Content\Application\ContentResolver;

use Sulu\Bundle\ContentBundle\Content\Application\ContentAggregator\ContentAggregatorInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\ContentResolverInterface;
use Sulu\Bundle\ContentBundle\Tests\Functional\Traits\CreateCategoryTrait;
use Sulu\Bundle\ContentBundle\Tests\Traits\CreateExampleTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;

class ContentResolverTest extends SuluTestCase
{
    use CreateCategoryTrait;
    use CreateExampleTrait;

    private ContentResolverInterface $contentResolver;
    private ContentAggregatorInterface $contentAggregator;

    protected function setUp(): void
    {
        self::purgeDatabase();
        self::initPhpcr();

        $this->contentResolver = self::getContainer()->get('sulu_content.content_resolver');
        $this->contentAggregator = self::getContainer()->get('sulu_content.content_aggregator');
    }

    public function testResolveContent(): void
    {
        $category = static::createCategory(['key' => 'category-1']);
        self::getEntityManager()->flush();

        $example1 = static::createExample(
            [
                'en' => [
                    'live' => [
                        'title' => 'example-1',
                        'description' => 'text-area-1',
                        'article' => 'editor-1',
                        'blocks' => [
                            [
                                'type' => 'heading',
                                'heading' => 'heading-1',
                            ],
                            [
                                'type' => 'text',
                                'text' => 'text-1',
                            ],
                        ],
                        'excerptTitle' => 'excerpt-title-1',
                        'excerptDescription' => 'excerpt-description-1',
                        'excerptCategories' => [$category->getId()],
                    ],
                ],
            ],
            [
                'create_route' => true,
            ]
        );

        static::getEntityManager()->flush();

        $dimensionContent = $this->contentAggregator->aggregate($example1, ['locale' => 'en', 'stage' => 'live']);
        /** @var mixed[] $result */
        $result = $this->contentResolver->resolve($dimensionContent);

        /** @var mixed[] $content */
        $content = $result['content'];

        self::assertSame('example-1', $content['title']);
        self::assertSame('text-area-1', $content['description']);
        self::assertSame('editor-1', $content['article']);
        self::assertCount(2, $content['blocks']);
        self::assertSame('heading-1', $content['blocks'][0]['heading']);
        self::assertSame('heading', $content['blocks'][0]['type']);
        self::assertSame('text-1', $content['blocks'][1]['text']);
        self::assertSame('text', $content['blocks'][1]['type']);

        // TODO add tests for categories / tags / images / ...
        self::assertNull($content['image']);

        // TODO add excerpt / seo test
    }
}
