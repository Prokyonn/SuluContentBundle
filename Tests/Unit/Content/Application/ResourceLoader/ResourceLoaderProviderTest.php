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

namespace Sulu\Bundle\ContentBundle\Tests\Unit\Content\Application\ResourceLoader;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Sulu\Bundle\ContentBundle\Content\Application\ResourceLoader\MediaResourceLoader;
use Sulu\Bundle\ContentBundle\Content\Application\ResourceLoader\ResourceLoaderInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ResourceLoader\ResourceLoaderProvider;

class ResourceLoaderProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testGetResourceLoader(): void
    {
        $mediaResourceLoader = $this->prophesize(MediaResourceLoader::class);
        $categoryResourceLoader = $this->prophesize(ResourceLoaderInterface::class);
        $resourceLoaderProvider = new ResourceLoaderProvider(
            [
                'media' => $mediaResourceLoader->reveal(),
                'category' => $categoryResourceLoader->reveal(),
            ]
        );

        self::assertSame($mediaResourceLoader->reveal(), $resourceLoaderProvider->getResourceLoader('media'));
        self::assertSame($categoryResourceLoader->reveal(), $resourceLoaderProvider->getResourceLoader('category'));
        self::assertNull($resourceLoaderProvider->getResourceLoader('invalid'));
    }
}
