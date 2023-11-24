<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Thor\Common\Types\Collection\Collection;

final class CollectionTest extends TestCase
{

    private static function getCollection(): Collection
    {
        return new Collection([
            'keyA' => 'testA',
            'keyB' => 'testB',
            'keyC' => 'testC',
        ]);
    }

    public function testCollectionInstance(): void
    {
        $this->assertInstanceOf(Collection::class, $this->getCollection());
    }

    public function testStatic(): void
    {
        $collection = $this->getCollection();
        $this->assertSame(
            $collection->toArray(),
            Collection::combine(
                new Collection(['keyA', 'keyB', 'keyC']),
                new Collection(['testA', 'testB', 'testC'])
            )->toArray()
        );
        $this->assertSame(
            ['A' => 1, 'B' => 1, 'C' => 1],
            Collection::fillKeys(
                new Collection(['A', 'B', 'C']),
                1
            )->toArray()
        );
        $this->assertSame(['', '', ''], Collection::fill(0, 3)->toArray());
        $this->assertSame([0, 1, 2, 3], Collection::range(0, 3)->toArray());

    }
}
