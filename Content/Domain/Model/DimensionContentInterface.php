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

namespace Sulu\Bundle\ContentBundle\Content\Domain\Model;

interface DimensionContentInterface
{
    public static function getResourceKey(): string;

    public function getDimension(): DimensionInterface;

    public function getResource(): ContentRichEntityInterface;

    public function isMerged(): bool;

    public function markAsMerged(): void;
}
