<?php
declare(strict_types = 1);

namespace Battleship;


use Battleship\Grid\Response;
use InvalidArgumentException;

class Shot
{

    /**
     * @var int
     */
    private $offsetTop;

    /**
     * @var int
     */
    private $offsetLeft;

    /**
     * @var Response
     */
    private $result;


    /**
     * Shot constructor.
     * @param int $offsetTop
     * @param int $offsetLeft
     * @throws \InvalidArgumentException
     */
    public function __construct(int $offsetTop, int $offsetLeft)
    {
        $this->setOffsetTop($offsetTop);
        $this->setOffsetLeft($offsetLeft);
    }

    /**
     * @return int
     */
    public function getOffsetTop(): int
    {
        return $this->offsetTop;
    }

    /**
     * @return int
     */
    public function getOffsetLeft(): int
    {
        return $this->offsetLeft;
    }

    /**
     * @param int $offsetTop
     * @throws \InvalidArgumentException
     */
    public function setOffsetTop(int $offsetTop)
    {
        $pattern = '/^\d*$/';
        if(1 !== preg_match($pattern, (string) $offsetTop)) {
            throw new InvalidArgumentException(sprintf('offsetTop should match the pattern: %s ', $pattern));
        }
        $this->offsetTop = $offsetTop;
    }

    /**
     * @param int $offsetLeft
     * @throws \InvalidArgumentException
     */
    public function setOffsetLeft(int $offsetLeft)
    {
        $pattern = '/^\d*$/';
        if(1 !== preg_match($pattern, (string) $offsetLeft)) {
            throw new InvalidArgumentException(sprintf('offsetLeft should match the pattern: %s ', $pattern));
        }
        $this->offsetLeft = $offsetLeft;
    }

    /**
     * @return Response
     */
    public function getResult(): Response
    {
        return $this->result;
    }

    /**
     * @param Response $result
     */
    public function setResult(Response $result)
    {
        $this->result = $result;
    }


}