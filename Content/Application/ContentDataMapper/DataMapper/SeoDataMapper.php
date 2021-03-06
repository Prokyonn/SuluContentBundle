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

namespace Sulu\Bundle\ContentBundle\Content\Application\ContentDataMapper\DataMapper;

use Sulu\Bundle\ContentBundle\Content\Domain\Model\SeoInterface;

class SeoDataMapper implements DataMapperInterface
{
    public function map(
        array $data,
        object $unlocalizedObject,
        ?object $localizedObject = null
    ): void {
        if (!$unlocalizedObject instanceof SeoInterface) {
            return;
        }

        if ($localizedObject) {
            if (!$localizedObject instanceof SeoInterface) {
                throw new \RuntimeException(sprintf('Expected "$localizedObject" from type "%s" but "%s" given.', SeoInterface::class, \get_class($localizedObject)));
            }

            $this->setSeoData($localizedObject, $data);

            return;
        }

        $this->setSeoData($unlocalizedObject, $data);
    }

    /**
     * @param mixed[] $data
     */
    private function setSeoData(SeoInterface $dimensionContent, array $data): void
    {
        $dimensionContent->setSeoTitle($data['seoTitle'] ?? null);
        $dimensionContent->setSeoDescription($data['seoDescription'] ?? null);
        $dimensionContent->setSeoKeywords($data['seoKeywords'] ?? null);
        $dimensionContent->setSeoCanonicalUrl($data['seoCanonicalUrl'] ?? null);
        $dimensionContent->setSeoHideInSitemap($data['seoHideInSitemap'] ?? false);
        $dimensionContent->setSeoNoFollow($data['seoNoFollow'] ?? false);
        $dimensionContent->setSeoNoIndex($data['seoNoIndex'] ?? false);
    }
}
