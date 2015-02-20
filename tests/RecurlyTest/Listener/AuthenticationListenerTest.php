<?php
namespace RecurlyTest\Listener;

use Recurly\Listener\AuthenticationListener;
use Recurly\Module;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class AuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachEvent()
    {
        $authAdapter = $this->getAuthenticationAdapter();

        $listener = new AuthenticationListener($authAdapter);

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
            [true, true],
            [false, false],
        ];
    }

    /**
     * @dataProvider ipDataProvider
     */
    public function testIsGranted($validAuthentication, $isGranted)
    {
        $event = new MvcEvent();

        $routeMatch = new RouteMatch([]);
        $routeMatch->setMatchedRouteName(Module::RECURLY_NOTIFICATION_ROUTE);
        $event->setRouteMatch($routeMatch);

        $request = new HttpRequest();
        $event->setRequest($request);

        $authenticationResult = $this->getAuthenticationResult();

        $authenticationResult
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue($validAuthentication));

        $authAdapter = $this->getAuthenticationAdapter();

        $authAdapter
            ->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($authenticationResult));

        $listener = new AuthenticationListener($authAdapter);

        $this->assertEquals($isGranted, $listener->isGranted($event));
    }

    public function testProperlyFillEventOnAuthorization()
    {
        $event      = new MvcEvent();
        $request    = new HttpRequest();
        $response   = new HttpResponse();
        $routeMatch = new RouteMatch([]);

        $routeMatch->setMatchedRouteName(Module::RECURLY_NOTIFICATION_ROUTE);
        $event
            ->setRequest($request)
            ->setResponse($response)
            ->setRouteMatch($routeMatch);

        $authenticationResult = $this->getAuthenticationResult();

        $authenticationResult
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $authAdapter = $this->getAuthenticationAdapter();

        $authAdapter
            ->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($authenticationResult));

        $listener = new AuthenticationListener($authAdapter);
        $listener->onResult($event);

        $this->assertEmpty($event->getError());
        $this->assertNull($event->getParam('exception'));
    }

    public function testProperlySetUnauthorizedAndTriggerEventOnUnauthorization()
    {
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

        $authenticationResult = $this->getAuthenticationResult();

        $authenticationResult
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $authAdapter = $this->getAuthenticationAdapter();

        $authAdapter
            ->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($authenticationResult));

        $listener = new AuthenticationListener($authAdapter);

        $logger = $this->getMock('Zend\Log\LoggerInterface');

        $logger
            ->expects($this->once())
            ->method('info');

        $listener->setLogger($logger);

        $listener->onResult($event);

        $this->assertNotEmpty($event->getError());
        $this->assertNotNull($event->getParam('exception'));

        $this->assertEquals(HttpResponse::STATUS_CODE_401, $response->getStatusCode());
    }

    private function getAuthenticationAdapter()
    {
        return $this->getMockBuilder('Zend\Authentication\Adapter\Http')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getAuthenticationResult()
    {
        return $this->getMockBuilder('Zend\Authentication\Result')
            ->disableOriginalConstructor()
            ->getMock();
    }
}