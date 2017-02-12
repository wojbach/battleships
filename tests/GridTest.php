<?php

use Battleship\Grid;
use PHPUnit\Framework\TestCase;

final class GridTest extends TestCase
{

    public function testCanPassValidSizeToConstructor(): void
    {
        $object = new Grid(Grid::MAX_SIZE - 1);
        $this->assertAttributeEquals(Grid::MAX_SIZE - 1, 'size', $object);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidSize(): void
    {
        new Grid(Grid::MAX_SIZE + 1);
    }

    public function testCanPassSizeGetter(): void
    {
        $object = new Grid(12);
        $this->assertSame(12, $object->getSize());
    }

    public function testPassPutObject(): void
    {
        $object = new Grid(Grid::MAX_SIZE - 1);
        $object->putItem(GameTest::getFakeSubmarine());
        $this->assertEquals([GameTest::getFakeSubmarine()], $object->getItems());
        $cells = $object->getCells();
        foreach ($cells as $rowIndex => $cellRows) {
            foreach ($cellRows as $cellIndex => $cell) {
                if ($rowIndex === 1 && $cellIndex === 1) {
                    $this->assertEquals(new Grid\Cell(Grid\Cell::STATE_TAKEN, GameTest::getFakeSubmarine()), $cell);
                } else {
                    $this->assertEquals(new Grid\Cell(Grid\Cell::STATE_BUFFER, null, [GameTest::getFakeSubmarine()]), $cell);
                }
            }
        }
    }

    public function testPassWillObjectFit(): void
    {
        $object = new Grid(Grid::MAX_SIZE - 1);

        /**
         * stuck to right
         */
        $item1 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_HORIZONTAL, 1, $object->getSize() - 2);
        $this->assertTrue($object->willObjectFit($item1));
        $item2 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_HORIZONTAL, 1, $object->getSize() - 1);
        $this->assertFalse($object->willObjectFit($item2));

        /**
         * stuck to bottom
         */
        $item3 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_VERTICAL, $object->getSize() - 2, 1);
        $this->assertTrue($object->willObjectFit($item3));
        $item4 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_VERTICAL, $object->getSize() - 1, 1);
        $this->assertFalse($object->willObjectFit($item4));

        /**
         * outside grid
         */
        $item5 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_VERTICAL, $object->getSize() + 99, $object->getSize() + 99);
        $this->assertFalse($object->willObjectFit($item5));
    }

    public function testPassWillObjectCollide(): void
    {
        $object = new Grid(Grid::MAX_SIZE - 1);

        $item1 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_HORIZONTAL, 1, 2);
        $object->putItem($item1);

        /**
         * directly on element
         */
        $item2 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_HORIZONTAL, 1, 3);
        $this->assertTrue($object->willObjectCollide($item2));

        /**
         * buffer top
         */
        $item3 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_HORIZONTAL, 0, 2);
        $this->assertTrue($object->willObjectCollide($item3));

        /**
         * buffer right
         */
        $item4 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_VERTICAL, 0, 4);
        $this->assertTrue($object->willObjectCollide($item4));

        /**
         * buffer bottom
         */
        $item5 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_HORIZONTAL, 2, 2);
        $this->assertTrue($object->willObjectCollide($item5));

        /**
         * buffer left
         */
        $item6 = GameTest::getFakeCruiser(Grid\Item::ORIENTATION_VERTICAL, 0, 1);
        $this->assertTrue($object->willObjectCollide($item6));
    }


}
