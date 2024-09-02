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

namespace Sulu\Bundle\ContentBundle\Content\Application\TemplateResolver;

use Sulu\Bundle\ContentBundle\Content\Application\ContentObjects\ContentView;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;

interface TemplateResolverInterface
{
    /**
     * @return ContentView[]
     */
    public function resolve(DimensionContentInterface $dimensionContent): array;
}
