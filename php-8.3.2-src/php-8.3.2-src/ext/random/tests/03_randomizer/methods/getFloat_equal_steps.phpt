--TEST--
Random: Randomizer: getFloat(): Returned floats have equal distance.
--FILE--
<?php

use Random\Engine;
use Random\Engine\Test\TestCountingEngine64;
use Random\IntervalBoundary;
use Random\Randomizer;

require __DIR__ . "/../../engines.inc";

function run_test(Randomizer $r, float $min, float $max, int $count) {
    printf("[%.17g, %.17g]\n", $min, $max);

    $prev = null;
    for ($i = 0; $i < $count; $i++) {
        $float = $r->getFloat($min, $max, IntervalBoundary::ClosedClosed);
        printf("%.17f", $float);
        if ($prev !== null) {
            printf(" (%+.17g)", ($float - $prev));

            if ($prev < $float) {
                printf("\nRepeat");
            }
        }
        printf("\n");

        $prev = $float;
    }
}

run_test(
    new Randomizer(new TestCountingEngine64()),
    1.99999999999999,
    2.00000000000001,
    150,
);

echo "==================", PHP_EOL, PHP_EOL;

run_test(
    new Randomizer(new TestCountingEngine64()),
    1.99999999999999,
    2.00000000000000,
    150,
);

echo "==================", PHP_EOL, PHP_EOL;

run_test(
    new Randomizer(new TestCountingEngine64()),
    2.00000000000000,
    2.00000000000001,
    60,
);

?>
--EXPECT--
[1.99999999999999, 2.0000000000000102]
2.00000000000001021
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
2.00000000000000622 (-4.4408920985006262e-16)
2.00000000000000577 (-4.4408920985006262e-16)
2.00000000000000533 (-4.4408920985006262e-16)
2.00000000000000488 (-4.4408920985006262e-16)
2.00000000000000444 (-4.4408920985006262e-16)
2.00000000000000400 (-4.4408920985006262e-16)
2.00000000000000355 (-4.4408920985006262e-16)
2.00000000000000311 (-4.4408920985006262e-16)
2.00000000000000266 (-4.4408920985006262e-16)
2.00000000000000222 (-4.4408920985006262e-16)
2.00000000000000178 (-4.4408920985006262e-16)
2.00000000000000133 (-4.4408920985006262e-16)
2.00000000000000089 (-4.4408920985006262e-16)
2.00000000000000044 (-4.4408920985006262e-16)
2.00000000000000000 (-4.4408920985006262e-16)
1.99999999999999956 (-4.4408920985006262e-16)
1.99999999999999911 (-4.4408920985006262e-16)
1.99999999999999867 (-4.4408920985006262e-16)
1.99999999999999822 (-4.4408920985006262e-16)
1.99999999999999778 (-4.4408920985006262e-16)
1.99999999999999734 (-4.4408920985006262e-16)
1.99999999999999689 (-4.4408920985006262e-16)
1.99999999999999645 (-4.4408920985006262e-16)
1.99999999999999600 (-4.4408920985006262e-16)
1.99999999999999556 (-4.4408920985006262e-16)
1.99999999999999512 (-4.4408920985006262e-16)
1.99999999999999467 (-4.4408920985006262e-16)
1.99999999999999423 (-4.4408920985006262e-16)
1.99999999999999378 (-4.4408920985006262e-16)
1.99999999999999334 (-4.4408920985006262e-16)
1.99999999999999289 (-4.4408920985006262e-16)
1.99999999999999245 (-4.4408920985006262e-16)
1.99999999999999201 (-4.4408920985006262e-16)
1.99999999999999156 (-4.4408920985006262e-16)
1.99999999999999112 (-4.4408920985006262e-16)
1.99999999999999067 (-4.4408920985006262e-16)
1.99999999999999023 (-4.4408920985006262e-16)
1.99999999999999001 (-2.2204460492503131e-16)
2.00000000000001021 (+2.0206059048177849e-14)
Repeat
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
2.00000000000000622 (-4.4408920985006262e-16)
2.00000000000000577 (-4.4408920985006262e-16)
2.00000000000000533 (-4.4408920985006262e-16)
2.00000000000000488 (-4.4408920985006262e-16)
2.00000000000000444 (-4.4408920985006262e-16)
2.00000000000000400 (-4.4408920985006262e-16)
2.00000000000000355 (-4.4408920985006262e-16)
2.00000000000000311 (-4.4408920985006262e-16)
2.00000000000000266 (-4.4408920985006262e-16)
2.00000000000000222 (-4.4408920985006262e-16)
2.00000000000000178 (-4.4408920985006262e-16)
2.00000000000000133 (-4.4408920985006262e-16)
2.00000000000000089 (-4.4408920985006262e-16)
2.00000000000000044 (-4.4408920985006262e-16)
2.00000000000000000 (-4.4408920985006262e-16)
1.99999999999999956 (-4.4408920985006262e-16)
1.99999999999999911 (-4.4408920985006262e-16)
1.99999999999999867 (-4.4408920985006262e-16)
1.99999999999999822 (-4.4408920985006262e-16)
1.99999999999999778 (-4.4408920985006262e-16)
1.99999999999999734 (-4.4408920985006262e-16)
1.99999999999999689 (-4.4408920985006262e-16)
1.99999999999999645 (-4.4408920985006262e-16)
1.99999999999999600 (-4.4408920985006262e-16)
1.99999999999999556 (-4.4408920985006262e-16)
1.99999999999999512 (-4.4408920985006262e-16)
1.99999999999999467 (-4.4408920985006262e-16)
1.99999999999999423 (-4.4408920985006262e-16)
1.99999999999999378 (-4.4408920985006262e-16)
1.99999999999999334 (-4.4408920985006262e-16)
1.99999999999999289 (-4.4408920985006262e-16)
1.99999999999999245 (-4.4408920985006262e-16)
1.99999999999999201 (-4.4408920985006262e-16)
1.99999999999999156 (-4.4408920985006262e-16)
1.99999999999999112 (-4.4408920985006262e-16)
1.99999999999999067 (-4.4408920985006262e-16)
1.99999999999999023 (-4.4408920985006262e-16)
1.99999999999999001 (-2.2204460492503131e-16)
2.00000000000001021 (+2.0206059048177849e-14)
Repeat
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
2.00000000000000622 (-4.4408920985006262e-16)
2.00000000000000577 (-4.4408920985006262e-16)
2.00000000000000533 (-4.4408920985006262e-16)
2.00000000000000488 (-4.4408920985006262e-16)
2.00000000000000444 (-4.4408920985006262e-16)
2.00000000000000400 (-4.4408920985006262e-16)
2.00000000000000355 (-4.4408920985006262e-16)
2.00000000000000311 (-4.4408920985006262e-16)
2.00000000000000266 (-4.4408920985006262e-16)
2.00000000000000222 (-4.4408920985006262e-16)
2.00000000000000178 (-4.4408920985006262e-16)
2.00000000000000133 (-4.4408920985006262e-16)
2.00000000000000089 (-4.4408920985006262e-16)
2.00000000000000044 (-4.4408920985006262e-16)
2.00000000000000000 (-4.4408920985006262e-16)
1.99999999999999956 (-4.4408920985006262e-16)
1.99999999999999911 (-4.4408920985006262e-16)
1.99999999999999867 (-4.4408920985006262e-16)
1.99999999999999822 (-4.4408920985006262e-16)
1.99999999999999778 (-4.4408920985006262e-16)
1.99999999999999734 (-4.4408920985006262e-16)
1.99999999999999689 (-4.4408920985006262e-16)
1.99999999999999645 (-4.4408920985006262e-16)
1.99999999999999600 (-4.4408920985006262e-16)
1.99999999999999556 (-4.4408920985006262e-16)
1.99999999999999512 (-4.4408920985006262e-16)
1.99999999999999467 (-4.4408920985006262e-16)
1.99999999999999423 (-4.4408920985006262e-16)
1.99999999999999378 (-4.4408920985006262e-16)
1.99999999999999334 (-4.4408920985006262e-16)
1.99999999999999289 (-4.4408920985006262e-16)
1.99999999999999245 (-4.4408920985006262e-16)
1.99999999999999201 (-4.4408920985006262e-16)
1.99999999999999156 (-4.4408920985006262e-16)
1.99999999999999112 (-4.4408920985006262e-16)
1.99999999999999067 (-4.4408920985006262e-16)
1.99999999999999023 (-4.4408920985006262e-16)
1.99999999999999001 (-2.2204460492503131e-16)
2.00000000000001021 (+2.0206059048177849e-14)
Repeat
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
==================

[1.99999999999999, 2]
2.00000000000000000
1.99999999999999978 (-2.2204460492503131e-16)
1.99999999999999956 (-2.2204460492503131e-16)
1.99999999999999933 (-2.2204460492503131e-16)
1.99999999999999911 (-2.2204460492503131e-16)
1.99999999999999889 (-2.2204460492503131e-16)
1.99999999999999867 (-2.2204460492503131e-16)
1.99999999999999845 (-2.2204460492503131e-16)
1.99999999999999822 (-2.2204460492503131e-16)
1.99999999999999800 (-2.2204460492503131e-16)
1.99999999999999778 (-2.2204460492503131e-16)
1.99999999999999756 (-2.2204460492503131e-16)
1.99999999999999734 (-2.2204460492503131e-16)
1.99999999999999711 (-2.2204460492503131e-16)
1.99999999999999689 (-2.2204460492503131e-16)
1.99999999999999667 (-2.2204460492503131e-16)
1.99999999999999645 (-2.2204460492503131e-16)
1.99999999999999623 (-2.2204460492503131e-16)
1.99999999999999600 (-2.2204460492503131e-16)
1.99999999999999578 (-2.2204460492503131e-16)
1.99999999999999556 (-2.2204460492503131e-16)
1.99999999999999534 (-2.2204460492503131e-16)
1.99999999999999512 (-2.2204460492503131e-16)
1.99999999999999489 (-2.2204460492503131e-16)
1.99999999999999467 (-2.2204460492503131e-16)
1.99999999999999445 (-2.2204460492503131e-16)
1.99999999999999423 (-2.2204460492503131e-16)
1.99999999999999400 (-2.2204460492503131e-16)
1.99999999999999378 (-2.2204460492503131e-16)
1.99999999999999356 (-2.2204460492503131e-16)
1.99999999999999334 (-2.2204460492503131e-16)
1.99999999999999312 (-2.2204460492503131e-16)
1.99999999999999289 (-2.2204460492503131e-16)
1.99999999999999267 (-2.2204460492503131e-16)
1.99999999999999245 (-2.2204460492503131e-16)
1.99999999999999223 (-2.2204460492503131e-16)
1.99999999999999201 (-2.2204460492503131e-16)
1.99999999999999178 (-2.2204460492503131e-16)
1.99999999999999156 (-2.2204460492503131e-16)
1.99999999999999134 (-2.2204460492503131e-16)
1.99999999999999112 (-2.2204460492503131e-16)
1.99999999999999090 (-2.2204460492503131e-16)
1.99999999999999067 (-2.2204460492503131e-16)
1.99999999999999045 (-2.2204460492503131e-16)
1.99999999999999023 (-2.2204460492503131e-16)
1.99999999999999001 (-2.2204460492503131e-16)
2.00000000000000000 (+9.9920072216264089e-15)
Repeat
1.99999999999999978 (-2.2204460492503131e-16)
1.99999999999999956 (-2.2204460492503131e-16)
1.99999999999999933 (-2.2204460492503131e-16)
1.99999999999999911 (-2.2204460492503131e-16)
1.99999999999999889 (-2.2204460492503131e-16)
1.99999999999999867 (-2.2204460492503131e-16)
1.99999999999999845 (-2.2204460492503131e-16)
1.99999999999999822 (-2.2204460492503131e-16)
1.99999999999999800 (-2.2204460492503131e-16)
1.99999999999999778 (-2.2204460492503131e-16)
1.99999999999999756 (-2.2204460492503131e-16)
1.99999999999999734 (-2.2204460492503131e-16)
1.99999999999999711 (-2.2204460492503131e-16)
1.99999999999999689 (-2.2204460492503131e-16)
1.99999999999999667 (-2.2204460492503131e-16)
1.99999999999999645 (-2.2204460492503131e-16)
1.99999999999999623 (-2.2204460492503131e-16)
1.99999999999999600 (-2.2204460492503131e-16)
1.99999999999999578 (-2.2204460492503131e-16)
1.99999999999999556 (-2.2204460492503131e-16)
1.99999999999999534 (-2.2204460492503131e-16)
1.99999999999999512 (-2.2204460492503131e-16)
1.99999999999999489 (-2.2204460492503131e-16)
1.99999999999999467 (-2.2204460492503131e-16)
1.99999999999999445 (-2.2204460492503131e-16)
1.99999999999999423 (-2.2204460492503131e-16)
1.99999999999999400 (-2.2204460492503131e-16)
1.99999999999999378 (-2.2204460492503131e-16)
1.99999999999999356 (-2.2204460492503131e-16)
1.99999999999999334 (-2.2204460492503131e-16)
1.99999999999999312 (-2.2204460492503131e-16)
1.99999999999999289 (-2.2204460492503131e-16)
1.99999999999999267 (-2.2204460492503131e-16)
1.99999999999999245 (-2.2204460492503131e-16)
1.99999999999999223 (-2.2204460492503131e-16)
1.99999999999999201 (-2.2204460492503131e-16)
1.99999999999999178 (-2.2204460492503131e-16)
1.99999999999999156 (-2.2204460492503131e-16)
1.99999999999999134 (-2.2204460492503131e-16)
1.99999999999999112 (-2.2204460492503131e-16)
1.99999999999999090 (-2.2204460492503131e-16)
1.99999999999999067 (-2.2204460492503131e-16)
1.99999999999999045 (-2.2204460492503131e-16)
1.99999999999999023 (-2.2204460492503131e-16)
1.99999999999999001 (-2.2204460492503131e-16)
2.00000000000000000 (+9.9920072216264089e-15)
Repeat
1.99999999999999978 (-2.2204460492503131e-16)
1.99999999999999956 (-2.2204460492503131e-16)
1.99999999999999933 (-2.2204460492503131e-16)
1.99999999999999911 (-2.2204460492503131e-16)
1.99999999999999889 (-2.2204460492503131e-16)
1.99999999999999867 (-2.2204460492503131e-16)
1.99999999999999845 (-2.2204460492503131e-16)
1.99999999999999822 (-2.2204460492503131e-16)
1.99999999999999800 (-2.2204460492503131e-16)
1.99999999999999778 (-2.2204460492503131e-16)
1.99999999999999756 (-2.2204460492503131e-16)
1.99999999999999734 (-2.2204460492503131e-16)
1.99999999999999711 (-2.2204460492503131e-16)
1.99999999999999689 (-2.2204460492503131e-16)
1.99999999999999667 (-2.2204460492503131e-16)
1.99999999999999645 (-2.2204460492503131e-16)
1.99999999999999623 (-2.2204460492503131e-16)
1.99999999999999600 (-2.2204460492503131e-16)
1.99999999999999578 (-2.2204460492503131e-16)
1.99999999999999556 (-2.2204460492503131e-16)
1.99999999999999534 (-2.2204460492503131e-16)
1.99999999999999512 (-2.2204460492503131e-16)
1.99999999999999489 (-2.2204460492503131e-16)
1.99999999999999467 (-2.2204460492503131e-16)
1.99999999999999445 (-2.2204460492503131e-16)
1.99999999999999423 (-2.2204460492503131e-16)
1.99999999999999400 (-2.2204460492503131e-16)
1.99999999999999378 (-2.2204460492503131e-16)
1.99999999999999356 (-2.2204460492503131e-16)
1.99999999999999334 (-2.2204460492503131e-16)
1.99999999999999312 (-2.2204460492503131e-16)
1.99999999999999289 (-2.2204460492503131e-16)
1.99999999999999267 (-2.2204460492503131e-16)
1.99999999999999245 (-2.2204460492503131e-16)
1.99999999999999223 (-2.2204460492503131e-16)
1.99999999999999201 (-2.2204460492503131e-16)
1.99999999999999178 (-2.2204460492503131e-16)
1.99999999999999156 (-2.2204460492503131e-16)
1.99999999999999134 (-2.2204460492503131e-16)
1.99999999999999112 (-2.2204460492503131e-16)
1.99999999999999090 (-2.2204460492503131e-16)
1.99999999999999067 (-2.2204460492503131e-16)
1.99999999999999045 (-2.2204460492503131e-16)
1.99999999999999023 (-2.2204460492503131e-16)
1.99999999999999001 (-2.2204460492503131e-16)
2.00000000000000000 (+9.9920072216264089e-15)
Repeat
1.99999999999999978 (-2.2204460492503131e-16)
1.99999999999999956 (-2.2204460492503131e-16)
1.99999999999999933 (-2.2204460492503131e-16)
1.99999999999999911 (-2.2204460492503131e-16)
1.99999999999999889 (-2.2204460492503131e-16)
1.99999999999999867 (-2.2204460492503131e-16)
1.99999999999999845 (-2.2204460492503131e-16)
1.99999999999999822 (-2.2204460492503131e-16)
1.99999999999999800 (-2.2204460492503131e-16)
1.99999999999999778 (-2.2204460492503131e-16)
1.99999999999999756 (-2.2204460492503131e-16)
==================

[2, 2.0000000000000102]
2.00000000000001021
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
2.00000000000000622 (-4.4408920985006262e-16)
2.00000000000000577 (-4.4408920985006262e-16)
2.00000000000000533 (-4.4408920985006262e-16)
2.00000000000000488 (-4.4408920985006262e-16)
2.00000000000000444 (-4.4408920985006262e-16)
2.00000000000000400 (-4.4408920985006262e-16)
2.00000000000000355 (-4.4408920985006262e-16)
2.00000000000000311 (-4.4408920985006262e-16)
2.00000000000000266 (-4.4408920985006262e-16)
2.00000000000000222 (-4.4408920985006262e-16)
2.00000000000000178 (-4.4408920985006262e-16)
2.00000000000000133 (-4.4408920985006262e-16)
2.00000000000000089 (-4.4408920985006262e-16)
2.00000000000000044 (-4.4408920985006262e-16)
2.00000000000000000 (-4.4408920985006262e-16)
2.00000000000001021 (+1.021405182655144e-14)
Repeat
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
2.00000000000000622 (-4.4408920985006262e-16)
2.00000000000000577 (-4.4408920985006262e-16)
2.00000000000000533 (-4.4408920985006262e-16)
2.00000000000000488 (-4.4408920985006262e-16)
2.00000000000000444 (-4.4408920985006262e-16)
2.00000000000000400 (-4.4408920985006262e-16)
2.00000000000000355 (-4.4408920985006262e-16)
2.00000000000000311 (-4.4408920985006262e-16)
2.00000000000000266 (-4.4408920985006262e-16)
2.00000000000000222 (-4.4408920985006262e-16)
2.00000000000000178 (-4.4408920985006262e-16)
2.00000000000000133 (-4.4408920985006262e-16)
2.00000000000000089 (-4.4408920985006262e-16)
2.00000000000000044 (-4.4408920985006262e-16)
2.00000000000000000 (-4.4408920985006262e-16)
2.00000000000001021 (+1.021405182655144e-14)
Repeat
2.00000000000000977 (-4.4408920985006262e-16)
2.00000000000000933 (-4.4408920985006262e-16)
2.00000000000000888 (-4.4408920985006262e-16)
2.00000000000000844 (-4.4408920985006262e-16)
2.00000000000000799 (-4.4408920985006262e-16)
2.00000000000000755 (-4.4408920985006262e-16)
2.00000000000000711 (-4.4408920985006262e-16)
2.00000000000000666 (-4.4408920985006262e-16)
2.00000000000000622 (-4.4408920985006262e-16)
2.00000000000000577 (-4.4408920985006262e-16)
2.00000000000000533 (-4.4408920985006262e-16)
