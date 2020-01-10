<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Payum\Heidelpay\Request\Api;

use CoreShop\Payum\Heidelpay\Api;
use Payum\Core\Request\Generic;

class HeidelpayCapture extends Generic
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @param $request
     * @param Api $api
     */
    public function __construct($request, Api $api)
    {
        parent::__construct($request);

        $this->api = $api;
    }

    /**
     * @return Api
     */
    public function getApi()
    {
        return $this->api;
    }
}
