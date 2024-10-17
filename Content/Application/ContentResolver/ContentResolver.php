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

use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Resolver\ResolverInterface;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Resolver\TemplateResolver;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Value\ContentView;
use Sulu\Bundle\ContentBundle\Content\Application\ContentResolver\Value\ResolvableResource;
use Sulu\Bundle\ContentBundle\Content\Application\ResourceLoader\ResourceLoaderProvider;
use Sulu\Bundle\ContentBundle\Content\Domain\Model\DimensionContentInterface;

class ContentResolver implements ContentResolverInterface
{
    /**
     * @param iterable<ResolverInterface> $contentResolvers
     */
    public function __construct(
        private iterable $contentResolvers,
        private ResourceLoaderProvider $resourceLoaderProvider
    ) {
    }

    /**
     * @return array{
     *      resource: object,
     *      content: mixed,
     *      view: mixed[]
     *  }
     */
    public function resolve(DimensionContentInterface $dimensionContent): array
    {
        $contentViews = [];
        foreach ($this->contentResolvers as $key => $contentResolver) {
            $contentView = $contentResolver->resolve($dimensionContent);

            if ($contentResolver instanceof TemplateResolver) {
                /** @var mixed[] $content */
                $content = $contentView->getContent();
                $contentViews = \array_merge($contentViews, $content);
                continue;
            }

            $contentViews[$key] = $contentView;
        }

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
     * @param ContentView[] $contentViews
     *
     * @return array{
     *     content: mixed[],
     *     view: mixed[],
     *     resolvableResources: array<string, array<int|string>>
     *     }
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
     * @param array<string, array<int|string>> $resolvableResourceIds
     *
     * @return array<string, mixed[]>
     */
    private function loadResolvableResources(array $resolvableResourceIds, ?string $locale): array
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

    /**
     * @return array{
     *     content: mixed[],
     *     view: mixed[],
     *     resolvableResources: array<string, array<int|string>>
     *     }
     */
    private function resolveContentView(ContentView $contentView, string $name): array
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

                    $content[$name][$index] = $result['content'];
                    $view[$name][$index] = $result['view'];
                    continue;
                }

                if ($value instanceof ResolvableResource) {
                    $resolvableResources[$value->getResourceLoaderKey()][] = $value->getId();
                }

                $result = $value instanceof ContentView ?
                    $this->resolveContentView($value, $index) :
                    [
                        'content' => $value,
                        'view' => [],
                    ];

                // TODO this has to be refactored
                $content[$name] = \array_merge($content[$name], \is_array($result['content']) ? $result['content'] : [$index => $result['content']]); // @phpstan-ignore-line
                $view[$name] = \array_merge($view[$name], $result['view']);
            }
        }

        return [
            'content' => $content,
            'view' => $view,
            'resolvableResources' => $resolvableResources,
        ];
    }
}
