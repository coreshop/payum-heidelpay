<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Payum\Heidelpay\Action\Api;

use CoreShop\Payum\Heidelpay\Request\Api\HeidelpayCapture;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;

class HeidelpayPayPalCaptureAction implements ActionInterface
{
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        if (!$request instanceof HeidelpayCapture) {
            return;
        }

        $request->getApi()->getApi()->debit();
    }

    public function supports($request)
    {
        return $request instanceof HeidelpayCapture &&
                $request->getApi()->getType() === 'PayPal';
    }
}