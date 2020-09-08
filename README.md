PangalinkBundle
===============

Adds a functionality for making payments in Estonia using Banklink (Pangalink) transfer.<br>
At this moment only payment logic is implemented.<br>

Banks available:
* Swedbank
* SEB
* Krediidipank
* Danske
* Nordea
* LHV



Installation
==============
* Add a dependency to composer.json:

<pre><code>
"require": {
  ...
  "sakshram/pangalink-bundle": "2.*@dev"
  ...
</code></pre>
* Run "php composer.phar update"
* Register a bundle in your app/AppKernel.php:
<pre><code>
$bundles = array(
  ...
  new TFox\PangalinkBundle\TFoxPangalinkBundle(),
  ...
);
</code></pre>

Configuration
==============
PangalinkBundle configuration is stored in app/config/config.yml file. An example is provided below:
<pre><code>
t_fox_pangalink:
    accounts:
        #First bank
        #ID of the first bank. This ID will be used in system. Feel free to write any ID you wish
        swedbank:
            #Type of bank. Possible arguments: swedbank, seb, krediidipank, sampo, nordea, lhv
            bank: swedbank
            #Service URL. Remove in production mode if real bank's URL is necessary
            service_url: "https://pangalink.net/banklink/swedbank"
            #Vendor's bank account number
            account_number: 123456
            #Vendor's name
            account_owner: "Test"
            #Path to file with user's private key. Relative to "app" directory
            private_key: "data/pangalink/swed_user_key.pem"
            #Path to file with certificate of the bank. Relative to "app" directory
            bank_certificate: "data/pangalink/swed_bank_cert.pem"
            #Vendor's ID given by bank
            vendor_id: "222222"
            #Route of the page displayed if payment was successful
            route_return: "acme_demo_pangalink_swedbank_process"
            #Route of the page displayed if payment was cancelled
            route_cancel: "acme_demo_pangalink_swedbank_index"
            #Alternatives to route_return and route_cancel are:
            #url_return: "http://example.com"
            #url_cancel:  "http://example.com"
        #Second bank
        #ID of the second bank. This ID will be used in system. Feel free to write any ID you wish
        seb:
            bank: seb
            account_number: 1234567
            account_owner: "Test2"
            private_key: "data/pangalink/seb_user_key.pem"
            bank_certificate: "data/pangalink/seb_bank_cert.pem"
            vendor_id: "33333333"
            route_return: "acme_demo_pangalink_sebbank_process"
            route_cancel: "acme_demo_pangalink_sebbank_index"
        #Third bank. Yes, you might have multiple accounts for each bank
        seb_second:
            bank: seb
            account_number: 9876545
            account_owner: "Test3"
            private_key: "data/pangalink/seb_user_key2.pem"
            bank_certificate: "data/pangalink/seb_bank_cert2.pem"
            vendor_id: "33333334"
            route_return: "acme_demo_pangalink_sebbanksecond_process"
            route_cancel: "acme_demo_pangalink_sebbanksecond_index"
        #Nordea uses secret instead of key pair based encryption.
        nordea:
            bank: nordea
            account_owner: "Test4"
            secret: "SomeSecretString"
            vendor_id: "33333334"
            route_return: "acme_demo_pangalink_nordea_process"
            route_reject: "acme_demo_pangalink_nordea_index"
            route_cancel: "acme_demo_pangalink_nordea_index"
</code></pre>

Usage
==============
* First of all let's create an action where payment data will be saved

<pre><code>
//YourBundle/Controller/SomeController.php

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;

class SomeController extends BaseController
{

    /**
     * @Sensio\Route
     * @Sensio\Template
     */
    public function indexAction()
    {
        //Amount, payment description and some transaction ID
        $amount = 10.0;
        $description = 'Symfony2 pangalink bundle test';
        $transactionId = mt_rand(0, 999999);
        $transactionId = str_pad($transactionId, 6, '0');

        /* @var $service \TFox\PangalinkBundle\Service\PangalinkService */
        $service = $this->get('tfox.pangalink.service');
         // 'swedbank' is ID of the bank from config.yml
        /* @var $connector \TFox\PangalinkBundle\Connector\SwedbankConnector */		
        $connector = $service->getConnector('swedbank');
        $request = $connector->createPaymentRequest();
        $request
            ->setAmount($amount)
            ->setComment($description)
            ->setTransactionId($transactionId)
            ->setLanguage('EST') //Possible values: EST, ENG, RUS
            // Warning: RUS is not applicable for Nordea
        ;

        // Warning: date is not applicable for Solo protocol (Nordea)
        $request->setDateTime(new \DateTime());
            
        //Optional. Warning: don't forget to make a 7 + 3 + 1 check of reference number,
        //otherwise bank might not accept sended data
        //Further info: http://www.pangaliit.ee/et/arveldused/7-3-1meetod
        //->setReferenceNumber('123456')
        ;

        return array('payment_request' => $request);

    }
}
</code></pre>

* Now you can render your form. You have two ways of doing it. The first way allows you to make any design of the form you want:

<pre><code>

//YourBundle/Resources/views/Some/index.html.twig

{# 'swedbank' is bank ID which was defined in config.yml  #}
&lt;form method="post" action="{{ pangalink_action_url(payment_request) }}"&gt;
{{ pangalink_form_data(payment_request) }}

{# Just an argument from controller  #}

&lt;input type="submit" value="Begin payment"&gt;
&lt;/form&gt;

</code></pre>

* The second way displays a graphic button which redirects to bank if clicked. Here you can see five different rendered buttons:

<pre><code>

//YourBundle/Resources/views/Some/index.html.twig

{# The first argument is a payment request received from controller. The second argument is a button code. Watch the table below.  #}

{{ pangalink_button(payment_request, '88x31') }}

</code></pre>

In the table below are provided codes for images which are available in PangalinkBundle.<br>
<b>WARNING! </b> All images are the property of their respective owners (banks). It is usually forbidden to modify provided images.

| Bank                   | Available image codes         |
|-----------------------:|:-----------------------------:|
| Swedbank               | 88x31                         |
|                        | 120x60_1                      |
|                        | 120x60_2                      |
|                        | 217x31_est                    |
|                        | 217x31_rus                    |
|                        | 217x31_eng                    |
| SEB                    | 88x31                         |
|                        | 120x60_1                      |
|                        | 120x60_2                      |
| Krediidipank           | 88x19                         |
|                        | 88x31                         |
|                        | 137x30                        |
| Sampobank              | 88x31                         |
|                        | 88x31_anim                    |
|                        | 120x60_1                      |
|                        | 120x60_2                      |
|                        | 180x70                        |
| Nordea                 | 88x31                         |
|                        | 177x56                        |
| LHV                    | 88x31                         |
|                        | 120x60                        |


* The last task is to process a response which was sent by bank. Let's look to controller again:

<pre><code>
//YourBundle/Controller/SomeController.php

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;

class SomeController extends BaseController
{

    /**
     * @Sensio\Route("/process")
     * @Sensio\Template
     */
    public function processAction(Request $request)
    {
            /* @var $service \TFox\PangalinkBundle\Service\PangalinkService */
            $service = $this->get('tfox.pangalink.service');
            // Get a response from bank. 
            // The bundle automatically determines an appropriate connector
            $paymentResponse = $service->getPaymentResponse($request);

            // If no appropriate connector found, the function "getPaymentResponse" returns null
            if(true == is_null($paymentResponse))
                throw new \Exception('Could not determine a bank operator');

            // Check if payment was successful
            if(true == $paymentResponse->isSuccessful()) {
                // Get some properties 
                $paymentResponse->getBankId();
                $paymentResponse->getOrderNumber();
                $paymentResponse->getTransactionId();
                $paymentResponse->getDateTime();
            }

            // Get ID of the connector defined in the Symfony confiruration
            $accountId = $paymentResponse ? $paymentResponse->getConnector()->getAccountId() : 'NONE';
            // Format status
            $status = $paymentResponse && $paymentResponse->isSuccessful() ? 'YES' : 'NO';

            return array(
                'response' => $paymentResponse,
                'account_id' => $accountId,
                'status' => $status
            );
    }
}
</codt></pre>
