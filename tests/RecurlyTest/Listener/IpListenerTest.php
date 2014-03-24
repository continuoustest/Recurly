<?php
namespace RecurlyTest\Listener;

use Recurly\Listener\IpListener;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class IpListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachEvent()
    {
        $listener = new IpListener(array());

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with(MvcEvent::EVENT_ROUTE);

        $listener->attach($eventManager);
    }

    public function ipDataProvider()
    {
        return array(
            array(array('127.0.0.1'), '127.0.0.1', true),
            array(array('127.0.0.1'), '127.0.0.2', false),
        );
    }

    /**
     * @dataProvider ipDataProvider
     */
    public function testIsGranted(array $ipAddresses, $clientIp, $isGranted)
    {
        $_SERVER['REMOTE_ADDR'] = $clientIp;

        $event = new MvcEvent();
        
        $routeMatch = new RouteMatch(array());
        $routeMatch->setMatchedRouteName('recurly/notification');
        $event->setRouteMatch($routeMatch);

        $request = new HttpRequest();
        $event->setRequest($request);

        $listener = new IpListener($ipAddresses);

        $this->assertEquals($isGranted, $listener->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $event      = new MvcEvent();
        $request    = new HttpRequest();
        $response   = new HttpResponse();
        $routeMatch = new RouteMatch(array());

        $routeMatch->setMatchedRouteName('recurly/notification');
        $event
            ->setRequest($request)
            ->setResponse($response)
            ->setRouteMatch($routeMatch);

        $listener = new IpListener(array('127.0.0.1'));
        $listener->onResult($event);

        $this->assertEmpty($event->getError());
        $this->assertNull($event->getParam('exception'));
    }

    public function testProperlySetUnauthorizedAndTriggerEventOnUnauthorization()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $event      = new MvcEvent();
        $request    = new HttpRequest();
        $response   = new HttpResponse();
        $routeMatch = new RouteMatch(array());

        $application  = $this->getMockBuilder('Zend\Mvc\Application')
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $application
            ->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $routeMatch->setMatchedRouteName('recurly/notification');
        $event
            ->setRequest($request)
            ->setResponse($response)
            ->setRouteMatch($routeMatch)
            ->setApplication($application);

        $listener = new IpListener(array());
        $listener->onResult($event);

        $this->assertNotEmpty($event->getError());
        $this->assertNotNull($event->getParam('exception'));

        $this->assertEquals(HttpResponse::STATUS_CODE_403, $response->getStatusCode());
    }
}