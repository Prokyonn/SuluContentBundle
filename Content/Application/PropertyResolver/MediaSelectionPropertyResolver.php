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

use Sulu\Bundle\ContentBundle\Content\Application\ContentObjects\ContentView;

class MediaSelectionPropertyResolver implements PropertyResolverInterface
{
    public function resolve(mixed $data, string $locale, array $params = []): ContentView
    {
        if (empty($data) || !\is_array($data) || !isset($data['ids'])) {
            return ContentView::create([], ['ids' => []]);
        }

        return ContentView::createResolvables(
            $data['ids'],
            $params['resourceLoader'] ?? $this->getDefaultResourceLoader(),
            ['ids' => $data['ids']],
        );
    }

    public static function getType(): string
    {
        return 'media_selection';
    }

    public function getDefaultResourceLoader(): string
    {
        return 'media';
    }
}
