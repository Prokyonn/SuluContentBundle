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

namespace Sulu\Bundle\ContentBundle\Content\Application\ContentResolver;

use Sulu\Bundle\ContentBundle\Content\Application\ContentObjects\ContentView;
use Sulu\Bundle\ContentBundle\Content\Application\ContentObjects\ResolvableResource;
use Sulu\Bundle\ContentBundle\Content\Application\ResourceLoader\ResourceLoaderProvider;
use Sulu\Bundle\ContentBundle\Content\Application\TemplateResolver\TemplateResolverInterface;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;

class ContentResolver implements ContentResolverInterface
{
    public function __construct(
        private TemplateResolverInterface $templateResolver,
        private ResourceLoaderProvider $resourceLoaderProvider
    ) {
    }

    public function resolve(DimensionContentInterface $dimensionContent): array
    {
        $contentViews = $this->templateResolver->resolve($dimensionContent);
        $result = $this->resolveContentViews($contentViews);
        $resources = $this->loadResolvableResources($result['resolvableResources'], $dimensionContent->getLocale());
        \array_walk_recursive($result['content'], function(&$value) use ($resources) {
            if ($value instanceof ResolvableResource) {
                $value = $resources[$value->getResourceLoaderKey()][$value->getId()];
            }
        });

        return [
            'resource' => $dimensionContent->getResource(),
            'content' => $result['content'],
            'view' => $result['view'],
        ];
    }

    /**
     * @param ContentView[]|mixed $contentViews
     *
     * @return mixed[]
     */
    private function resolveContentViews(array $contentViews): array
    {
        $content = [];
        $view = [];
        $resolvableResources = [];

        foreach ($contentViews as $name => $contentView) {
            $result = $this->resolveContentView($contentView, $name);
            $content = \array_merge($content, $result['content']);
            $view = \array_merge($view, $result['view']);
            $resolvableResources = \array_merge_recursive($resolvableResources, $result['resolvableResources']);
        }

        return [
            'content' => $content,
            'view' => $view,
            'resolvableResources' => $resolvableResources,
        ];
    }

    /**
     * @param array<string, int|string> $resolvableResourceIds
     */
    private function loadResolvableResources(array $resolvableResourceIds, string $locale): array
    {
        $resources = [];
        foreach ($resolvableResourceIds as $resourceLoaderKey => $ids) {
            $resourceLoader = $this->resourceLoaderProvider->getResourceLoader($resourceLoaderKey);
            if (!$resourceLoader) {
                throw new \RuntimeException(\sprintf('ResourceLoader with key "%s" not found', $resourceLoaderKey));
            }

            $resources[$resourceLoaderKey] = $resourceLoader->load($ids, $locale);
        }

        return $resources;
    }

    private function resolveContentView(mixed $contentView, string $name): array
    {
        $resolvableResources = [];
        $content[$name] = $contentView->getContent();
        $view[$name] = $contentView->getView();

        if (\is_array($content[$name])) {
            foreach ($content[$name] as $index => $value) {
                $contentViewValues = [];
                $otherValues = [];

                if (\is_array($value)) {
                    foreach ($value as $key => $entry) {
                        if ($entry instanceof ResolvableResource) {
                            $resolvableResources[$entry->getResourceLoaderKey()][] = $entry->getId();
                        }

                        match (true) {
                            $entry instanceof ContentView => $contentViewValues[$key] = $entry,
                            default => $otherValues[$key] = $entry,
                        };
                    }

                    $resolvedContentViews = $this->resolveContentViews($contentViewValues);
                    $result['content'] = \array_merge(
                        $resolvedContentViews['content'],
                        $otherValues,
                    );
                    $result['view'] = \array_merge(
                        $resolvedContentViews['view'],
                    );

                    $resolvableResources = \array_merge_recursive($resolvableResources, $resolvedContentViews['resolvableResources']);
                } else {
                    if ($value instanceof ResolvableResource) {
                        $resolvableResources[$value->getResourceLoaderKey()][] = $value->getId();
                    }

                    if ($value instanceof ContentView) {
                        $result = $this->resolveContentView($value, $name);
                    } else {
                        $result['content'] = $value;
                        $result['view'] = [];
                    }
                }

                $content[$name][$index] = $result['content'];
                $view[$name][$index] = $result['view'];
            }
        }

        return [
            'content' => $content,
            'view' => $view,
            'resolvableResources' => $resolvableResources,
        ];
    }
}
