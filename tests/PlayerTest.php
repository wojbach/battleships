<?php

use Battleship\Grid;
use Battleship\Player;
use Battleship\Ships\Ship\Submarine;
use Battleship\ShipsFleet;
use PHPUnit\Framework\TestCase;

final class PlayerTest extends TestCase
{

    public function testCanPassWithValidIdToConstructor(): void
    {
        $object = new Player(1);
        $this->assertSame(1, $object->getPlayerId(), $object);

        $object2 = new Player('test');
        $this->assertSame('test', $object2->getPlayerId(), $object2);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidPlayerId(): void
    {
        new Player([]);
    }

    public function testCanPassSetterGetterShipsFleet(): void
    {
        $object = new Player('test');
        $object->setShipsFleet(ShipsFleet::MINE, new ShipsFleet());
        $this->assertEquals(new ShipsFleet(), $object->getShipsFleet(ShipsFleet::MINE));
        $this->assertNotEquals(new ShipsFleet(), $object->getShipsFleet(ShipsFleet::THEIRS));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnSettingInvalidShipsFleetType(): void
    {
        $object = new Player('test');
        $object->setShipsFleet(99999999, new ShipsFleet());
    }

    public function testCanPassSetterGetterGrid(): void
    {
        $object = new Player('test');
        $object->setGrid(Grid::MINE, new Grid(15));
        $this->assertEquals(new Grid(15), $object->getGrid(Grid::MINE));
        $this->assertNotEquals(new Grid(15), $object->getShipsFleet(Grid::THEIRS));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnSettingInvalidShipsGridType(): void
    {
        $object = new Player('test');
        $object->setGrid(99999999, new Grid(15));
    }

    public function testCanPassPutShipAndTakeOffShip(): void
    {
        $object = new Player('test');
        $submarine = GameTest::getFakeSubmarine();
        $object->setShipsFleet(ShipsFleet::MINE, new ShipsFleet([get_class($submarine)]));
        $object->setGrid(Grid::MINE, new Grid(15));


        $this->assertTrue($object->putShip($submarine));
        $this->assertTrue($object->takeOffShip($submarine));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnPuttingShipWithoutHavingFleet(): void
    {
        $object = new Player('test');
        $object->setGrid(Grid::MINE, new Grid(15));

        $submarine = GameTest::getFakeSubmarine();

        $this->assertTrue($object->putShip($submarine));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnPuttingShipWithoutHavingGrid(): void
    {
        $object = new Player('test');
        $object->setShipsFleet(ShipsFleet::MINE, new ShipsFleet([Submarine::class]));

        $submarine = GameTest::getFakeSubmarine();

        $this->assertTrue($object->putShip($submarine));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnTakingOffShipWithoutHavingFleet(): void
    {
        $object = new Player('test');
        $object->setGrid(Grid::MINE, new Grid(15));

        $submarine = GameTest::getFakeSubmarine();

        $this->assertTrue($object->takeOffShip($submarine));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnTakingOffShipWithoutHavingGrid(): void
    {
        $object = new Player('test');
        $object->setShipsFleet(ShipsFleet::MINE, new ShipsFleet([Submarine::class]));

        $submarine = GameTest::getFakeSubmarine();

        $this->assertTrue($object->takeOffShip($submarine));
    }
}
