<?php
namespace RecurlyTest\Listener;

use Recurly\Listener\IpListener;
use Recurly\Module;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class IpListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachEvent()
    {
        $whip = $this->getMock('VectorFace\Whip\Whip');

        $listener = new IpListener($whip);

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $eventManager
            ->expects($this->once())
            ->method('attach')
            ->with(MvcEvent::EVENT_ROUTE);

        $listener->attach($eventManager);
    }

    public function ipDataProvider()
    {
        return [
            ['127.0.0.1', true],
            [false, false],
        ];
    }

    /**
     * @dataProvider ipDataProvider
     */
    public function testIsGranted($ipAddress, $isGranted)
    {
        $whip = $this->getMock('VectorFace\Whip\Whip');
        $whip
            ->expects($this->once())
            ->method('getValidIpAddress')
            ->will($this->returnValue($ipAddress));

        $event = new MvcEvent();

        $routeMatch = new RouteMatch([]);
        $routeMatch->setMatchedRouteName(Module::RECURLY_NOTIFICATION_ROUTE);
        $event->setRouteMatch($routeMatch);

        $request = new HttpRequest();
        $event->setRequest($request);

        $listener = new IpListener($whip);

        $this->assertSame($isGranted, $listener->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $whip = $this->getMock('VectorFace\Whip\Whip');
        $whip
            ->expects($this->once())
            ->method('getValidIpAddress')
            ->will($this->returnValue(true));

        $event      = new MvcEvent();
        $request    = new HttpRequest();
        $response   = new HttpResponse();
        $routeMatch = new RouteMatch([]);

        $routeMatch->setMatchedRouteName(Module::RECURLY_NOTIFICATION_ROUTE);
        $event
            ->setRequest($request)
            ->setResponse($response)
            ->setRouteMatch($routeMatch);

        $listener = new IpListener($whip);
        $listener->onResult($event);

        $this->assertEmpty($event->getError());
        $this->assertNull($event->getParam('exception'));
    }

    public function testProperlySetUnauthorizedAndTriggerEventOnUnauthorization()
    {
        $whip = $this->getMock('VectorFace\Whip\Whip');
        $whip
            ->expects($this->once())
            ->method('getValidIpAddress')
            ->will($this->returnValue(false));

        $event      = new MvcEvent();
        $request    = new HttpRequest();
        $response   = new HttpResponse();
        $routeMatch = new RouteMatch([]);

        $application  = $this->getMockBuilder('Zend\Mvc\Application')
            ->disableOriginalConstructor()
            ->getMock();
        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');

        $application
            ->expects($this->any())
            ->method('getEventManager')
            ->will($this->returnValue($eventManager));

        $routeMatch->setMatchedRouteName(Module::RECURLY_NOTIFICATION_ROUTE);
        $event
            ->setRequest($request)
            ->setResponse($response)
            ->setRouteMatch($routeMatch)
            ->setApplication($application);

        $listener = new IpListener($whip);

        $logger = $this->getMock('Zend\Log\LoggerInterface');

        $logger
            ->expects($this->once())
            ->method('info');

        $listener->setLogger($logger);

        $listener->onResult($event);

        $this->assertNotEmpty($event->getError());
        $this->assertNotNull($event->getParam('exception'));

        $this->assertEquals(HttpResponse::STATUS_CODE_403, $response->getStatusCode());
    }
}