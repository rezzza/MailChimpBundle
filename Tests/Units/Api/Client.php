<?php

namespace Rezzza\MailChimpBundle\Tests\Units\Api;

/**
 * Description of HttpConnection
 *
 * @author mika
 */
class Client extends \mageekguy\atoum\test
{
    const CUSTOMER_LIST_ID = 'db993d96da';

    private $customerEmail;

    /**
     * Test batch-subscribe success
     */
    public function testBatchSubscribeSuccess()
    {
        $response = $this->getClient()->call('lists/batch-subscribe', $this->getBatchSubscribeParameters());
        $this->array($response)->isNotEmpty()->notHasKey('error');
    }

    /**
     * Test batch-subscribe fail
     */
    public function testBatchSubscribeFail()
    {
        $client = $this->getClient();
        $response = $client->call('lists/batch-subscribe', array_merge($this->getBatchSubscribeParameters(), array('id' => 'victor_samuel_mackey')));
        $this->array($response)->isNotEmpty()->hasKey('error')
            ->integer($client->getLastErrorCode())->isEqualTo(200)
            ->string($client->getLastErrorMessage())->isEqualTo('Invalid MailChimp List ID: victor_samuel_mackey');
    }

    /**
     * Test batch-unsubscribe success
     */
    public function testBatchUnsubscribeSuccess()
    {
        $response = $this->getClient()->call('lists/batch-unsubscribe', $this->getBatchUnsubscribeParameters());
        $this->array($response)->isNotEmpty()->notHasKey('error');
    }

    /**
     * Test batch-unsubscribe fail
     */
    public function testBatchUnsubscribeFail()
    {
        $response = $this->getClient()->call('lists/batch-unsubscribe', array_merge($this->getBatchUnsubscribeParameters(), array('id' => 'victor_samuel_mackey')));
        $this->array($response)->isNotEmpty()->hasKey('error');
    }

    /**
     * Get MailChimp Client
     * @return \Rezzza\MailChimpBundle\Api\Client
     */
    private function getClient()
    {
        $client = new \Rezzza\MailChimpBundle\Api\Client('e90c270f8cf2cfbb2d5c14a36ba884c2-us9');
        $client->setConnection(new \Rezzza\MailChimpBundle\Connection\HttpConnection(true));

        return $client;
    }

    /**
     * Parameters for subscribe method
     * @return array
     */
    private function getBatchSubscribeParameters()
    {
        return array(
            'id' => self::CUSTOMER_LIST_ID,
            'batch' => array(
                array(
                    'email' => array('email' => $this->getEmail()),
                    'email_type' => $this->getEmailType(),
                    'merge_vars' => $this->getMergeVars(),
                ),
            ),
            "double_optin" => false,
            "update_existing" => true,
            "replace_interests" => true
        );
    }

    /**
     * Parameters for unsubscribe method
     * @return array
     */
    private function getBatchUnsubscribeParameters()
    {
        return array(
            'id' => self::CUSTOMER_LIST_ID,
            'batch' => array(
                array(
                    'email' => $this->getEmail(),
                ),
            ),
            "delete_member" => false,
            "send_goodbye" => false,
            "send_notify" => false,
        );
    }

    /**
     * Customer email
     * @return string
     */
    private function getEmail()
    {
        if (is_null($this->customerEmail) === true) {
            $this->customerEmail = 'mika+mailchimptest'.time().'@verylastroom.com';
        }

        return $this->customerEmail;
    }

    /**
     * Customer email type
     * @return string
     */
    private function getEmailType()
    {
        return 'html';
    }

    /**
     * Customer merge_vars
     * @return array
     */
    private function getMergeVars()
    {
        return array(
            'FNAME' => 'john',
            'LNAME' => 'doe',
            'GENDER' => 'Male',
            'LOCALE' => 'fr',
            'SIGNUPVIA' => 'EMAIL',
            'PHONE' => '0606060606',
            'CREDIT' => 30,
            'CREATEDAT' => date('Y-m-d H:i:s'),
            'UPDATEDAT' => date('Y-m-d H:i:s'),
            'APP_VER' => null,
            'APP_OS' => null,
            'REF_CODE' => 'JDOE',
            'CREDIT_EXP' => date('Y-m-d H:i:s'),
        );
    }

}
