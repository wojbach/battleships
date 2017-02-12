<?php

use Battleship\ShipsFleet;
use PHPUnit\Framework\TestCase;

final class ShipsFleetTest extends TestCase
{
    public function testCanPassNoArgsConstructor(): void
    {
        $object = new ShipsFleet();

        $reflection = new ReflectionProperty(ShipsFleet::class, 'availableFleet');
        $reflection->setAccessible(true);

        $this->assertEquals($reflection->getValue($object), $object->getAvailableFleet());
    }


    public function testCanPassAvailableFleetToConstructor(): void
    {
        $battleship1 = get_class(GameTest::getFakeBattleship(\Battleship\Grid\Item::ORIENTATION_HORIZONTAL,0,0));
        $battleship2 = get_class(GameTest::getFakeBattleship(\Battleship\Grid\Item::ORIENTATION_HORIZONTAL,1,1));
        $cruiser1 = get_class(GameTest::getFakeCruiser(\Battleship\Grid\Item::ORIENTATION_HORIZONTAL,2,2));

        $object = new ShipsFleet([$battleship1, $battleship2, $cruiser1]);
        $this->assertAttributeEquals([$battleship1, $battleship2, $cruiser1], 'availableFleet', $object);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnEmptyAvailableFleet(): void
    {
        new ShipsFleet([]);
    }

    public function testPassShipsManipulations(): void
    {
        $battleship1 = GameTest::getFakeBattleship(\Battleship\Grid\Item::ORIENTATION_HORIZONTAL,0,0);
        $battleship2 = GameTest::getFakeBattleship(\Battleship\Grid\Item::ORIENTATION_HORIZONTAL,1,1);
        $cruiser1 = GameTest::getFakeCruiser(\Battleship\Grid\Item::ORIENTATION_HORIZONTAL,2,2);

        $object = new ShipsFleet([get_class($battleship1), get_class($battleship2), get_class($cruiser1)]);
        $object->callUpShip($battleship1);

        $this->assertEquals([$battleship1], $object->getShips());

        $object->callUpShip($battleship2);
        $object->callUpShip($cruiser1);
        $this->assertEquals([$battleship1, $battleship2, $cruiser1], $object->getShips());

        $this->assertFalse($object->callUpShip($cruiser1));

        $this->assertFalse($object->anyAvailableShips());

        $this->assertTrue($object->callOffShip($battleship1));
        $this->assertEquals([get_class($battleship1)], $object->getAvailableFleet());

        $this->assertTrue($object->callOffShip($battleship2));
        $this->assertTrue($object->callOffShip($cruiser1));

        $this->assertFalse($object->callOffShip($cruiser1));

        $this->assertEmpty($object->getShips());
    }

}
