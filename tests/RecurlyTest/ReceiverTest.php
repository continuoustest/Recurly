<?php
namespace RecurlyTest;

use Recurly\Receiver;
use Zend\EventManager\EventManager;

class ReceiverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Receiver 
     */
    protected $receiver;

    public function setUp()
    {
        $this->receiver = new Receiver();
    }

    public function testSetGetEventManager()
    {
        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $this->receiver->getEventManager());

        $this->receiver->setEventManager(new EventManager());
        $this->assertInstanceOf('Zend\EventManager\EventManagerInterface', $this->receiver->getEventManager());
    }

    public function testReceive()
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
            ->setMethods(array('trigger'))
            ->getMock();

        $eventManager
            ->expects($this->once())
            ->method('trigger');

        $this->receiver->setEventManager($eventManager);
        $this->receiver->receive($xml);
    }
}