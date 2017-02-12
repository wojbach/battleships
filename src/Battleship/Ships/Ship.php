<?php
declare(strict_types = 1);

namespace Battleship\Ships;


use Battleship\Grid\Item;
use InvalidArgumentException;

abstract class Ship extends Item
{

    /**
     * Ship constructor.
     * @param int $orientation
     * @param int $offsetTop
     * @param int $offsetLeft
     * @throws \InvalidArgumentException
     */
    public function __construct(int $orientation, int $offsetTop, int $offsetLeft)
    {
        parent::__construct($orientation, $offsetTop, $offsetLeft);
        $this->state = self::STATE_UNDAMAGED;
        $this->health = self::$size;
    }
}