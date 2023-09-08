<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Gis;

use PhpMyAdmin\Gis\Ds\Extent;
use PhpMyAdmin\Gis\Ds\ScaleData;
use PhpMyAdmin\Gis\GisMultiPolygon;
use PhpMyAdmin\Image\ImageWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use TCPDF;

#[CoversClass(GisMultiPolygon::class)]
#[PreserveGlobalState(false)]
#[RunTestsInSeparateProcesses]
class GisMultiPolygonTest extends GisGeomTestCase
{
    /**
     * Provide some common data to data providers
     *
     * @return mixed[][]
     */
    private static function getData(): array
    {
        return [
            'MULTIPOLYGON' => [
                'no_of_polygons' => 2,
                0 => [
                    'no_of_lines' => 2,
                    0 => [
                        'no_of_points' => 5,
                        0 => ['x' => 35, 'y' => 10],
                        1 => ['x' => 10, 'y' => 20],
                        2 => ['x' => 15, 'y' => 40],
                        3 => ['x' => 45, 'y' => 45],
                        4 => ['x' => 35, 'y' => 10],
                    ],
                    1 => [
                        'no_of_points' => 4,
                        0 => ['x' => 20, 'y' => 30],
                        1 => ['x' => 35, 'y' => 32],
                        2 => ['x' => 30, 'y' => 20],
                        3 => ['x' => 20, 'y' => 30],
                    ],
                ],
                1 => [
                    'no_of_lines' => 1,
                    0 => [
                        'no_of_points' => 4,
                        0 => ['x' => 123, 'y' => 0],
                        1 => ['x' => 23, 'y' => 30],
                        2 => ['x' => 17, 'y' => 63],
                        3 => ['x' => 123, 'y' => 0],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test for generateWkt
     *
     * @param array<mixed> $gisData
     * @param int          $index   index in $gis_data
     * @param string|null  $empty   empty parameter
     * @param string       $output  expected output
     */
    #[DataProvider('providerForTestGenerateWkt')]
    public function testGenerateWkt(array $gisData, int $index, string|null $empty, string $output): void
    {
        $object = GisMultiPolygon::singleton();
        $this->assertEquals($output, $object->generateWkt($gisData, $index, $empty));
    }

    /**
     * data provider for testGenerateWkt
     *
     * @return array<array{array<mixed>, int, string|null, string}>
     */
    public static function providerForTestGenerateWkt(): array
    {
        $temp = [0 => self::getData()];

        $temp1 = $temp;
        $temp1[0]['MULTIPOLYGON']['no_of_polygons'] = 0;

        $temp2 = $temp;
        $temp2[0]['MULTIPOLYGON'][1]['no_of_lines'] = 0;

        $temp3 = $temp;
        $temp3[0]['MULTIPOLYGON'][1][0]['no_of_points'] = 3;

        return [
            [
                $temp,
                0,
                null,
                'MULTIPOLYGON(((35 10,10 20,15 40,45 45,35 10)'
                    . ',(20 30,35 32,30 20,20 30)),((123 0,23 30,17 63,123 0)))',
            ],
            // at lease one polygon should be there
            [$temp1, 0, null, 'MULTIPOLYGON(((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20,20 30)))'],
            // a polygon should have at least one ring
            [
                $temp2,
                0,
                null,
                'MULTIPOLYGON(((35 10,10 20,15 40,45 45,35 10)'
                    . ',(20 30,35 32,30 20,20 30)),((123 0,23 30,17 63,123 0)))',
            ],
            // a ring should have at least four points
            [
                $temp3,
                0,
                '0',
                'MULTIPOLYGON(((35 10,10 20,15 40,45 45,35 10)'
                    . ',(20 30,35 32,30 20,20 30)),((123 0,23 30,17 63,123 0)))',
            ],
        ];
    }

    /**
     * test generateParams method
     *
     * @param string       $wkt    point in WKT form
     * @param array<mixed> $params expected output array
     */
    #[DataProvider('providerForTestGenerateParams')]
    public function testGenerateParams(string $wkt, array $params): void
    {
        $object = GisMultiPolygon::singleton();
        $this->assertEquals($params, $object->generateParams($wkt));
    }

    /**
     * data provider for testGenerateParams
     *
     * @return array<array{string, array<mixed>}>
     */
    public static function providerForTestGenerateParams(): array
    {
        return [
            [
                "'MULTIPOLYGON(((35 10,10 20,15 40,45 45,35 10),"
                . "(20 30,35 32,30 20,20 30)),((123 0,23 30,17 63,123 0)))',124",
                ['srid' => 124, 0 => self::getData()],
            ],
        ];
    }

    /**
     * test getShape method
     *
     * @param mixed[] $rowData array of GIS data
     * @param string  $shape   expected shape in WKT
     */
    #[DataProvider('providerForTestGetShape')]
    public function testGetShape(array $rowData, string $shape): void
    {
        $object = GisMultiPolygon::singleton();
        $this->assertEquals($shape, $object->getShape($rowData));
    }

    /**
     * data provider for testGetShape
     *
     * @return array<array{mixed[], string}>
     */
    public static function providerForTestGetShape(): array
    {
        return [
            [
                [
                    'parts' => [
                        0 => [
                            'points' => [
                                0 => ['x' => 10, 'y' => 10],
                                1 => ['x' => 10, 'y' => 40],
                                2 => ['x' => 50, 'y' => 40],
                                3 => ['x' => 50, 'y' => 10],
                                4 => ['x' => 10, 'y' => 10],
                            ],
                        ],
                        1 => [
                            'points' => [
                                0 => ['x' => 60, 'y' => 40],
                                1 => ['x' => 75, 'y' => 65],
                                2 => ['x' => 90, 'y' => 40],
                                3 => ['x' => 60, 'y' => 40],
                            ],
                        ],
                        2 => [
                            'points' => [
                                0 => ['x' => 20, 'y' => 20],
                                1 => ['x' => 40, 'y' => 20],
                                2 => ['x' => 25, 'y' => 30],
                                3 => ['x' => 20, 'y' => 20],
                            ],
                        ],
                    ],
                ],
                'MULTIPOLYGON(((10 10,10 40,50 40,50 10,10 10),(20 20,40 20,25 30'
                    . ',20 20)),((60 40,75 65,90 40,60 40)))',
            ],
        ];
    }

    /**
     * test getExtent method
     *
     * @param string $spatial spatial data of a row
     * @param Extent $extent  expected results
     */
    #[DataProvider('providerForTestGetExtent')]
    public function testGetExtent(string $spatial, Extent $extent): void
    {
        $object = GisMultiPolygon::singleton();
        $this->assertEquals($extent, $object->getExtent($spatial));
    }

    /**
     * data provider for testGetExtent
     *
     * @return array<array{string, Extent}>
     */
    public static function providerForTestGetExtent(): array
    {
        return [
            [
                'MULTIPOLYGON(((136 40,147 83,16 75,136 40)),((105 0,56 20,78 73,105 0)))',
                new Extent(minX: 16, minY: 0, maxX: 147, maxY: 83),
            ],
            [
                'MULTIPOLYGON(((35 10,10 20,15 40,45 45,35 10),(20 30,35 32,30 20'
                    . ',20 30)),((105 0,56 20,78 73,105 0)))',
                new Extent(minX: 10, minY: 0, maxX: 105, maxY: 73),
            ],
        ];
    }

    #[RequiresPhpExtension('gd')]
    public function testPrepareRowAsPng(): void
    {
        $object = GisMultiPolygon::singleton();
        $image = ImageWrapper::create(200, 124, ['red' => 229, 'green' => 229, 'blue' => 229]);
        $this->assertNotNull($image);
        $object->prepareRowAsPng(
            'MULTIPOLYGON(((5 5,95 5,95 95,5 95,5 5),(10 10,10 40,40 40,40 10,10 10),(60 60,90 60,90 90,60 90,6'
            . '0 60)),((-5 -5,-95 -5,-95 -95,-5 -95,-5 -5),(-10 -10,-10 -40,-40 -40,-40 -10,-10 -10),(-60 -60,-90'
            . ' -60,-90 -90,-60 -90,-60 -60)))',
            'image',
            [176, 46, 224],
            new ScaleData(offsetX: -202, offsetY: -125, scale: 0.50, height: 124),
            $image,
        );
        $this->assertEquals(200, $image->width());
        $this->assertEquals(124, $image->height());

        $fileExpected = $this->testDir . '/multipolygon-expected.png';
        $fileActual = $this->testDir . '/multipolygon-actual.png';
        $this->assertTrue($image->png($fileActual));
        $this->assertFileEquals($fileExpected, $fileActual);
    }

    /**
     * test case for prepareRowAsPdf() method
     *
     * @param string    $spatial   GIS MULTIPOLYGON object
     * @param string    $label     label for the GIS MULTIPOLYGON object
     * @param int[]     $color     color for the GIS MULTIPOLYGON object
     * @param ScaleData $scaleData array containing data related to scaling
     */
    #[DataProvider('providerForPrepareRowAsPdf')]
    public function testPrepareRowAsPdf(
        string $spatial,
        string $label,
        array $color,
        ScaleData $scaleData,
        TCPDF $pdf,
    ): void {
        $object = GisMultiPolygon::singleton();
        $object->prepareRowAsPdf($spatial, $label, $color, $scaleData, $pdf);

        $fileExpected = $this->testDir . '/multipolygon-expected.pdf';
        $this->assertStringEqualsFile($fileExpected, $pdf->Output(dest: 'S'));
    }

    /**
     * data provider for testPrepareRowAsPdf() test case
     *
     * @return array<array{string, string, int[], ScaleData, TCPDF}>
     */
    public static function providerForPrepareRowAsPdf(): array
    {
        return [
            [
                'MULTIPOLYGON(((5 5,95 5,95 95,5 95,5 5),(10 10,10 40,40 40,40 10,10 10),(60 60,90 60,90 90,60 90,6'
                . '0 60)),((-5 -5,-95 -5,-95 -95,-5 -95,-5 -5),(-10 -10,-10 -40,-40 -40,-40 -10,-10 -10),(-60 -60,-90'
                . ' -60,-90 -90,-60 -90,-60 -60)))',
                'pdf',
                [176, 46, 224],
                new ScaleData(offsetX: -110, offsetY: -157, scale: 0.95, height: 297),

                parent::createEmptyPdf('MULTIPOLYGON'),
            ],
        ];
    }

    /**
     * test case for prepareRowAsSvg() method
     *
     * @param string    $spatial   GIS MULTIPOLYGON object
     * @param string    $label     label for the GIS MULTIPOLYGON object
     * @param int[]     $color     color for the GIS MULTIPOLYGON object
     * @param ScaleData $scaleData array containing data related to scaling
     * @param string    $output    expected output
     */
    #[DataProvider('providerForPrepareRowAsSvg')]
    public function testPrepareRowAsSvg(
        string $spatial,
        string $label,
        array $color,
        ScaleData $scaleData,
        string $output,
    ): void {
        $object = GisMultiPolygon::singleton();
        $svg = $object->prepareRowAsSvg($spatial, $label, $color, $scaleData);
        $this->assertEquals($output, $svg);
    }

    /**
     * data provider for testPrepareRowAsSvg() test case
     *
     * @return array<array{string, string, int[], ScaleData, string}>
     */
    public static function providerForPrepareRowAsSvg(): array
    {
        return [
            [
                'MULTIPOLYGON(((5 5,95 5,95 95,5 95,5 5),(10 10,10 40,40 40,40 10,10 10),(60 60,90 60,90 90,60 90,6'
                . '0 60)),((-5 -5,-95 -5,-95 -95,-5 -95,-5 -5),(-10 -10,-10 -40,-40 -40,-40 -10,-10 -10),(-60 -60,-90'
                . ' -60,-90 -90,-60 -90,-60 -60)))',
                'svg',
                [176, 46, 224],
                new ScaleData(offsetX: -50, offsetY: -50, scale: 2, height: 400),
                '<path d=" M 110, 290 L 290, 290 L 290, 110 L 110, 110 Z  M 120, 280 L 120, 220 L 180, 220 L 180, 28'
                . '0 Z  M 220, 180 L 280, 180 L 280, 120 L 220, 120 Z " name="svg" class="multipolygon vector" stroke='
                . '"black" stroke-width="0.5" fill="#b02ee0" fill-rule="evenodd" fill-opacity="0.8" id="svg1234567890"'
                . '/><path d=" M 90, 310 L -90, 310 L -90, 490 L 90, 490 Z  M 80, 320 L 80, 380 L 20, 380 L 20, 320 Z '
                . ' M -20, 420 L -80, 420 L -80, 480 L -20, 480 Z " name="svg" class="multipolygon vector" stroke="bla'
                . 'ck" stroke-width="0.5" fill="#b02ee0" fill-rule="evenodd" fill-opacity="0.8" id="svg1234567890"/>',
            ],
        ];
    }

    /**
     * test case for prepareRowAsOl() method
     *
     * @param string $spatial GIS MULTIPOLYGON object
     * @param int    $srid    spatial reference ID
     * @param string $label   label for the GIS MULTIPOLYGON object
     * @param int[]  $color   color for the GIS MULTIPOLYGON object
     * @param string $output  expected output
     */
    #[DataProvider('providerForPrepareRowAsOl')]
    public function testPrepareRowAsOl(
        string $spatial,
        int $srid,
        string $label,
        array $color,
        string $output,
    ): void {
        $object = GisMultiPolygon::singleton();
        $ol = $object->prepareRowAsOl($spatial, $srid, $label, $color);
        $this->assertEquals($output, $ol);
    }

    /**
     * data provider for testPrepareRowAsOl() test case
     *
     * @return array<array{string, int, string, int[], string}>
     */
    public static function providerForPrepareRowAsOl(): array
    {
        return [
            [
                'MULTIPOLYGON(((136 40,147 83,16 75,136 40)),((105 0,56 20,78 73,105 0)))',
                4326,
                'Ol',
                [176, 46, 224],
                'var feature = new ol.Feature(new ol.geom.MultiPolygon([[[[136,40],[147,83],[16,75]'
                . ',[136,40]]],[[[105,0],[56,20],[78,73],[105,0]]]]).transform(\'EPSG:4326\', \'EPS'
                . 'G:3857\'));feature.setStyle(new ol.style.Style({fill: new ol.style.Fill({"color"'
                . ':[176,46,224,0.8]}),stroke: new ol.style.Stroke({"color":[0,0,0],"width":0.5}),t'
                . 'ext: new ol.style.Text({"text":"Ol"})}));vectorSource.addFeature(feature);',
            ],
        ];
    }
}
