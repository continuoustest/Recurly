<?php
namespace Recurly\Notification;

use Recurly_PushNotification;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;

class Handler implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    const EVENT_NEW_ACCOUNT = 'new_account_notification';
    const EVENT_CANCELED_ACCOUNT = 'canceled_account_notification';
    const EVENT_BILLING_INFO_UPDATED = 'billing_info_updated_notification';
    const EVENT_REACTIVATED_ACCOUNT = 'reactivated_account_notification';
    const EVENT_NEW_SUSBCRIPTION = 'new_subscription_notification';
    const EVENT_UPDATED_SUBSCRIPTION = 'updated_subscription_notification';
    const EVENT_CANCELED_SUBSCRIPTION = 'canceled_subscription_notification';
    const EVENT_EXPIRED_SUBSCRIPTION = 'expired_subscription_notification';
    const EVENT_RENEWED_SUBSCRIPTION = 'renewed_subscription_notification';
    const EVENT_SUCCESSFUL_PAYMENT = 'successful_payment_notification';
    const EVENT_FAILED_PAYMENT = 'failed_payment_notification';
    const EVENT_SUCCESSFUL_REFUND = 'successful_refund_notification';
    const EVENT_VOID_PAYMENT = 'void_payment_notification';

    /**
     * @param  string $data
     * @return void
     */
    public function handle($data)
    {
        $notification = new Recurly_PushNotification($data);

        $params = ['account' => $notification->account];
        if (!empty($notification->subscription)) {
            $params['subscription'] = $notification->subscription;
        }
        if (!empty($notification->transaction)) {
            $params['transaction'] = $notification->transaction;
        }

        $this->getEventManager()->trigger($notification->type, null, $params);
    }
}