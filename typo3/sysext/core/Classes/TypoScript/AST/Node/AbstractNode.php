<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Core\TypoScript\AST\Node;

use TYPO3\CMS\Core\TypoScript\Tokenizer\Token\TokenStreamInterface;

/**
 * Generic node. Implements common methods of NodeInterface used
 * in all Node classes.
 *
 * @internal: Internal AST structure.
 */
abstract class AbstractNode implements NodeInterface
{
    protected string $name;
    private ?string $value = null;
    private ?string $previousValue = null;

    /**
     * @var array<string, ChildNodeInterface>
     */
    protected array $children = [];
    private bool $expanded = false;
    private ?TokenStreamInterface $originalValueTokenStream = null;
    private bool $searchMatchInName = false;
    private bool $searchMatchInValue = false;
    private array $comments = [];

    /**
     * When storing to cache, we only store FE relevant properties and skip
     * various BE related properties which then default to class defaults when
     * unserialized. This is done to create smaller php cache files.
     */
    final public function __serialize(): array
    {
        return $this->serialize();
    }

    protected function serialize(): array
    {
        $result = [
            'name' => $this->name,
            'children' => $this->children,
        ];
        if ($this->value !== null) {
            $result['value'] = $this->value;
        }
        return $result;
    }

    /**
     * This forces $this->name NOT to be readonly.
     * Used with '<' operator on tree root to copy:
     *      foo = value
     *      bar < foo
     * The 'foo' object node is copied, but added to AST as name 'bar'
     */
    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function addChild(ChildNodeInterface $node): void
    {
        $this->children[$node->getName()] = $node;
    }

    public function getChildByName(string $name): ?ChildNodeInterface
    {
        return $this->children[$name] ?? null;
    }

    /**
     * Note this does *not* choke if that child does not exist, so we can "blindly" remove without error.
     */
    public function removeChildByName(string $name): void
    {
        unset($this->children[$name]);
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function getNextChild(): iterable
    {
        foreach ($this->children as $child) {
            yield $child;
        }
    }

    public function sortChildren(): void
    {
        ksort($this->children);
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function appendValue(string $value): void
    {
        $this->value .= $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function isValueNull(): bool
    {
        return $this->value === null;
    }

    public function setPreviousValue(?string $value): void
    {
        $this->previousValue = $value;
    }

    public function getPreviousValue(): ?string
    {
        return $this->previousValue;
    }

    public function setOriginalValueTokenStream(?TokenStreamInterface $tokenStream): void
    {
        $this->originalValueTokenStream = $tokenStream;
    }

    public function getOriginalValueTokenStream(): ?TokenStreamInterface
    {
        return $this->originalValueTokenStream;
    }

    public function setExpanded(bool $expanded): void
    {
        $this->expanded = $expanded;
    }

    public function isExpanded(): bool
    {
        return $this->expanded;
    }

    public function setSearchMatchInName(): void
    {
        $this->searchMatchInName = true;
    }

    public function hasSearchMatchInName(): bool
    {
        return $this->searchMatchInName;
    }

    public function setSearchMatchInValue(): void
    {
        $this->searchMatchInValue = true;
    }

    public function hasSearchMatchInValue(): bool
    {
        return $this->searchMatchInValue;
    }

    public function addComment(TokenStreamInterface $tokenStream): void
    {
        $this->comments[] = $tokenStream;
    }

    /**
     * @return TokenStreamInterface[]
     */
    public function getComments(): array
    {
        return $this->comments;
    }
}
