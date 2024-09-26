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

namespace Sulu\Bundle\ContentBundle\Content\Application\PropertyResolver;

use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FieldMetadata;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Value\ContentView;
use Sulu\Bundle\ContentBundle\Content\Application\MetadataResolver\MetadataResolver;

class BlockPropertyResolver implements PropertyResolverInterface
{
    private MetadataResolver $metadataResolver;

    /**
     * Prevent circular dependency by injecting the MetadataResolver after instantiation.
     */
    public function setMetadataResolver(MetadataResolver $metadataResolver): void
    {
        $this->metadataResolver = $metadataResolver;
    }

    /**
     * @param array{
     *     type: string,
     *     ...
     * }[] $data
     */
    public function resolve(mixed $data, string $locale, array $params = []): ContentView
    {
        /** @var FieldMetadata $metadata */
        $metadata = $params['metadata'];
        $blockTypes = $metadata->getTypes();
        $contentViews = [];
        foreach ($data as $block) {
            $type = $block['type'];
            $formMetadata = $blockTypes[$type];
            $contentViews[] = [
                'type' => $type,
                ...$this->metadataResolver->resolveItems($formMetadata->getItems(), $block, $locale),
            ];
        }

        return ContentView::create($contentViews, []);
    }

    public static function getType(): string
    {
        return 'block';
    }
}
