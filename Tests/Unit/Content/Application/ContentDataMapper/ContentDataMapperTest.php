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

namespace Sulu\Bundle\ContentBundle\Tests\Unit\Content\Application\ContentDataMapper;

use PHPUnit\Framework\TestCase;
use Sulu\Bundle\ContentBundle\Content\Application\ContentDataMapper\ContentDataMapper;
use Sulu\Bundle\ContentBundle\Content\Application\ContentDataMapper\ContentDataMapperInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentDataMapper\DataMapper\DataMapperInterface;

class ContentDataMapperTest extends TestCase
{
    /**
     * @param iterable<DataMapperInterface> $dataMappers
     */
    protected function createContentDataMapperInstance(
        iterable $dataMappers
    ): ContentDataMapperInterface {
        return new ContentDataMapper($dataMappers);
    }

    public function testMap(): void
    {
        $dataMapper1 = $this->prophesize(DataMapperInterface::class);
        $dataMapper2 = $this->prophesize(DataMapperInterface::class);

        $contentDataMapper = $this->createContentDataMapperInstance([
            $dataMapper1->reveal(),
            $dataMapper2->reveal(),
        ]);

        $data = ['test-key' => 'test-value'];
        $unlocalizedObject = $this->prophesize(\stdClass::class);
        $localizedObject = $this->prophesize(\stdClass::class);

        $dataMapper1->map($data, $unlocalizedObject->reveal(), $localizedObject->reveal())->shouldBeCalled();
        $dataMapper2->map($data, $unlocalizedObject->reveal(), $localizedObject->reveal())->shouldBeCalled();

        $contentDataMapper->map($data, $unlocalizedObject, $localizedObject);
    }

    public function testMapWithoutLocalizedObject(): void
    {
        $dataMapper1 = $this->prophesize(DataMapperInterface::class);
        $dataMapper2 = $this->prophesize(DataMapperInterface::class);

        $contentDataMapper = $this->createContentDataMapperInstance([
            $dataMapper1->reveal(),
            $dataMapper2->reveal(),
        ]);

        $data = ['test-key' => 'test-value'];
        $unlocalizedObject = $this->prophesize(\stdClass::class);

        $dataMapper1->map($data, $unlocalizedObject->reveal(), null)->shouldBeCalled();
        $dataMapper2->map($data, $unlocalizedObject->reveal(), null)->shouldBeCalled();

        $contentDataMapper->map($data, $unlocalizedObject);
    }
}
