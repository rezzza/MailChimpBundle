<?php

namespace Rezzza\MailChimpBundle\Tests\Units\Api;

/**
 * Description of HttpConnection
 *
 * @author mika
 */
class Client extends \mageekguy\atoum\test
{

    /**
     * Test call
     */
    public function testCall()
    {
        $client = new \Rezzza\MailChimpBundle\Api\Client('e90c270f8cf2cfbb2d5c14a36ba884c2-us9');
        $client->setConnection(new \Rezzza\MailChimpBundle\Connection\HttpConnection(true));

        $this->testSubscribe($client);
        $this->testUnSubscribe($client);
    }

    private function testSubscribe(\Rezzza\MailChimpBundle\Api\Client $client)
    {
        $parameters = array(
            'id' => 'db993d96da',
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

        //Test success
        $successResponse = $client->call('lists/batch-subscribe', $parameters);
        $this->array($successResponse)->isNotEmpty()->notHasKey('error');

        // Test Error
        $errorResponse = $client->call('lists/batch-subscribe', array_merge($parameters, array('id' => 'victor_samuel_mackey')));
        $this->array($errorResponse)->isNotEmpty()->hasKey('error');
    }

    private function testUnsubscribe(\Rezzza\MailChimpBundle\Api\Client $client)
    {
        $parameters = array('id' => 'db993d96da',
            'batch' => array(
                array(
                    'email' => $this->getEmail(),
                ),
            ),
            "delete_member" => false,
            "send_goodbye" => false,
            "send_notify" => false,
        );

        //Test success
        $successResponse = $client->call('lists/batch-unsubscribe', $parameters);
        $this->array($successResponse)->isNotEmpty()->notHasKey('error');

        // Test Error
        $errorResponse = $client->call('lists/batch-unsubscribe', array_merge($parameters, array('id' => 'victor_samuel_mackey')));
        $this->array($errorResponse)->isNotEmpty()->hasKey('error');
    }

    private function getEmail()
    {
        return 'jdoe@god.fr';
    }

    private function getEmailType()
    {
        return 'html';
    }

    private function getMergeVars()
    {
        $vars = array(
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

        return $vars;
    }

}
