<?php


namespace Matrix;


class Builder
{
  
    public static function createFilledMatrix($fillValue, $rows, $columns = null)
    {
        if ($columns === null) {
            $columns = $rows;
        }

        $rows = Matrix::validateRow($rows);
        $columns = Matrix::validateColumn($columns);

        return new Matrix(
            array_fill(
                0,
                $rows,
                array_fill(
                    0,
                    $columns,
                    $fillValue
                )
            )
        );
    }

    /**
     * Create a new identity matrix of specified dimensions
     * This will always be a square matrix, with the number of rows and columns matching the provided dimension
     *
     * @param int $dimensions
     * @return Matrix
     * @throws Exception
     */
    public static function createIdentityMatrix($dimensions, $fillValue = null)
    {
        $grid = static::createFilledMatrix($fillValue, $dimensions)->toArray();

        for ($x = 0; $x < $dimensions; ++$x) {
            $grid[$x][$x] = 1;
        }

        return new Matrix($grid);
    }
}
