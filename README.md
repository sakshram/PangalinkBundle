PangalinkBundle
===============

Adds a functionality for making payments in Estonia using Banklink (Pangalink) transfer.

At this moment only payment logic is implemented.

Banks available:

* Swedbank
* SEB
* Krediidipank
* Danske



Installation
==============
* Add a dependency to composer.json:

<pre><code>
"require": {
  ...
  "tfox/pangalink-bundle": "1.0.*@dev"
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
            #Type of bank. Possible arguments: swedbank, seb, krediidipank, sampo
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
            account_owner: "Test"
            private_key: "data/pangalink/seb_user_key.pem"
            bank_certificate: "data/pangalink/seb_bank_cert.pem"
            vendor_id: "33333333"
            route_return: "acme_demo_pangalink_sebbank_process"
            route_cancel: "acme_demo_pangalink_sebbank_index"
        #Third bank. Yes, you might have multiple accounts for each bank
        seb_second:
            bank: seb
            account_number: 9876545
            account_owner: "Test2"
            private_key: "data/pangalink/seb_user_key2.pem"
            bank_certificate: "data/pangalink/seb_bank_cert2.pem"
            vendor_id: "33333334"
            route_return: "acme_demo_pangalink_sebbanksecond_process"
            route_cancel: "acme_demo_pangalink_sebbanksecond_index"
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
		$connector
			->setAmount($amount)
			->setDescription($description)
			->setTransactionId($transactionId)
			->setLanguage('EST') //Possible values: EST, ENG, RUS
      //Optional. Warning: don't forget to make a 7 + 3 + 1 check of reference number,
      //otherwise bank might not accept sended data
      //Further info: http://www.pangaliit.ee/et/arveldused/7-3-1meetod
      //->setReferenceNumber('123456')
      ;
			
    //It isn't necessary to pass any parameters to controller. Just an example. 
		return array('amount' => $amount);
		
	}
</code></pre>

* Now you can render your form. You have two ways of doing it. The first way allows you to make any design of the form you want:

<pre><code>

//YourBundle/Resources/views/Some/index.html.twig

{# 'swedbank' is bank ID which was defined in config.yml  #}
&lt;form method="post" action="{{ pangalink_action_url('swedbank') }}"&gt;
{{ pangalink_form_data('swedbank') }}

{# Just an argument from controller  #}
Summ: {{ amount }}<br />

&lt;input type="submit" value="Begin payment"&gt;
&lt;/form&gt;

</code></pre>

* The second way displays a graphic button which redirects to bank if clicked. Here you can see five different rendered buttons:

<pre><code>

//YourBundle/Resources/views/Some/index.html.twig

{# The first argument is bank ID. The second argument is a button code. All codes will be published a bit later.  #}

{{ pangalink_button('swedbank', '88x31') }}

{{ pangalink_button('swedbank', '120x60') }}

{{ pangalink_button('swedbank', '217x31_est') }}

{{ pangalink_button('swedbank', '217x31_rus') }}

{{ pangalink_button('swedbank', '217x31_eng') }}

</code></pre>

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
		/* @var $connector \TFox\PangalinkBundle\Connector\SwedbankConnector */
		$connector = $service->getConnector('swedbank');
		$connector->processPayment($request);
		
		$data = $connector->getResponse()->getData();
    //Now $data contains raw response data
		$str = var_export($data, true); //Would you like to look at raw bank response?
    //A simple check if service was payed
		$result = $connector->isPaymentSuccessful() ? 'Success' : 'Failure';
    //This order number is generated by bank. Might be useful for logs.
		$orderNr = $connector->getResponse()->getOrderNumber();
    //When payment was made?
		$date = $connector->getResponse()->getOrderDate();
		
		return array('data' => $str, 'status' => $result, 
			'order_number' => $orderNr, 'sender_name' => $connector->getResponse()->getSenderName(),
			'sender_account' => $connector->getResponse()->getSenderAccountNumber(),
			'order_date' => $date
		);
  }
  
</codt></pre>

Further information will be available soon.
