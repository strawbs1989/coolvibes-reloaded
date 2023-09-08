<?php

declare(strict_types=1);

namespace PhpMyAdmin;

use const VERSION_SUFFIX;

/**
 * This class is generated by scripts/console.
 *
 * @see \PhpMyAdmin\Command\SetVersionCommand
 */
final class Version
{
    // The VERSION_SUFFIX constant is defined at libraries/constants.php
    public const VERSION = '6.0.0-dev' . VERSION_SUFFIX;
    public const SERIES = '6.0';
    public const MAJOR = 6;
    public const MINOR = 0;
    public const PATCH = 0;
    public const ID = 60000;
    public const PRE_RELEASE_NAME = 'dev';
    public const IS_DEV = true;
}
