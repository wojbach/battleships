<?php
declare(strict_types = 1);

namespace Battleship\Grid;


interface ItemInterface
{
    public function getSize();
    public function getOrientation();
    public function getOffsetTop();
    public function getOffsetLeft();

}