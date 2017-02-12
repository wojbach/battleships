<?php

use Battleship\Game;
use Battleship\Player;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    /**
     * @return \Battleship\Ships\Ship
     */
    public static function getFakeSubmarine() {
        return new class(\Battleship\Grid\Item::ORIENTATION_VERTICAL, 1, 1) extends \Battleship\Ships\Ship {
            static protected $size = 1;
        };
    }

    public static function getFakeCruiser(int $orientation, int $top, int $left) {
        return new class($orientation, $top, $left) extends \Battleship\Ships\Ship {
            static protected $size = 2;
        };
    }

    public static function getFakeBattleship(int $orientation, int $top, int $left) {
        return new class($orientation, $top, $left) extends \Battleship\Ships\Ship {
            static protected $size = 4;
        };
    }

    public function testCanPassNoOptionsToConstructor(): void
    {
        $object = new Game('test');
        $this->assertAttributeEquals('test', 'gameId', $object);
    }


    public function testCanPassOptionsToConstructor(): void
    {
        $object = new Game(1, [Game::SHOTS_PER_ROUND => 3, Game::GRID_SIZE => 12, 'dummyOption' => 444]);
        $this->assertAttributeEquals(1, 'gameId', $object);
        $this->assertAttributeEquals(
            [
                Game::SHOTS_PER_ROUND => 3,
                Game::GRID_SIZE => 12
            ], 'options', $object);


    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidGameId(): void
    {
        new Game([]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidOptions(): void
    {
        new Game('test', [Game::SHOTS_PER_ROUND => new stdClass()]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidOptionPattern(): void
    {
        new Game('test', [Game::SHOTS_PER_ROUND => 0]);
    }

    public function testCanPassOptionSetterGetter(): void
    {
        $object = new Game('test');
        $object->setOption(Game::GRID_SIZE, 14);
        $this->assertSame(14, $object->getOption(Game::GRID_SIZE));
    }

    public function testCanPassPlayersManipulation(): void
    {
        $object = new Game('test');

        $object->addPlayer(1);
        $this->assertEquals(new Player(1), $object->getPlayer(1));

        $object->addPlayer(2);
        $this->assertTrue($object->isFull());

        $this->assertEquals([1 => new Player(1),2 => new Player(2)], $object->getPlayers());

        $object->removePlayer(1);
        $this->assertFalse($object->isFull());

        $this->assertSame(1, $object->countPlayers());

        $object->flushPlayers();
        $this->assertEmpty($object->getPlayers());
    }

}
