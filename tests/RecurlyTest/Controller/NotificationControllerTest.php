<?php
namespace RecurlyTest\Controller;

use Recurly\Controller\NotificationController;
use Zend\Http\Response as HttpResponse;

class NotificationControllerFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testIndexActionWithRequestContent()
    {
        $requestContent = 'foo';

        $controller = new NotificationController();

        $handler = $this->getMock('Recurly\Notification\Handler');
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($requestContent);
        $controller->setNotificationHandler($handler);

        $request = $controller->getRequest();
        $request->setContent($requestContent);

        $response = $controller->pushAction();

        $this->assertEquals(HttpResponse::STATUS_CODE_200, $response->getStatusCode());
    }

    public function testIndexActionWithEmptyRequestContent()
    {
        $controller = new NotificationController();

        $handler = $this->getMock('Recurly\Notification\Handler');
        $handler
            ->expects($this->never())
            ->method('handle');
        $controller->setNotificationHandler($handler);

        $response = $controller->pushAction();

        $this->assertEquals(HttpResponse::STATUS_CODE_202, $response->getStatusCode());
    }
}