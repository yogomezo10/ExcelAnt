<?php

namespace ExcelAnt\Traits;

use ExcelAnt\Coordinate\Coordinate;

trait Coordinable
{
    /**
     * {@inheritdoc}
     */
    public function setCoordinate(Coordinate $coordinate)
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCoordinate()
    {
        return $this->coordinate;
    }
}