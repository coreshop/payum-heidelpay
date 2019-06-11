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

use Heidelpay\PhpPaymentApi\PaymentMethods\CreditCardPaymentMethod;
use Heidelpay\PhpPaymentApi\PaymentMethods\DebitCardPaymentMethod;
use Heidelpay\PhpPaymentApi\PaymentMethods\PayPalPaymentMethod;
use Heidelpay\PhpPaymentApi\PaymentMethods\SofortPaymentMethod;
use Http\Message\MessageFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\HttpClientInterface;

class Api
{
    /**
     * @var mixed
     */
    protected $api;

    /**
     * @var HttpClientInterface
     */
    protected $client;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    const TYPES = [
        'PayPal' => PayPalPaymentMethod::class,
        'Sofort' => SofortPaymentMethod::class,
        'CreditCard' => CreditCardPaymentMethod::class,
        'DebitCard' => DebitCardPaymentMethod::class
    ];

    /**
     * @var array|ArrayObject
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $type;

    public function __construct($type, array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $options = ArrayObject::ensureArrayObject($options);

        $this->type = $type;
        $this->options = $options;
        $this->client = $client;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @param $option
     * @return mixed
     */
    public function getOption($option)
    {
        return $this->options[$option];
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getApi()
    {
        if (null === $this->api) {
            $class = self::TYPES[$this->type];

            $this->api = new $class();
        }

        return $this->api;
    }
}
