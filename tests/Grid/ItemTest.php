<?php
use Battleship\Grid\Item;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{

    public function testCanPassWithValidArgumentsToConstructor(): void
    {
        $object = new Item(Item::ORIENTATION_HORIZONTAL, 1, 1);
        $this->assertInstanceOf(Item::class, $object);
        $this->assertSame(Item::ORIENTATION_HORIZONTAL, $object->getOrientation());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidOrientation(): void
    {
        new Item(99999, 1, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidOffsetTop(): void
    {
        new Item(Item::ORIENTATION_HORIZONTAL, -3, 1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidOffsetLeft(): void
    {
        new Item(Item::ORIENTATION_HORIZONTAL, 1, -3);
    }

    public function testPassOffsetGettersAndSetters()
    {
        $object = new Item(Item::ORIENTATION_HORIZONTAL, 0, 0);
        $object->setOffsetTop(1);
        $this->assertSame(1, $object->getOffsetTop());

        $object->setOffsetLeft(3);
        $this->assertSame(3, $object->getOffsetLeft());
    }

    public function testPassSizeSetterGetter()
    {
        $object = new Item(Item::ORIENTATION_HORIZONTAL, 0, 0);
        $object->setSize(10);
        $this->assertSame(10, $object->getSize());
    }

    public function testPassHealthSetterGetter()
    {
        $object = new Item(Item::ORIENTATION_HORIZONTAL, 0, 0);
        $object->setHealth(5);
        $this->assertSame(5, $object->getHealth());
    }

}