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

class ResourceLoaderProvider
{
    /**
     * @var ResourceLoaderInterface[]
     */
    private array $resourceLoaders;

    public function __construct(\Traversable $resourceLoaders)
    {
        $this->resourceLoaders = \iterator_to_array($resourceLoaders);
    }

    public function getResourceLoader(string $type): ?ResourceLoaderInterface
    {
        if (!\array_key_exists($type, $this->resourceLoaders)) {
            return null;
        }

        return $this->resourceLoaders[$type];
    }
}
