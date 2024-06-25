<?php

namespace BethlehemIT\HyvaSendCloud\Observer;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class ShippingMethodSelected implements ObserverInterface
{
    public function __construct(
        protected readonly SessionCheckout $sessionCheckout,
        protected readonly CartRepositoryInterface $quoteRepository
    ) {
    }

    public function execute(Observer $observer)
    {
        $quote = $this->sessionCheckout->getQuote();
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress->getShippingMethod() !== "sendcloud_sendcloud") {
            if (!empty($quote->getSendcloudServicePointId())) {
                $quote->setSendcloudServicePointId(null);
                $this->quoteRepository->save($quote);
            }
        }
    }
}
