<?php

namespace BethlehemIT\HyvaSendCloud\Model\Form\EntityFormModifier;

use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Quote\Api\Data\AddressInterface;

class SendcloudModifier implements EntityFormModifierInterface
{
    public function __construct(
        protected SessionCheckout $sessionCheckout
    ) {
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            "sendcloudSet",
            "form:build",
            [$this, "sendcloudSet"]
        );

        return $form;
    }

    public function sendcloudSet(EntityFormInterface $form): void
    {
        $quote = $this->sessionCheckout->getQuote();
        if (!empty($quote->getSendcloudServicePointId())) {
            $form->getField(AddressInterface::KEY_POSTCODE)->disable();
            $street = $form->getField(AddressInterface::KEY_STREET);
            $street->disable();
            foreach ($street->getRelatives() as $relative) {
                $relative->disable();
            }
            $form->getField(AddressInterface::KEY_FIRSTNAME)->disable();
            $form->getField(AddressInterface::KEY_LASTNAME)->disable();
            $form->getField(AddressInterface::KEY_COUNTRY_ID)->disable();
            $form->getField(AddressInterface::KEY_TELEPHONE)->disable();
            $form->getField(AddressInterface::KEY_COMPANY)->disable();
            $form->getField(AddressInterface::KEY_CITY)->disable();
        }
    }
}
