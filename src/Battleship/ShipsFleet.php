<?php
declare(strict_types = 1);

namespace Battleship;


use Battleship\Grid\Item;
use Battleship\Ships\Ship;
use Battleship\Ships\Ship\AircraftCarrier;
use Battleship\Ships\Ship\Battleship;
use Battleship\Ships\Ship\Cruiser;
use Battleship\Ships\Ship\Destroyer;
use Battleship\Ships\Ship\Submarine;

class ShipsFleet
{

    public const MINE = 0;
    public const THEIRS = 1;

    /**
     * @var Ship[]
     */
    private $ships = [];

    /**
     * @var array
     */
    private $availableFleet = [
        AircraftCarrier::class,
        Battleship::class,
        Cruiser::class,
        Cruiser::class,
        Destroyer::class,
        Destroyer::class,
        Submarine::class,
        Submarine::class
    ];

    /**
     * @var array
     */
    private $initialAvailableFleet = [];

    /**
     * ShipsCollection constructor.
     * @param array $availableFleet
     * @throws \InvalidArgumentException
     */
    public function __construct(array $availableFleet = null)
    {
        if (is_array($availableFleet) && empty($availableFleet)) {
            throw new \InvalidArgumentException('available fleet should be not empty array');
        }

        if (is_array($availableFleet) && !empty($availableFleet)) {
            $this->availableFleet = $availableFleet;
        }
        $this->initialAvailableFleet = $this->availableFleet;
    }

    /**
     * @param Ship $ship
     * @return bool
     */
    public function callUpShip(Ship $ship): bool
    {
        $response = false;
        $type = get_class($ship);

        if ($this->isShipAvailable($type)) {
            array_splice($this->availableFleet, array_search($type, $this->availableFleet, true), 1);
            $this->ships[] = $ship;
            $response = true;
        }

        return $response;
    }

    /**
     * @param Ship $ship
     * @return bool
     */
    public function callOffShip(Ship $ship): bool
    {
        $response = false;

        $diff = array_diff_key($this->initialAvailableFleet, $this->availableFleet);
        $type = get_class($ship);
        if(in_array($type, $diff, true)) {
            $index = array_search($ship, $this->ships, true);
            if($index !== false) {
                array_splice($this->ships, $index, 1);
                $this->availableFleet[] = $type;
                $response = true;
            }
        }

        return $response;
    }

    /**
     * @param string $type
     * @return bool
     */
    private function isShipAvailable(string $type): bool
    {
        return in_array($type, $this->availableFleet, true);
    }

    /**
     * @return Ship[]
     */
    public function getShips(): array
    {
        return $this->ships;
    }

    /**
     * @return array
     */
    public function getAvailableFleet(): array
    {
        return $this->availableFleet;
    }

    /**
     * @return bool
     */
    public function anyAvailableShips(): bool
    {
        return !empty($this->availableFleet);
    }

    public function removeFromShips(Item $ship):void
    {
        $key = array_search($ship, $this->ships, true);
        if(FALSE !== $key) {
            unset($this->ships[$key]);
        }
    }


}