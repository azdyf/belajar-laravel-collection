<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class CollectionTest extends TestCase
{
    public function testCreateCollection(): void
    {
        $collection = collect([1, 2, 3]);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        foreach ($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        $collection = collect([]);
        $collection->push(1, 2, 3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        $result = $collection->pop();
        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(['Fahmi']);
        $result = $collection->mapInto(Person::class);
        $this->assertEquals([new Person('Fahmi')], $result->all());

        var_dump($result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['Iman', 'Hasyim'],
            ['Azdy', 'Fahmi']
        ]);

        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = $firstName . ' ' . $lastName;
            return new Person($fullName);
        });

        $this->assertEquals([
            new Person("Iman Hasyim"),
            new Person("Azdy Fahmi"),
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                'name' => 'Fahmi',
                'department' => 'IT'
            ],
            [
                'name' => 'Azdy',
                'department' => 'IT'
            ],
            [
                'name' => 'Hasyim',
                'department' => 'HR'
            ],
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person['department'] => $person['name']
            ];
        });

        var_dump($result->all());

        $this->assertEquals([
            'IT' => collect(['Fahmi', 'Azdy']),
            'HR' => collect(['Hasyim'])
        ], $result->all());
    }

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        var_dump($collection3->all());

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6]),
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);

        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(['name', 'country']);
        $collection2 = collect(['Fahmi', 'Indonesia']);

        $collection3 = $collection1->combine($collection2);

        var_dump($collection3->all());

        $this->assertEqualsCanonicalizing([
            'name' => 'Fahmi',
            'country' => 'Indonesia'
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $result = $collection->collapse();
        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                'name' => 'Fahmi',
                'hobbies' => ['Coding', 'Reading']
            ],
            [
                'name' => 'Azdy',
                'hobbies' => ['Writing', 'Gaming']
            ]
        ]);

        $result = $collection->flatMap(function ($item) {
            $hobbies = $item['hobbies'];
            return $hobbies;
        });

        $this->assertEqualsCanonicalizing(['Coding', 'Reading', 'Writing', 'Gaming'], $result->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(['Azdy', 'Fahmi', 'Hasyim']);

        $this->assertEquals('Azdy-Fahmi-Hasyim', $collection->join('-'));
        $this->assertEquals('Azdy-Fahmi_Hasyim', $collection->join('-', finalGlue: '_'));
        $this->assertEquals('Azdy, Fahmi and Hasyim', $collection->join(', ', finalGlue: ' and '));
    }

    public function testFilter()
    {
        $collection = collect([
            'Azdy' => 100,
            'Gibran' => 80,
            'Azkan' => 90,
        ]);

        $result = $collection->filter(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            'Azdy' => 100,
            'Azkan' => 90
        ], $result->all());
    }

    public function testFilterIndex()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        $result = $collection->filter(function ($value, $key) {
            return $value % 2 == 0;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            'Azdy' => 100,
            'Gibran' => 80,
            'Azkan' => 90,
        ]);

        [$result1, $result2] = $collection->partition(function ($value, $key) {
            return $value >= 90;
        });

        $this->assertEquals([
            'Azdy' => 100,
            'Azkan' => 90
        ], $result1->all());

        $this->assertEquals([
            'Gibran' => 80
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(['Azdy', 'Fahmi', 'Hasyim']);
        $this->assertTrue($collection->contains('Fahmi'));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == 'Hasyim';
        }));
    }

    public function testGrouping()
    {
        $collection = collect([
            [
                'name' => 'Azdy',
                'department' => 'IT'
            ],
            [
                'name' => 'Fahmi',
                'department' => 'IT'
            ],
            [
                'name' => 'Azkan',
                'department' => 'HR'
            ],
        ]);

        $result = $collection->groupBy('department');

        var_dump($result->all());

        $this->assertEquals([
            'IT' => collect([
                [
                    'name' => 'Azdy',
                    'department' => 'IT'
                ],
                [
                    'name' => 'Fahmi',
                    'department' => 'IT'
                ]
            ]),
            'HR' => collect([
                [
                    'name' => 'Azkan',
                    'department' => 'HR'
                ]
            ])
        ], $result->all());

        $result = $collection->groupBy(function ($value, $key) {
            return strtolower($value['department']);
        });

        $this->assertEquals([
            'it' => collect([
                [
                    'name' => 'Azdy',
                    'department' => 'IT'
                ],
                [
                    'name' => 'Fahmi',
                    'department' => 'IT'
                ]
            ]),
            'hr' => collect([
                [
                    'name' => 'Azkan',
                    'department' => 'HR'
                ]
            ])
        ], $result->all());
    }
}
