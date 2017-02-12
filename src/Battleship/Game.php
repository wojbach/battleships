<?php
declare(strict_types = 1);

namespace Battleship;


use Battleship\Grid\Item;
use InvalidArgumentException;

/**
 * Class Game
 * @package Battleship
 */
class Game
{
    public const SHOTS_PER_ROUND = 'spr';
    public const GRID_SIZE = 'gs';

    /**
     * @var int|string
     */
    protected $gameId;

    /**
     * @var array
     */
    private $options = [
        self::SHOTS_PER_ROUND => 5,
        self::GRID_SIZE => 10,
    ];

    /**
     * @var Player[]
     */
    private $players = [];

    /**
     * @var Salvo[]
     */
    private $moves = [];

    /**
     * @var int
     */
    private $maxPlayers = 2;

    /**
     * @var array
     */
    private static $allowedOptionsWithTypes = [
        self::SHOTS_PER_ROUND => 'int',
        self::GRID_SIZE => 'int'
    ];

    private static $allowedOptionsFormats = [
        self::SHOTS_PER_ROUND => '/^[1-9]\d*$/',
        self::GRID_SIZE => '/^[1-9]\d*$/'
    ];

    /**
     * Game constructor.
     * @param int|string $gameId
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct($gameId, array $options = [])
    {

        if (!is_int($gameId) && !is_string($gameId)) {
            throw new InvalidArgumentException(sprintf('gameId should be int or string, got %s instead', gettype($gameId)));
        }

        $this->gameId = $gameId;

        $optionsNames = array_keys(self::$allowedOptionsWithTypes);
        foreach ($optionsNames as $name) {
            if (isset($options[$name])) {
                $this->setOption($name, $options[$name]);
            }
        }

        $this->gameId = $gameId;
    }


    /**
     * @param $key
     * @param $value
     * @throws \InvalidArgumentException
     */
    public function setOption($key, $value): void
    {
        $dataType = self::$allowedOptionsWithTypes[$key];
        $pattern = self::$allowedOptionsFormats[$key];
        $validateMethod = 'is_' . $dataType;

        if (false === $validateMethod($value)) {
            throw new InvalidArgumentException(sprintf('%s should be type of %s got %s instead', $key, $dataType, gettype($value)));
        }

        if(1 !== preg_match($pattern, (string) $value)) {
            throw new InvalidArgumentException(sprintf('%s should match the pattern: %s ', $key, $pattern));
        }

        $this->options[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function getOption($key)
    {
        $value = null;
        if (array_key_exists($key, $this->options)) {
            $value = $this->options[$key];
        }

        return $value;
    }

    /**
     * @param int|string $identity
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \SM\SMException
     */
    public function addPlayer($identity): bool
    {
        if (!is_int($identity) && !is_string($identity)) {
            throw new InvalidArgumentException(sprintf('Identity should be int or string, got %s instead', gettype($identity)));
        }

        $result = false;
        if (count($this->players) < $this->maxPlayers) {
            $this->players[$identity] = new Player($identity);
            $result = true;
        }

        return $result;
    }

    /**
     * @param int|string $identity
     * @return bool
     */
    public function removePlayer($identity): bool
    {
        $result = false;

        if (array_key_exists($identity, $this->players)) {
            unset($this->players[$identity]);
            $result = true;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @param string|int $identity
     * @return Player|null
     */
    public function getPlayer($identity): ?Player
    {
        $player = null;
        if(array_key_exists($identity, $this->players)) {
            $player = $this->players[$identity];
        }

        return $player;
    }

    /**
     * @return int
     */
    public function countPlayers(): int
    {
        return count($this->players);
    }

    /**
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->countPlayers() === $this->maxPlayers;
    }

    /**
     * @return void
     */
    public function flushPlayers(): void
    {
        $this->players = [];
    }

    /**
     * @return null|string|int
     */
    public function getLastTarget() {
        $response = null;
        if(!empty($this->moves)) {
            $salve = array_values(array_slice($this->moves, -1))[0];
            $response = $salve->getTarget();
        }

        return $response;
    }

    /**
     * @param int $playerId Target
     * @param array $shots
     * @throws \InvalidArgumentException
     */
    public function dischargeSalve(int $playerId, array $shots)
    {
        if($playerId === $this->getLastTarget()) {
            throw new InvalidArgumentException('This player can not be fired two times in a row');
        }

        if (count($shots) !== $this->options[self::SHOTS_PER_ROUND]) {
            throw new InvalidArgumentException(sprintf('Salve should have exactly %d shots', $this->options[self::SHOTS_PER_ROUND]));
        }

        $players = $this->getPlayers();

        /**
         * @var Player $target
         * @var Player $shooter
         */
        $target = array_splice($players, array_search($playerId, array_keys($players), true))[0];
        $shooter = array_shift($players);

        $salvo = new Salvo($playerId, $shots);
        $targetGrid = $target->getGrid(Grid::MINE);
        $shooterGrid = $shooter->getGrid(Grid::THEIRS);

        foreach ($salvo->getShots() as $shot) {
            $targetGrid->receiveShot($shot);
            $result = $shot->getResult();
            if($result->isHit()) {
                if($result->getItem()->getState() === Item::STATE_DESTROYED) {
                    $shipsFleet = $this->getPlayer($playerId)->getShipsFleet(ShipsFleet::MINE);
                    $shipsFleet->removeFromShips($result->getItem());
                }
            }
            /**
             * TODO: implement status update on shooter grid
             */
        }

        $this->moves[] = $salvo;

    }

}