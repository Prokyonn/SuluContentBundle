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

class DefaultPropertyResolver implements PropertyResolverInterface
{
    public function resolve(mixed $data, string $locale, array $params = []): ContentView
    {
        return ContentView::create($data, []);
    }

    public static function getType(): string
    {
        return 'default';
    }

    public function getDefaultResourceLoader(): ?string
    {
        return null;
    }
}
