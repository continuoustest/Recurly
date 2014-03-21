<?php
namespace RecurlyTest\Listener;

use Recurly\Exception;
use Recurly\Listener\ErrorListener;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class ErrorListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachEvent()
    {
        $listener = new ErrorListener();

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with(MvcEvent::EVENT_DISPATCH_ERROR);

        $listener->attach($eventManager);
    }

    public function testFillEvent()
    {
        $event = new MvcEvent();
        $event->setParam('exception', new Exception\UnauthorizedException());

        $response = new HttpResponse();
        $event->setResponse($response);

        $listener = new ErrorListener();
        $listener->onError($event);

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function testFillEventWithoutException()
    {
        $event = new MvcEvent();

        $response = new HttpResponse();
        $event->setResponse($response);

        $listener = new ErrorListener();
        $listener->onError($event);

        $this->assertEquals(200, $response->getStatusCode());
    }
}