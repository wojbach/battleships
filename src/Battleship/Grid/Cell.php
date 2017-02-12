<?php
declare(strict_types = 1);

namespace Battleship\Grid;


use Battleship\Ships\Ship;
use InvalidArgumentException;

class Cell
{
    public const STATE_FREE = 0;
    public const STATE_BUFFER = 1;
    public const STATE_TAKEN = 2;

    private $state;
    private $item;

    /**
     * @var bool
     */
    private $wasShoot = false;

    /**
     * @var Item[]
     */
    private $bufferOf = [];

    /**
     * Cell constructor.
     * @param int $state
     * @param Item|null $item
     * @param array|null $bufferOf
     * @throws \InvalidArgumentException
     */
    public function __construct(int $state = self::STATE_FREE, Item $item = null, array $bufferOf = [])
    {

        $this->setState($state);
        $this->setItem($item);
        $this->setBufferOf($bufferOf);
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @throws \InvalidArgumentException
     */
    public function setState(int $state): void
    {
        if (!in_array($state, [self::STATE_FREE, self::STATE_BUFFER, self::STATE_TAKEN], true)) {
            throw new InvalidArgumentException(sprintf('Invalid parameter used for state'));
        }

        $this->state = $state;
    }

    /**
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @param null|Item $item
     * @throws \InvalidArgumentException
     */
    public function setItem($item): void
    {
        if (null !== $item && !($item instanceof Item)) {
            throw new InvalidArgumentException(sprintf('Item can be null or instance of %s', Item::class));
        }
        $this->item = $item;
    }

    /**
     * @return bool
     */
    public function hasItem(): bool
    {
        return $this->item !== null;
    }

    /**
     * @return array
     */
    public function getBufferOf(): array
    {
        return $this->bufferOf;
    }

    /**
     * @param Item[] $bufferOf
     * @throws \InvalidArgumentException
     */
    public function setBufferOf(array $bufferOf)
    {
        $this->bufferOf = $bufferOf;
    }

    /**
     * @return bool
     */
    public function isEmptyBufferOf(): bool
    {
        return empty($this->bufferOf);
    }

    /**
     * @param Item $item
     */
    public function addToBufferOf(Item $item): void
    {
        if (!in_array($item, $this->bufferOf, true)) {
            $this->bufferOf[] = $item;
        }
    }

    /**
     * @param Item $item
     * @throws \InvalidArgumentException
     */
    public function removeFromBufferOf(Item $item): void
    {

        if (false === $itemIndex = array_search($item, $this->bufferOf, true)) {
            throw new InvalidArgumentException('This cell isn\'t buffer of this item');
        }
        unset($this->bufferOf[$itemIndex]);
    }

    /**
     * @return mixed
     */
    public function getWasShoot(): bool
    {
        return $this->wasShoot;
    }

    /**
     * @param bool $wasShoot
     */
    public function setWasShoot(bool $wasShoot): void
    {
        $this->wasShoot = $wasShoot;
    }


}