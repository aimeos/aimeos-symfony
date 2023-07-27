<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @copyright Aimeos (aimeos.org), 2014-2023
 */


namespace Aimeos\ShopBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;


class AimeosShopBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname( __DIR__ );
    }
}
