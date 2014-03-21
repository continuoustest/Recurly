<?php
namespace RecurlyTest\Controller;

use Recurly\Controller\NotificationController;

class NotificationControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationController
     */
    protected $controller;

    /**
     * @var \Recurly\Receiver
     */
    protected $receiver;

    public function setUp()
    {
        $this->controller = new NotificationController();

        $this->receiver = $this->getMockBuilder('Recurly\Receiver')
            ->getMock();
        $this->controller->setReceiver($this->receiver);
    }

    public function testIndexActionWithRequestContent()
    {
        $this->receiver
            ->expects($this->once())
            ->method('receive');

        $xml = '<new_account_notification>
            <account>
                <account_code>1</account_code>
                <username nil="true"></username>
                <email>verena@example.com</email>
                <first_name>Verena</first_name>
                <last_name>Example</last_name>
                <company_name nil="true"></company_name>
            </account>
        </new_account_notification>';

        $request = $this->controller->getRequest();
        $request->setContent('$xml');

        $response = $this->controller->indexAction();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testIndexActionWithEmptyRequestContent()
    {
        $this->receiver
            ->expects($this->never())
            ->method('receive');

        $response = $this->controller->indexAction();

        $this->assertEquals(202, $response->getStatusCode());
    }
}