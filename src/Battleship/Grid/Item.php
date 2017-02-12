<?php
declare(strict_types = 1);

namespace Battleship\Grid;


use InvalidArgumentException;

class Item implements ItemInterface
{
    public const STATE_UNDAMAGED = 0;
    public const STATE_DAMAGED = 1;
    public const STATE_DESTROYED = 2;

    public const ORIENTATION_VERTICAL = 0;
    public const ORIENTATION_HORIZONTAL = 1;

    /**
     * @var int
     */
    protected $health;

    /**
     * @var int
     */
    protected $state;

    protected $orientation;
    static protected $size;

    /**
     * @var int
     */
    protected $offsetTop;

    /**
     * @var int
     */
    protected $offsetLeft;

    /**
     * Item constructor.
     * @param int $orientation
     * @param int $offsetTop
     * @param int $offsetLeft
     * @throws \InvalidArgumentException
     */
    public function __construct(int $orientation, int $offsetTop, int $offsetLeft)
    {
        if ($orientation !== self::ORIENTATION_VERTICAL && $orientation !== self::ORIENTATION_HORIZONTAL) {
            throw new \InvalidArgumentException('Invalid object orientation');
        }

        $this->orientation = $orientation;
        $this->setOffsetTop($offsetTop);
        $this->setOffsetLeft($offsetLeft);
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
        if (!in_array($state, [self::STATE_UNDAMAGED, self::STATE_DAMAGED, self::STATE_DESTROYED], true)) {
            throw new InvalidArgumentException(sprintf('Invalid state given: %d', $state));
        }
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getOrientation(): int
    {
        return $this->orientation;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return static::$size;
    }

    public function setSize(int $size): void
    {
        static::$size = $size;
    }

    /**
     * @return int
     */
    public function getOffsetTop(): int
    {
        return $this->offsetTop;
    }

    /**
     * @return int
     */
    public function getOffsetLeft(): int
    {
        return $this->offsetLeft;
    }

    /**
     * @param int $offsetTop
     * @throws \InvalidArgumentException
     */
    public function setOffsetTop(int $offsetTop)
    {
        $pattern = '/^\d*$/';
        if (1 !== preg_match($pattern, (string)$offsetTop)) {
            throw new InvalidArgumentException(sprintf('offsetTop should match the pattern: %s ', $pattern));
        }
        $this->offsetTop = $offsetTop;
    }

    /**
     * @param int $offsetLeft
     * @throws \InvalidArgumentException
     */
    public function setOffsetLeft(int $offsetLeft)
    {
        $pattern = '/^\d*$/';
        if (1 !== preg_match($pattern, (string)$offsetLeft)) {
            throw new InvalidArgumentException(sprintf('offsetLeft should match the pattern: %s ', $pattern));
        }
        $this->offsetLeft = $offsetLeft;
    }

    /**
     * @return int
     */
    public function getHealth(): int
    {
        return $this->health;
    }

    /**
     * @param int $health
     */
    public function setHealth(int $health)
    {
        $this->health = $health;
    }


}