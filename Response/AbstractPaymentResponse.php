<?php
namespace TFox\PangalinkBundle\Response;

/**
 * @bstract class for payment response
 */
abstract class AbstractPaymentResponse extends AbstractResponse
{
   const PROPERTY_TRANSACTION_ID = 'transaction_id';
   const PROPERTY_REFERENCE_NUMBER = 'reference_number';

   public function getTransactionId()
   {
       return $this->getMappedProperty(AbstractPaymentResponse::PROPERTY_TRANSACTION_ID);
   }
   
   public function getReferenceNumber()
   {
       return $this->getMappedProperty(AbstractPaymentResponse::PROPERTY_REFERENCE_NUMBER);
   }
}
