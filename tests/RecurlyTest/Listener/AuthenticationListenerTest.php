<?php
namespace RecurlyTest\Listener;

use Recurly\Listener\AuthenticationListener;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class AuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testAttachEvent()
    {
        $authAdapter = $this->getMockBuilder('Zend\Authentication\Adapter\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $listener = new AuthenticationListener($authAdapter);

        $eventManager = $this->getMock('Zend\EventManager\EventManagerInterface');
        $eventManager->expects($this->once())
                     ->method('attach')
                     ->with(MvcEvent::EVENT_ROUTE);

        $listener->attach($eventManager);
    }

    public function ipDataProvider()
    {
        return array(
            array(true, true),
            array(false, false),
        );
    }

    /**
     * @dataProvider ipDataProvider
     */
    public function testIsGranted($validAuthentication, $isGranted)
    {
        $event = new MvcEvent();

        $routeMatch = new RouteMatch(array());
        $routeMatch->setMatchedRouteName('recurly/notification');
        $event->setRouteMatch($routeMatch);

        $request = new HttpRequest();
        $event->setRequest($request);

        $authAdapter = $this->getMockBuilder('Zend\Authentication\Adapter\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationResult = $this->getMockBuilder('Zend\Authentication\Result')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationResult
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue($validAuthentication));

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
        $routeMatch = new RouteMatch(array());

        $routeMatch->setMatchedRouteName('recurly/notification');
        $event
            ->setRequest($request)
            ->setResponse($response)
            ->setRouteMatch($routeMatch);


        $authAdapter = $this->getMockBuilder('Zend\Authentication\Adapter\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationResult = $this->getMockBuilder('Zend\Authentication\Result')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationResult
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

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


        $authAdapter = $this->getMockBuilder('Zend\Authentication\Adapter\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationResult = $this->getMockBuilder('Zend\Authentication\Result')
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationResult
            ->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $authAdapter
            ->expects($this->once())
            ->method('authenticate')
            ->will($this->returnValue($authenticationResult));

        $listener = new AuthenticationListener($authAdapter);
        $listener->onResult($event);

        $this->assertNotEmpty($event->getError());
        $this->assertNotNull($event->getParam('exception'));

        $this->assertEquals(HttpResponse::STATUS_CODE_401, $response->getStatusCode());
    }
}