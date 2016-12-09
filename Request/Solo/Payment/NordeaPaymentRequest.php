<?php
namespace TFox\PangalinkBundle\Request\Solo\Payment;

use TFox\PangalinkBundle\Connector\Solo\NordeaConnector;
use TFox\PangalinkBundle\Helper\CharsetHelper;
use TFox\PangalinkBundle\Request\AbstractPaymentRequest;

/**
 * Payment request for Nordea
 */
class NordeaPaymentRequest extends AbstractSoloPaymentRequest
{
    const LANUAGE_EN = 3; // English
    const LANUAGE_ET = 4; // Estonian
    const LANUAGE_LV = 6; // Latvian
    const LANUAGE_LT = 7; // Lithuanian

    /**
     * var \TFox\PangalinkBundle\Connector\Solo\NordeaConnector
     */
    protected $connector;

    public function __construct(NordeaConnector $connector)
    {
        parent::__construct();
        $this->connector = $connector;
    }

    public function initFormFields()
    {
        parent::initFormFields();

        $this
            ->setConfirm('YES')
            ->setKeyVersion('0001')
            ->setVendorId($this->connector->getConfigurationValue('vendor_id'))
            ->setCurrency('EUR')
            ->setRecipientAccount($this->connector->getConfigurationValue('account_number'))
            ->setRecipientName($this->connector->getConfigurationValue('account_owner'))
            ->setDateTime('EXPRESS')
            ->setReferenceNumber('')
            ->setLanguage('EST')
            ->setServiceUrl($this->connector->getServiceUrl())
            ->setUrlReturn($this->getUrlReturnOrNull())
            ->setUrlCancel($this->getUrlCancelOrNull())
            ->setUrlReject($this->getUrlRejectOrNull())
            ->setVersion('0003');

    }

    public function setLanguage($language)
    {
        $langMapping = array(
            'ENG' => self::LANUAGE_EN,
            'EST' => self::LANUAGE_ET
        );
        if (true == array_key_exists($language, $langMapping)) {
            $convertedLanguage = $langMapping[$language];
        } else {
            $convertedLanguage = $language;
        }
        return parent::setLanguage($convertedLanguage);
    }

    public function getLanguage()
    {
        $language = parent::getLanguage();

        $langMapping = array(
            self::LANUAGE_EN => 'ENG',
            self::LANUAGE_ET => 'EST'
        );
        if (true == array_key_exists($language, $langMapping)) {
            $language = $langMapping[$language];
        }
        return $language;
    }

    public function getFormData()
    {
        $formData = $this->formFields;
        $formData = array_map(function ($element) {
            if (true == is_string($element)) {
                $element = CharsetHelper::utf8ToIso88591($element);
            }
            return $element;
        }, $formData);

        $secret = $this->getSecret();
        $macData = array(
            $this->getVersion(), $this->getTransactionId(), $this->getVendorId(),
            $this->getAmount(), $this->getReferenceNumber(), $this->getDateTime(),
            $this->getCurrency(), $secret
        );

        $macData = implode('&', $macData);
        $macData .= '&';

        $formData["SOLOPMT_MAC"] = strtoupper(sha1($macData));
        return $formData;
    }

    public function setDateTime($datetime)
    {
        return parent::setDateTime('EXPRESS');
    }

    public function getDateTime()
    {
        return 'EXPRESS';
    }

}
