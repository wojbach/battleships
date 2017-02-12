<?php
declare(strict_types = 1);

namespace Battleship;


use InvalidArgumentException;

class Salvo
{

    /**
     * @var Shot[]
     */
    private $shots;

    /**
     * @var int|string
     */
    private $playerId;

    public function __construct($identity, array $shots)
    {
        if (!is_int($identity) && !is_string($identity)) {
            throw new InvalidArgumentException(sprintf('Identity should be int or string, got %s instead', gettype($identity)));
        }

        $this->setTarget($identity);
        $this->setShots($shots);
    }

    /**
     * @return int|string
     */
    public function getTarget()
    {
        return $this->playerId;
    }

    /**
     * @param int|string $playerId
     */
    public function setTarget($playerId)
    {
        $this->playerId = $playerId;
    }

    /**
     * @return Shot[]
     */
    public function getShots(): array
    {
        return $this->shots;
    }

    /**
     * @param Shot[] $shots
     */
    public function setShots(array $shots)
    {
        $this->shots = $shots;
    }


}