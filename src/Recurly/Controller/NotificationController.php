<?php
namespace Recurly\Controller;

use Recurly\Receiver;
use Zend\Mvc\Controller\AbstractActionController;

class NotificationController extends AbstractActionController
{
    /**
     * @var Receiver
     */
    protected $receiver;

    /**
     * @param Receiver $receiver
     */
    public function setReceiver(Receiver $receiver)
    {
        $this->receiver = $receiver;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        
        $xml = $request->getContent();
        if (empty($xml)) {
            $response->setStatusCode(202);
            return $response;
        }

        $this->receiver->receive($xml);

        $response->setStatusCode(200);

        return $response;
    }
}