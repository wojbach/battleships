<?php
declare(strict_types = 1);

namespace Battleship;


use Battleship\Grid\Cell;
use Battleship\Grid\Exception\CollideException;
use Battleship\Grid\Exception\FitException;
use Battleship\Grid\Item;
use Battleship\Grid\Response;
use Battleship\Ships\Ship;
use Closure;
use InvalidArgumentException;

class Grid
{
    public const MINE = 0;
    public const THEIRS = 1;

    public const MIN_SIZE = 10;
    public const MAX_SIZE = 20;

    /**
     * @var int
     */
    private $size;

    /**
     * @var array
     */
    private $cells;

    /**
     * @var Item[]
     */
    private $items;

    /**
     * Grid constructor.
     * @param int $size
     * @throws \InvalidArgumentException
     */
    public function __construct(int $size)
    {
        if ($size < self::MIN_SIZE || $size > self::MAX_SIZE) {
            throw new InvalidArgumentException(sprintf('size should be between %d and %d, got %s', self::MIN_SIZE, self::MAX_SIZE, gettype($size)));
        }

        $this->size = $size;
    }

    /**
     * @return array
     */
    public function getCells(): ?array
    {
        return $this->cells;
    }


    /**
     * @param Item $item
     * @return bool
     * @throws \InvalidArgumentException
     * @throws CollideException
     * @throws FitException
     */
    public function putItem(Item $item): bool
    {
        if (!$this->willObjectFit($item)) {
            throw new FitException('Object doesn\'t fit in a grid');
        }

        if ($this->willObjectCollide($item)) {
            throw new CollideException('Object collides with another object');
        }

        $cell = new Cell(Cell::STATE_TAKEN, $item);
        $this->items[] = $item;
        return (bool)$this->iterateItem($item, function ($row, $col) use ($cell) {
            $this->setCell($cell, $row, $col);

            $rowBounds = $this->findBufferBounds($row);
            $colBounds = $this->findBufferBounds($col);

            foreach ($rowBounds as $bufferRow) {
                foreach ($colBounds as $bufferCol) {
                    $neighbourCell = $this->getCell($bufferRow, $bufferCol);
                    if (null === $neighbourCell) {
                        $this->setCell(new Cell(Cell::STATE_BUFFER, null, [$cell->getItem()]), $bufferRow, $bufferCol);
                    } else if ($neighbourCell->getState() === Cell::STATE_BUFFER) {
                        $neighbourCell->addToBufferOf($cell->getItem());
                    }
                }
            }
        });
    }

    /**
     * @param Item $item
     * @throws \InvalidArgumentException
     */
    public function takeOffItem(Item $item): void
    {
        if (false === $objectIndex = array_search($item, $this->items, true)) {
            throw new InvalidArgumentException('This object isn\'t placed on this grid');
        }

        $this->iterateItem($item, function ($row, $col) use ($item) {
            $this->unsetCell($row, $col);

            $rowBounds = $this->findBufferBounds($row);
            $colBounds = $this->findBufferBounds($col);

            foreach ($rowBounds as $bufferRow) {
                foreach ($colBounds as $bufferCol) {
                    $neighbourCell = $this->getCell($bufferRow, $bufferCol);
                    if ($neighbourCell !== null && $neighbourCell->getState() === Cell::STATE_BUFFER) {
                        $neighbourCell->removeFromBufferOf($item);
                        if ($neighbourCell->isEmptyBufferOf()) {
                            $this->unsetCell($bufferRow, $bufferCol);
                        }
                    }
                }
            }
        });

        unset($this->items[$objectIndex]);
    }

    /**
     * @param Item $object
     * @return bool
     */
    public function willObjectFit(Item $object): bool
    {
        $fits = true;
        if ($object->getOrientation() === Item::ORIENTATION_HORIZONTAL && $object->getOffsetLeft() + $object->getSize() > $this->size) {
            $fits = false;
        }

        if ($object->getOrientation() === Item::ORIENTATION_VERTICAL && $object->getOffsetTop() + $object->getSize() > $this->size) {
            $fits = false;
        }

        return $fits;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function willObjectCollide(Item $item): bool
    {
        $response = (bool)$this->iterateItem($item, function ($row, $col) {
            if ($this->hasCell($row, $col)) {
                /** @var Cell $cell */
                $cell = $this->cells[$row][$col];
                if ($cell->getState() !== Cell::STATE_FREE) {
                    return true;
                }
            }
            return null;
        });

        return $response;
    }

    /**
     * @param Item $object
     * @param Closure $closure
     * @return mixed|null
     */
    private function iterateItem(Item $object, Closure $closure)
    {
        $i = 0;
        $return = null;
        $offsetTop = $object->getOffsetTop();
        $offsetLeft = $object->getOffsetLeft();

        if ($object->getOrientation() === Item::ORIENTATION_VERTICAL) {
            do {
                $return = $closure->call($this, $offsetTop + $i, $offsetLeft);
                $i++;
            } while ($i < $object->getSize() && $return === null);
        } else {
            do {
                $return = $closure->call($this, $offsetTop, $offsetLeft + $i);
                $i++;
            } while ($i < $object->getSize() && $return === null);
        }

        return $return;
    }

    /**
     * @param int $offsetTop
     * @param int $offsetLeft
     * @return bool
     */
    public function hasCell(int $offsetTop, int $offsetLeft): bool
    {
        $response = false;
        if (isset($this->cells[$offsetTop], $this->cells[$offsetTop][$offsetLeft])) {
            $response = true;
        }
        return $response;
    }

    /**
     * @param Cell $cell
     * @param int $offsetTop
     * @param int $offsetLeft
     * @throws \InvalidArgumentException
     */
    private function setCell(Cell $cell, int $offsetTop, int $offsetLeft): void
    {
        if ($offsetLeft < 0 || $offsetLeft > $this->size) {
            throw new InvalidArgumentException(sprintf('Invalid left offset given: %d', $offsetLeft));
        }

        if ($offsetTop < 0 || $offsetTop > $this->size) {
            throw new InvalidArgumentException(sprintf('Invalid top offset given: %d', $offsetTop));
        }

        $this->cells[$offsetTop][$offsetLeft] = $cell;
    }

    /**
     * @param int $position
     * @return array
     */
    private function findBufferBounds(int $position): array
    {
        $bufferMin = $position - 1 < 0 ? $position + 1 : $position - 1;
        $bufferMax = $position + 1 > $this->size ? $position - 1 : $position + 1;
        return range($bufferMin, $bufferMax);
    }

    /**
     * @param $offsetTop
     * @param $offsetLeft
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function unsetCell($offsetTop, $offsetLeft): bool
    {
        $response = false;

        if ($offsetLeft < 0 || $offsetLeft > $this->size) {
            throw new InvalidArgumentException(sprintf('Invalid left offset given: %d', $offsetLeft));
        }

        if ($offsetTop < 0 || $offsetTop > $this->size) {
            throw new InvalidArgumentException(sprintf('Invalid top offset given: %d', $offsetTop));
        }

        if (isset($this->cells[$offsetTop], $this->cells[$offsetTop][$offsetLeft])) {
            unset($this->cells[$offsetTop][$offsetLeft]);
            if (empty($this->cells[$offsetTop])) {
                unset($this->cells[$offsetTop]);
            }
            $response = true;
        }

        return $response;
    }

    /**
     * @param int $offsetTop
     * @param int $offsetLeft
     * @return Cell|null
     */
    private function getCell(int $offsetTop, int $offsetLeft): ?Cell
    {
        $cell = null;
        if (isset($this->cells[$offsetTop], $this->cells[$offsetTop][$offsetLeft])) {
            $cell = $this->cells[$offsetTop][$offsetLeft];
        }
        return $cell;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param Shot $shot
     * @throws \InvalidArgumentException
     */
    public function receiveShot(Shot $shot)
    {
        $cell = $this->getCell($shot->getOffsetTop(), $shot->getOffsetLeft());
        $hit = false;
        if (null !== $cell) {
            $hit = true;
            $cell->setWasShoot(true);
            if (null !== $item = $cell->getItem()) {
                if ($item->getState() === Ship::STATE_UNDAMAGED) {
                    $item->setState(Ship::STATE_DAMAGED);
                    $item->setHealth($item->getHealth() - 1);
                } else if ($item->getState() === Ship::STATE_DAMAGED) {
                    if ($item->getHealth() === 1) {
                        $item->setState(Ship::STATE_DESTROYED);
                    }
                    $item->setHealth($item->getHealth() - 1);
                }
            }
        } else {
            $cell = new Cell();
            $cell->setWasShoot(true);
            $this->setCell($cell, $shot->getOffsetTop(), $shot->getOffsetLeft());
        }

        $shot->setResult(new Response($hit, $cell->getItem()));
    }

}