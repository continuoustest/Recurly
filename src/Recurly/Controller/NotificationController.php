<?php
namespace Recurly\Controller;

use Recurly\Notification\Handler as NotificationHandler;
use Zend\Mvc\Controller\AbstractActionController;

class NotificationController extends AbstractActionController
{
    /**
     * @var NotificationHandler
     */
    protected $notificationHandler;

    /**
     * @param NotificationHandler $handler
     */
    public function setNotificationHandler(NotificationHandler $handler)
    {
        $this->notificationHandler = $handler;
    }

    public function pushAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        
        $xml = $request->getContent();
        if (empty($xml)) {
            $response->setStatusCode(202);
            return $response;
        }

        $this->notificationHandler->handle($xml);

        $response->setStatusCode(200);

        return $response;
    }
}