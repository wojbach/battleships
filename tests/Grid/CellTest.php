<?php
use Battleship\Grid\Cell;
use Battleship\Grid\Item;
use PHPUnit\Framework\TestCase;

final class CellTest extends TestCase
{

    public function testCanPassWithNoArgumentsToConstructor(): void
    {
        $object = new Cell();
        $this->assertInstanceOf(Cell::class, $object);
    }

    public function testCanPassWithArgumentsToConstructor(): void
    {
        $object = new Cell(Cell::STATE_BUFFER, new Item(Item::ORIENTATION_HORIZONTAL,0,0), [new Item(Item::ORIENTATION_HORIZONTAL,0,0)]);
        $this->assertInstanceOf(Cell::class, $object);
        $this->assertSame(Cell::STATE_BUFFER, $object->getState());
        $this->assertEquals(new Item(Item::ORIENTATION_HORIZONTAL,0,0), $object->getItem());
        $this->assertEquals([new Item(Item::ORIENTATION_HORIZONTAL,0,0)], $object->getBufferOf());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidState(): void
    {
        new Cell(99999);
    }

    public function testPassStateManipulation() {
        $object = new Cell();
        $object->setState(Cell::STATE_TAKEN);
        $this->assertSame(Cell::STATE_TAKEN, $object->getState());
    }

    public function testPassItemManipulation() {
        $object = new Cell();
        $this->assertFalse($object->hasItem());
        $item = new Item(Item::ORIENTATION_HORIZONTAL,0,0);
        $object->setItem($item);
        $this->assertEquals(new Item(Item::ORIENTATION_HORIZONTAL,0,0), $object->getItem());
        $this->assertTrue($object->hasItem());
    }

    public function testPassBufferOfManipulation() {
        $object = new Cell();
        $this->assertTrue($object->isEmptyBufferOf());
        $item = new Item(Item::ORIENTATION_HORIZONTAL,0,0);
        $object->setBufferOf([$item]);
        $this->assertEquals([$item], $object->getBufferOf());

        $this->assertFalse($object->isEmptyBufferOf());

        $object->removeFromBufferOf($item);
        $this->assertEmpty($object->getBufferOf());

        $object->addToBufferOf($item);
        $object->addToBufferOf($item);
        $object->addToBufferOf($item);
        $this->assertEquals(array_values([$item]), array_values($object->getBufferOf()));

        $item2 = new Item(Item::ORIENTATION_HORIZONTAL,1,1);
        $object->addToBufferOf($item2);
        $this->assertEquals(array_values([$item,$item2]), array_values($object->getBufferOf()));

        $object->removeFromBufferOf($item);
        $object->removeFromBufferOf($item2);

        $this->assertTrue($object->isEmptyBufferOf());
    }

}