<?php
declare(strict_types = 1);

namespace Battleship\Grid;


class Response
{

    /**
     * @var bool
     */
    private $hit;

    /**
     * @var null|Item
     */
    private $item;

    /**
     * Response constructor.
     * @param bool $hit
     * @param Item $item
     */
    public function __construct(bool $hit, $item)
    {
        $this->hit = $hit;
        $this->item = $item;
    }

    /**
     * @return bool
     */
    public function isHit(): bool
    {
        return $this->hit;
    }

    /**
     * @return Item|null
     */
    public function getItem()
    {
        return $this->item;
    }


}