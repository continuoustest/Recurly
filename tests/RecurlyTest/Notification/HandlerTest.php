<?php
namespace RecurlyTest\Notification;

use Recurly\Notification\Handler as NotificationHandler;
use Zend\EventManager\EventManager;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NotificationHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new NotificationHandler();
    }

    public function testSetGetEventManager()
    {
        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $this->handler->getEventManager());

        $this->handler->setEventManager(new EventManager());
        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $this->handler->getEventManager());
    }

    public function testNotificationHandler()
    {
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

        $eventManager = $this->getMockBuilder('Zend\EventManager\EventManager')
            ->setMethods(['trigger'])
            ->getMock();

        $eventManager
            ->expects($this->once())
            ->method('trigger');

        $this->handler->setEventManager($eventManager);
        $this->handler->handle($xml);
    }
}