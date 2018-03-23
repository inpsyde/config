<?php
declare(strict_types=1);

namespace Inpsyde\Config\Source;

use Inpsyde\Config\Config;

interface Source extends Config
{

    const SOURCE_ENV = 'environment';
    const SOURCE_WP_OPTION = 'wp_option';
    const SOURCE_WP_SITEOPTION = 'wp_siteoption';
    const SOURCE_CONSTANT = 'constant';
}
