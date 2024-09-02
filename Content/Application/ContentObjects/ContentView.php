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

namespace Sulu\Bundle\ContentBundle\Content\Application\ContentObjects;

class ContentView
{
    /**
     * @param mixed[] $content
     * @param mixed[] $view
     */
    private function __construct(
        private mixed $content,
        private array $view
    ) {
    }

    public static function create(mixed $content, array $view): self
    {
        return new self($content, $view);
    }

    public static function createResolvable(string $id, string $resourceLoaderKey, array $view): self
    {
        $resolvableResources = [
            new ResolvableResource($id, $resourceLoaderKey),
        ];

        return new self($resolvableResources, $view);
    }

    public static function createResolvables(array $ids, string $resourceLoaderKey, array $view): self
    {
        $resolvableResources = [];

        foreach ($ids as $id) {
            $resolvableResources[] = new ResolvableResource($id, $resourceLoaderKey);
        }

        return new self($resolvableResources, $view);
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function getView(): array
    {
        return $this->view;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setView(array $view): self
    {
        $this->view = $view;

        return $this;
    }
}
