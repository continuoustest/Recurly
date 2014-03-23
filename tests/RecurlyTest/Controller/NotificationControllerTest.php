<?php
namespace RecurlyTest\Controller;

use Recurly\Controller\NotificationController;
use Zend\Http\Response as HttpResponse;

class NotificationControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationController
     */
    protected $controller;

    /**
     * @var \Recurly\Notification\Handler
     */
    protected $handler;

    public function setUp()
    {
        $this->controller = new NotificationController();

        $this->handler = $this->getMockBuilder('Recurly\Notification\Handler')
            ->getMock();
        $this->controller->setNotificationHandler($this->handler);
    }

    public function testIndexActionWithRequestContent()
    {
        $this->handler
            ->expects($this->once())
            ->method('handle');

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

        $response = $this->controller->pushAction();

        $this->assertEquals(HttpResponse::STATUS_CODE_200, $response->getStatusCode());
    }

    public function testIndexActionWithEmptyRequestContent()
    {
        $this->handler
            ->expects($this->never())
            ->method('handle');

        $response = $this->controller->pushAction();

        $this->assertEquals(HttpResponse::STATUS_CODE_202, $response->getStatusCode());
    }
}