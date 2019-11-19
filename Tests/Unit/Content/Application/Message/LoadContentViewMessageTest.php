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

namespace Sulu\Bundle\ContentBundle\Tests\Unit\Content\Application\Message;

use PHPUnit\Framework\TestCase;
use Sulu\Bundle\ContentBundle\Content\Application\Message\LoadContentViewMessage;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\AbstractContent;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentDimensionInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\ContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionInterface;

class LoadContentViewMessageTest extends TestCase
{
    /**
     * @param mixed[] $dimensionAttributes
     */
    protected function createLoadContentViewMessageInstance(
        ContentInterface $content,
        array $dimensionAttributes
    ): LoadContentViewMessage {
        return new LoadContentViewMessage($content, $dimensionAttributes);
    }

    protected function createContentInstance(): ContentInterface
    {
        return new class() extends AbstractContent {
            public static function getResourceKey(): string
            {
                return 'example';
            }

            public function createDimension(DimensionInterface $dimension): ContentDimensionInterface
            {
                throw new \RuntimeException('Should not be called');
            }

            public function getId()
            {
                return null;
            }
        };
    }

    public function testGetContent(): void
    {
        $content = $this->createContentInstance();
        $createContentMessage = $this->createLoadContentViewMessageInstance($content, []);

        $this->assertSame($content, $createContentMessage->getContent());
    }

    public function testGetDimensionAttributes(): void
    {
        $content = $this->createContentInstance();
        $createContentMessage = $this->createLoadContentViewMessageInstance($content, [
            'locale' => 'de',
        ]);

        $this->assertSame([
            'locale' => 'de',
        ], $createContentMessage->getDimensionAttributes());
    }
}