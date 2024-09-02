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

use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\StructureFormMetadataLoader;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\TypedFormMetadata;
use Sulu\Bundle\ContentBundle\Content\Application\MetadataResolver\MetadataResolver;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\TemplateInterface;

class TemplateResolver implements TemplateResolverInterface
{
    public function __construct(
        private StructureFormMetadataLoader $structureFormMetadataLoader,
        private MetadataResolver $metadataResolver
    ) {
    }

    public function resolve(DimensionContentInterface $dimensionContent): array
    {
        if (!$dimensionContent instanceof TemplateInterface) {
            // what to do here?
            throw new \Exception('DimensionContent is not a Template');
        }

        $locale = $dimensionContent->getLocale();
        $templateKey = $dimensionContent->getTemplateKey();
        $templateType = $dimensionContent->getTemplateType();

        /** @var TypedFormMetadata $typedFormMetadata */
        $typedFormMetadata = $this->structureFormMetadataLoader->getMetadata($templateType, $locale);
        $formMetadata = $typedFormMetadata->getForms()[$templateKey] ?? throw new \Exception('Template not found');

        return $this->metadataResolver->resolveItems($formMetadata->getItems(), $dimensionContent->getTemplateData(), $locale);
    }
}
