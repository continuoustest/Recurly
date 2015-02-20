<?php
namespace RecurlyTest\Notification;

use Recurly\Notification\Handler as NotificationHandler;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetEventManager()
    {
        $handler = new NotificationHandler();

        $this->assertInstanceOf(
            'Zend\EventManager\EventManagerInterface',
            $handler->getEventManager()
        );

        $handler->setEventManager($this->getEventManager());
        $this->assertInstanceOf(
            'Zend\EventManager\EventManagerInterface',
            $handler->getEventManager()
        );
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

        $handler = new NotificationHandler();

        $eventManager = $this->getEventManager();
        $eventManager
            ->expects($this->once())
            ->method('trigger')
            ->with('new_account_notification', $this->anything());
        $handler->setEventManager($eventManager);

        $handler->handle($xml);
    }

    private function getEventManager()
    {
        return $this->getMock('Zend\EventManager\EventManagerInterface');
    }
}