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

namespace Sulu\Bundle\ContentBundle\Tests\Unit\Content\Application\ContentResolver\Value;

use PHPUnit\Framework\TestCase;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Value\ContentView;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Value\ResolvableResource;

class ContentViewTest extends TestCase
{
    public function testCreate(): void
    {
        $contentView = ContentView::create('content', ['view' => 'data']);

        $this->assertSame('content', $contentView->getContent());
        $this->assertSame(['view' => 'data'], $contentView->getView());
    }

    public function testCreateResolvable(): void
    {
        $contentView = ContentView::createResolvable(5, 'resourceLoaderKey', ['view' => 'data']);

        $this->assertSame([new ResolvableResource(5, 'resourceLoaderKey')], $contentView->getContent());
        $this->assertSame(['view' => 'data'], $contentView->getView());
    }

    public function testCreateResolvables(): void
    {
        $contentView = ContentView::createResolvables([5, 6], 'resourceLoaderKey', ['view' => 'data']);

        $this->assertSame([new ResolvableResource(5, 'resourceLoaderKey'), new ResolvableResource(6, 'resourceLoaderKey')], $contentView->getContent());
        $this->assertSame(['view' => 'data'], $contentView->getView());
    }

    public function testGetContent(): void
    {
        $contentView = ContentView::create('content', ['view' => 'data']);

        $this->assertSame('content', $contentView->getContent());
    }

    public function testGetView(): void
    {
        $contentView = ContentView::create('content', ['view' => 'data']);

        $this->assertSame(['view' => 'data'], $contentView->getView());
    }
}
