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

namespace Sulu\Bundle\ContentBundle\Content\Application\ResourceLoader;

interface ResourceLoaderInterface
{
    /**
     * @return array<int|string, object> index must be the ID of the object
     */
    public function load(array $ids, string $locale, array $params = []): array;

    public function getType(): string;
}
