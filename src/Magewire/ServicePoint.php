<?php

namespace BethlehemIT\HyvaSendCloud\Magewire;

use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;

class ServicePoint extends Component implements EvaluationInterface
{
    public ?string $postalCode      = null;
    public ?string $country         = null;
    public array $servicePointData = [
        "sendcloud_service_point_id" => ""
    ];

    protected $listeners = [
        'shipping_address_saved' => 'refresh'
    ];

    /**
     * @param SessionCheckout $sessionCheckout
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        protected SessionCheckout $sessionCheckout,
        protected CartRepositoryInterface $quoteRepository,
    ) {
    }

    public function mount(): void
    {
        $this->servicePointData = [
            "sendcloud_service_point_id" => ""
        ];
    }

    public function boot(): void
    {
        $shippingAddress = $this->sessionCheckout->getQuote()->getShippingAddress();
        $this->postalCode = $shippingAddress->getPostcode();
        $this->country = $shippingAddress->getCountryId();
    }

    public function set($key, $value) {
        if ($key === "servicePointData") {
            $this->servicePointData = $value;

            $quote = $this->sessionCheckout->getQuote();

            $quote->setSendcloudServicePointId($value["sendcloud_service_point_id"]);
            $quote->setSendcloudServicePointName($value["sendcloud_service_point_name"]);
            $quote->setSendcloudServicePointStreet($value["sendcloud_service_point_street"]);
            $quote->setSendcloudServicePointHouseNumber($value["sendcloud_service_point_house_number"]);
            $quote->setSendcloudServicePointZipCode($value["sendcloud_service_point_zip_code"]);
            $quote->setSendcloudServicePointCity($value["sendcloud_service_point_city"]);
            $quote->setSendcloudServicePointCountry($value["sendcloud_service_point_country"]);
            $quote->setSendcloudServicePointPostnumber($value["sendcloud_service_point_postnumber"]);

            $this->quoteRepository->save($quote);
        }
    }

    /**
     * @param EvaluationResultFactory $resultFactory
     *
     * @return EvaluationResultInterface
     */
    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        if ($this->servicePointData) {
            return $resultFactory->createSuccess()->withCustomEvent('sendcloud:servicepoint:success')->dispatch();
        } else {
            return $resultFactory->createErrorMessageEvent()->withCustomEvent("sendcloud:servicepoint:failure")->dispatch();
        }
    }
}
