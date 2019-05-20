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

namespace CoreShop\Payum\Heidelpay\Action;

use CoreShop\Payum\Heidelpay\Api;
use CoreShop\Payum\Heidelpay\Request\Api\HeidelpayCapture;
use CoreShop\Payum\Heidelpay\Request\Api\ObtainToken;
use CoreShop\Payum\Heidelpay\Request\Api\PopulateHeidelpay;
use Heidelpay\PhpPaymentApi\PaymentMethods\CreditCardPaymentMethod;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryAwareTrait;

/**
 * @property Api $api
 */
class CaptureAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface, GenericTokenFactoryAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;
    use GenericTokenFactoryAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['paymentReferenceId']) {
            return;
        }

        $api = $this->api->getApi();
        $api->getRequest()->authentification(
            $this->api->getOption('securitySender'),
            $this->api->getOption('userLogin'),
            $this->api->getOption('userPassword'),
            $this->api->getOption('transactionChannel'),
            $this->api->getOption('sandboxMode')
        );

        $notifyToken = $this->tokenFactory->createNotifyToken(
            $request->getToken()->getGatewayName(),
            $request->getToken()->getDetails()
        );

        $api->getRequest()->async(
            strtoupper(substr($model['language'],0 , 2)) ?: 'EN',
            $notifyToken->getTargetUrl() . '?afterUrl=' . $request->getToken()->getAfterUrl()
        );

        $this->gateway->execute(new PopulateHeidelpay($request, $api->getRequest()));

        $api->getRequest()->basketData(
            $model['basket']['number'],
            $model['basket']['amount'],
            $model['basket']['currency']
        );

        $this->gateway->execute(new HeidelpayCapture($request, $this->api));

        if ($api->getResponse()->isSuccess()) {
            if ($api instanceof CreditCardPaymentMethod) {
                $obtainToken = new ObtainToken($request->getToken());
                $obtainToken->setModel($model);

                $this->gateway->execute($obtainToken);
            }
            else {
                throw new HttpRedirect(
                    $api->getResponse()->getPaymentFormUrl()
                );
            }
        }

        throw new \Exception($api->getResponse()->getError()['message'], $api->getResponse()->getError()['code']);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess;
    }
}
