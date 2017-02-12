<?php
declare(strict_types = 1);

namespace Battleship;


use Battleship\Grid\Item;
use Battleship\Ships\Ship;
use InvalidArgumentException;

class Player
{
    /**
     * @var int|string
     */
    private $playerId;

    /**
     * @var Grid[]
     */
    private $grids = [];


    /**
     * @var ShipsFleet[]
     */
    private $shipsFleets = [];

    /**
     * Player constructor.
     * @param $playerId
     * @throws \InvalidArgumentException
     */
    public function __construct($playerId)
    {
        if (!is_int($playerId) && !is_string($playerId)) {
            throw new InvalidArgumentException(sprintf('playerId should be int or string, got %s instead', gettype($playerId)));
        }

        $this->playerId = $playerId;
    }

    /**
     * @return int|string
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param int $type
     * @param ShipsFleet $shipsFleet
     * @throws \InvalidArgumentException
     */
    public function setShipsFleet(int $type, ShipsFleet $shipsFleet)
    {
        if ($type !== ShipsFleet::MINE && $type !== ShipsFleet::THEIRS) {
            throw new InvalidArgumentException('Invalid fleet type');
        }

        $this->shipsFleets[$type] = $shipsFleet;
    }

    /**
     * @param int $type
     * @return ShipsFleet|null
     */
    public function getShipsFleet(int $type): ?ShipsFleet
    {
        $value = null;
        if (array_key_exists($type, $this->shipsFleets)) {
            $value = $this->shipsFleets[$type];
        }

        return $value;
    }

    /**
     * @param int $type
     * @param Grid $grid
     * @return Player
     * @throws InvalidArgumentException
     */
    public function setGrid(int $type, Grid $grid): Player
    {
        if ($type !== Grid::MINE && $type !== Grid::THEIRS) {
            throw new InvalidArgumentException('Invalid grid type');
        }

        $this->grids[$type] = $grid;

        return $this;
    }

    /**
     * @param int $type
     * @return Grid|null
     */
    public function getGrid(int $type): ?Grid
    {
        $value = null;
        if (array_key_exists($type, $this->grids)) {
            $value = $this->grids[$type];
        }

        return $value;
    }

    /**
     * @param Item $ship
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \Battleship\Grid\Exception\FitException
     * @throws \Battleship\Grid\Exception\CollideException
     */
    public function putShip(Item $ship): bool
    {
        if (!array_key_exists(ShipsFleet::MINE, $this->shipsFleets)) {
            throw new InvalidArgumentException(sprintf('Ships fleet missing'));
        }

        if (!array_key_exists(Grid::MINE, $this->grids)) {
            throw new InvalidArgumentException(sprintf('Grid missing'));
        }

        $response = false;

        $shipsFleet = $this->shipsFleets[ShipsFleet::MINE];
        if ($shipsFleet->callUpShip($ship)) {
            $this->grids[Grid::MINE]->putItem($ship);
            $response = true;
        }

        return $response;
    }

    /**
     * @param Ship $ship
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function takeOffShip(Ship $ship): bool
    {
        if (!array_key_exists(ShipsFleet::MINE, $this->shipsFleets)) {
            throw new InvalidArgumentException(sprintf('Ships fleet missing'));
        }

        if (!array_key_exists(Grid::MINE, $this->grids)) {
            throw new InvalidArgumentException(sprintf('Grid missing'));
        }

        $response = false;

        $shipsFleet = $this->shipsFleets[ShipsFleet::MINE];

        if ($shipsFleet->callOffShip($ship)) {
            $this->grids[Grid::MINE]->takeOffItem($ship);
            $response = true;
        }

        return $response;
    }
}