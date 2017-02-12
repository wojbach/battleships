<?php

use Battleship\Game;
use Battleship\Grid;
use Battleship\ShipsFleet;
use PHPUnit\Framework\TestCase;

final class BehaviourTest
{
    public function Dummy(): void
    {
        $game = new Game('test', [Game::GRID_SIZE => 20]);
        $game->addPlayer(1);
        $game->addPlayer(2);

        $game->getPlayer(1)
            ->setGrid(Grid::MINE, new Grid($game->getOption(Game::GRID_SIZE)))
            ->setGrid(Grid::THEIRS, new Grid($game->getOption(Game::GRID_SIZE)));

        $player = $game->getPlayer(1);
        $this->assertSame($player->getGrid(Grid::MINE)->getSize(), $game->getOption(Game::GRID_SIZE));

        $player->setShipsFleet(ShipsFleet::MINE, new ShipsFleet());
        $myFleet = $player->getShipsFleet(ShipsFleet::MINE);
        $i = 0;
        foreach($myFleet->getAvailableFleet() as $shipName) {
            $ship = new $shipName(Grid\Item::ORIENTATION_HORIZONTAL,$i,$i);
            $player->putShip($ship);
            $i += 2;
        }

        $availableFleet = $myFleet->getAvailableFleet();
        $cellsAfter = $player->getGrid(Grid::MINE)->getCells();


    }

}
