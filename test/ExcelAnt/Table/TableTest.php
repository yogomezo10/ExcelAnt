<?php

namespace ExcelAnt\Table;

use ExcelAnt\Table\Table;
use ExcelAnt\Table\Label;
use ExcelAnt\Cell\Cell;
use ExcelAnt\Cell\EmptyCell;
use ExcelAnt\Style\Fill;
use ExcelAnt\Style\Font;
use ExcelAnt\Collections\StyleCollection;

class TableTest extends \PHPUnit_Framework_TestCase
{
    private $table;

    public function setUp()
    {
        $this->table = new Table();
    }

    public function testSetAndGetLabel()
    {
        $this->table->setLabel(new Label());

        $this->assertInstanceOf('ExcelAnt\Table\Label', $this->table->getLabel());
    }

    public function testSetAndGetCell()
    {
        $this->table->setCell(new Cell('Foo'));
        $cellCollection = $this->table->getCells();

        $this->assertCount(1, $cellCollection);
        $this->assertEquals('Foo', $cellCollection[0]->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRowWithInvalidIndex()
    {
        $this->table->getRow('foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetRowWithOutOfBoundsIndex()
    {
        $this->table->getRow(999999);
    }

    public function testSetRowWithDefaultConfigurationAndIndirectlyGetRow()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $row = $this->table->getRow(0);

        $this->assertEquals('foo', $row[0]->getValue());
        $this->assertEquals('bar', $row[1]->getValue());
        $this->assertEquals('baz', $row[2]->getValue());
    }

    public function testSetRowWithSingleValue()
    {
        $this->table->setRow('foo');
        $row = $this->table->getRow(0);

        $this->assertEquals('foo', $row[0]->getValue());
    }

    public function testSetRowWithAnEmptyValue()
    {
        $this->table->setRow(['foo', null, 'bar']);
        $row = $this->table->getRow(0);

        for ($i = 0; $i < 3; $i++) {
            if ($i === 1) {
                $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[$i]);
            } else {
                $this->assertInstanceOf('ExcelAnt\Cell\Cell', $row[$i]);
            }
        }
    }

    public function testSetRowWhenWhereAreAlreadyRows()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby']);
        $row = $this->table->getRow(1);

        $this->assertEquals('bob', $row[0]->getValue());
        $this->assertEquals('bobby', $row[1]->getValue());
    }

    public function testGetLastRow()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby']);
        $this->assertEquals(1, $this->table->getLastRow());
    }

    public function testGetLastRowWithColumn()
    {
        $this->table->setColumn(['foo', 'bar', 'baz']);
        $this->table->setColumn(['bob', 'bobby']);
        $this->assertEquals(2, $this->table->getLastRow());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCleanRowWithInvalidIndex()
    {
        $this->table->cleanRow('foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testCleanRowWithOutOfBoundsIndex()
    {
        $this->table->cleanRow(999999);
    }

    public function testCleanRow()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->cleanRow(0);
        $row = $this->table->getRow(0);

        $this->assertCount(3, $row);

        foreach ($row as $cell) {
            $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $cell);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRowWithInvalidIndex()
    {
        $this->table->setRow(['foo', 'bar', 'baz'], 'foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testSetRowWithOutOfBoundsIndex()
    {
        $this->table->setRow(['foo', 'bar', 'baz'], 999999);
    }

    public function testSetRowWithAnExistingIndex()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby'], 0);
        $row = $this->table->getRow(0);

        $this->assertEquals('bob', $row[0]->getValue());
        $this->assertEquals('bobby', $row[1]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[2]);
        $this->assertCount(3, $row);
    }

    public function testSetRowWithAnExistingIndexAndTheNewDataAreMoreLength()
    {
        $this->table->setRow(['bob', 'bobby',]);
        $this->table->setRow(['foo', 'bar', 'baz'], 0);
        $row = $this->table->getRow(0);

        $this->assertEquals('foo', $row[0]->getValue());
        $this->assertEquals('bar', $row[1]->getValue());
        $this->assertEquals('baz', $row[2]->getValue());
        $this->assertCount(3, $row);
    }

    public function testSetRowWithAnExistingRowAndEmptyValue()
    {
        $this->table->setRow(['bob', 'bobby', null, null]);
        $this->table->setRow(['foo', 'bar', 'baz'], 0);
        $row = $this->table->getRow(0);

        $this->assertEquals('foo', $row[0]->getValue());
        $this->assertEquals('bar', $row[1]->getValue());
        $this->assertEquals('baz', $row[2]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[3]);
        $this->assertCount(4, $row);
    }

    public function testSetRowWithStyles()
    {
        $styles = new StyleCollection([new Fill(), new Font()]);
        $this->table->setRow(['foo', 'bar', 'baz'], null, $styles);
        $row = $this->table->getRow(0);

        foreach ($row as $cell) {
            $this->assertInstanceOf('ExcelAnt\Collections\StyleCollection', $cell->getStyles());
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveRowWithInvalidIndex()
    {
        $this->table->removeRow('foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testRemoveRowWithOutOfBoundsIndex()
    {
        $this->table->removeRow(999999);
    }

    public function testRemoveRow()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby']);
        $this->table->removeRow(0);

        $this->assertCount(1, $this->table->getTable());

        $row = $this->table->getRow(1);
        $this->assertEquals('bob', $row[0]->getValue());
        $this->assertEquals('bobby', $row[1]->getValue());
    }

    public function testRemoveRowWithReIndex()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby']);
        $this->table->removeRow(0, true);

        $this->assertCount(1, $this->table->getTable());

        $row = $this->table->getRow(0);
        $this->assertEquals('bob', $row[0]->getValue());
        $this->assertEquals('bobby', $row[1]->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetColumnWithInvalidIndex()
    {
        $this->table->getColumn('foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetColumnWithOutOfBoundsIndex()
    {
        $this->table->getColumn(999999);
    }

    public function testSetAndGetColumnWithDefaultConfiguration()
    {
        $data = ['foo', 'bar', 'baz'];

        $this->table->setColumn($data);
        $column = $this->table->getColumn(0);

        foreach ($column as $key => $cell) {
            $this->assertEquals($data[$key], $cell->getValue());
        }
    }

    public function testSetColumnWithSingleValue()
    {
        $this->table->setColumn('foo');
        $column = $this->table->getColumn(0);

        foreach ($column as $key => $cell) {
            $this->assertEquals('foo', $cell->getValue());
        }
    }

    public function testSetColumnWithAnEmptyValue()
    {
        $this->table->setColumn(['foo', null, 'bar']);
        $column = $this->table->getColumn(0);

        for ($i = 0; $i < 3; $i++) {
            if ($i === 1) {
                $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $column[$i]);
            } else {
                $this->assertInstanceOf('ExcelAnt\Cell\Cell', $column[$i]);
            }
        }
    }

    public function testSetColumnWhenThereAreAlreadyRows()
    {
        $data = ['col1', 'col2', 'col3'];

        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby']);

        $this->table->setColumn($data);
        $column = $this->table->getColumn(3);

        foreach ($column as $key => $cell) {
            $this->assertEquals($data[$key], $cell->getValue());
        }
    }

    public function testGetLastColumn()
    {
        $this->table->setColumn(['foo', 'bar', 'baz']);
        $this->table->setColumn(['foo', 'bar', 'baz']);

        $this->assertEquals(1, $this->table->getLastColumn());
    }

    public function testGetLastColumnWithRows()
    {
        $this->table->setRow(['foo', 'bar', 'baz']);
        $this->table->setRow(['bob', 'bobby']);

        $this->assertEquals(2, $this->table->getLastColumn());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCleanColumnWithInvalidIndex()
    {
        $this->table->cleanColumn('foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testCleanColumnWithOutOfBoundsIndex()
    {
        $this->table->cleanColumn(999999);
    }

    public function testCleanColumn()
    {
        $this->table->setColumn(['foo', 'bar', 'baz']);
        $this->table->cleanColumn(0);
        $column = $this->table->getColumn(0);

        $this->assertCount(3, $column);

        foreach ($column as $cell) {
            $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $cell);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRemoveColumnWithInvalidIndex()
    {
        $this->table->removeColumn('foo');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testRemoveColumnWithOutOfBoundsIndex()
    {
        $this->table->removeColumn(999999);
    }

    public function testRemoveColumn()
    {
        $this->table->setColumn(['foo', 'bar', 'baz']);
        $this->table->setColumn(['foo', 'bar', 'baz']);
        $this->table->setColumn(['foo', 'bar', 'baz']);
        $this->table->removeColumn(1);

        $row = $this->table->getRow(0);
        $this->assertFalse(array_key_exists(1, $row));
        $row = $this->table->getRow(1);
        $this->assertFalse(array_key_exists(1, $row));
        $row = $this->table->getRow(2);
        $this->assertFalse(array_key_exists(1, $row));
    }

    public function testBigArray()
    {
        $this->table->setRow(['foo', null, null, 'bar', 'baz', null]);
        $this->table->setRow(['foo', 'bar']);
        $this->table->setRow(['foo', null, null, 'bar']);
        $this->table->setRow(['foo', null, 'bar', 'baz']);

        $this->table->cleanRow(1);

        $this->table->setColumn(['foo', 'bar', null, 'baz']);
        $this->table->setColumn(['foo', 'bar', null, 'baz'], 0);
        $this->table->setColumn([null, 'foo', null, 'baz']);

        $row = $this->table->getRow(0);
        $this->assertCount(8, $row);
        $this->assertEquals('foo', $row[0]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[1]);
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[2]);
        $this->assertEquals('bar', $row[3]->getValue());
        $this->assertEquals('baz', $row[4]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[5]);
        $this->assertEquals('foo', $row[6]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[7]);

        $row = $this->table->getRow(1);
        $this->assertCount(4, $row);
        $this->assertEquals('bar', $row[0]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[1]);
        $this->assertEquals('bar', $row[6]->getValue());
        $this->assertEquals('foo', $row[7]->getValue());

        $row = $this->table->getRow(2);
        $this->assertCount(6, $row);
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[0]);
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[1]);
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[2]);
        $this->assertEquals('bar', $row[3]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[6]);
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[7]);

        $row = $this->table->getRow(3);
        $this->assertCount(6, $row);
        $this->assertEquals('baz', $row[0]->getValue());
        $this->assertInstanceOf('ExcelAnt\Cell\EmptyCell', $row[1]);
        $this->assertEquals('bar', $row[2]->getValue());
        $this->assertEquals('baz', $row[3]->getValue());
        $this->assertEquals('baz', $row[6]->getValue());
        $this->assertEquals('baz', $row[7]->getValue());
    }
}