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

namespace Sulu\Bundle\ContentBundle\Content\Application\MetadataResolver;

use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\ItemMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\SectionMetadata;
use Sulu\Bundle\ContentBundle\Content\Application\ContentObjects\ContentView;
use Sulu\Bundle\ContentBundle\Content\Application\PropertyResolver\PropertyResolverProvider;

class MetadataResolver
{
    public function __construct(
        private PropertyResolverProvider $propertyResolverProvider
    ) {
    }

    /**
     * @param ItemMetadata[] $items
     *
     * @return ContentView[]
     */
    public function resolveItems(array $items, array $data, string $locale): array
    {
        $contentViews = [];
        foreach ($items as $item) {
            $name = $item->getName();
            $type = $item->getType();
            if ($item instanceof SectionMetadata || $item instanceof FormMetadata) {
                $contentViews = \array_merge(
                    $contentViews,
                    $this->resolveItems($item->getItems(), $data, $locale)
                );
            } else {
                $contentViews[$name] = $this->resolveProperty($type, $data[$name], $locale, ['metadata' => $item]);
            }
        }

        return $contentViews;
    }

    private function resolveProperty(string $type, mixed $data, string $locale, array $params = []): ContentView
    {
        $propertyResolver = $this->propertyResolverProvider->getPropertyResolver($type);

        return $propertyResolver->resolve($data, $locale, $params);
    }
}
