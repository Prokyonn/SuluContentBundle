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

namespace Sulu\Bundle\ContentBundle\Content\Application\ContentObjects;

class ResolvableResource
{
    public function __construct(
        private string $id,
        private string $resourceLoaderKey,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getResourceLoaderKey(): string
    {
        return $this->resourceLoaderKey;
    }
}
