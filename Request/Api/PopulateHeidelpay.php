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

use Payum\Core\Request\Generic;
use Heidelpay\PhpPaymentApi\Request as HeidelpayRequest;

class PopulateHeidelpay extends Generic
{
    /**
     * @var HeidelpayRequest
     */
    protected $heidelpayRequest;

    /**
     * @param $request
     * @param HeidelpayRequest $heidelpayRequest
     */
    public function __construct($request, HeidelpayRequest $heidelpayRequest)
    {
        parent::__construct($request);

        $this->heidelpayRequest = $heidelpayRequest;
    }

    /**
     * @return HeidelpayRequest
     */
    public function getHeidelpayRequest()
    {
        return $this->heidelpayRequest;
    }
}
