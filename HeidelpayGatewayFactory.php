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

namespace CoreShop\Payum\Heidelpay;

use CoreShop\Payum\Heidelpay\Action\Api\HeidelpayCreditCardCaptureAction;
use CoreShop\Payum\Heidelpay\Action\Api\HeidelpayPayPalCaptureAction;
use CoreShop\Payum\Heidelpay\Action\Api\HeidelpaySofortCaptureAction;
use CoreShop\Payum\Heidelpay\Action\Api\ObtainTokenAction;
use CoreShop\Payum\Heidelpay\Action\Api\PopulateHeidelpayAction;
use CoreShop\Payum\Heidelpay\Action\CaptureAction;
use CoreShop\Payum\Heidelpay\Action\ConvertPaymentAction;
use CoreShop\Payum\Heidelpay\Action\NotifyAction;
use CoreShop\Payum\Heidelpay\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class HeidelpayGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'heidelpay',
            'payum.factory_title' => 'Heidelpay',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.api.populate_heidelpay' => new PopulateHeidelpayAction(),
            'payum.action.api.heidelpay_paypal_capture' => new HeidelpayPayPalCaptureAction(),
            'payum.action.api.heidelpay_sofort_capture' => new HeidelpaySofortCaptureAction(),
            'payum.action.api.heidelpay_creditcard_capture' => new HeidelpayCreditCardCaptureAction(),
            'payum.template.obtain_token' => '@PayumHeidelpay/Action/obtain_checkout_token.html.twig',
            'payum.action.api.heidelpay_obtain_token' => function (ArrayObject $config) {
                return new ObtainTokenAction($config['payum.template.obtain_token']);
            },
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandboxMode' => true
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'gatewayType',
                'securitySender',
                'userLogin',
                'userPassword',
                'transactionChannel'
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api(
                    $config['gatewayType'],
                    [
                        'securitySender' => $config['securitySender'],
                        'userLogin' => $config['userLogin'],
                        'userPassword' => $config['userPassword'],
                        'transactionChannel' => $config['transactionChannel'],
                        'sandboxMode' => $config['sandboxMode'],
                    ],
                    $config['payum.http_client'],
                    $config['httplug.message_factory']
                );
            };

            $config['payum.paths'] = array_replace([
            'PayumHeidelpay' => __DIR__.'/Resources/views',
        ], $config['payum.paths'] ?: []);
        }
    }
}
