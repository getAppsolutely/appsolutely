<?php

declare(strict_types=1);

namespace App\Admin\Forms\Models;

use App\Models\NotificationRule;
use App\Models\NotificationTemplate;

final class NotificationRuleForm extends ModelForm
{
    public function __construct(?int $id = null)
    {
        parent::__construct($id);
    }

    protected function initializeModel(): void
    {
        $this->model = new NotificationRule();
    }

    public function form(): void
    {
        parent::form();

        $this->hidden('id');

        $this->text('name', __t('Rule Name'))->required()
            ->help(__t('Enter a descriptive name for this notification rule'));

        $this->select('template_id', __t('Email Template'))->options(
            NotificationTemplate::where('status', 1)->pluck('name', 'id')->toArray()
        )->required()
            ->help(__t('Select which email template to use for this rule'));

        $this->divider();

        $this->html('<h5>' . __t('Trigger Settings') . '</h5>');

        $this->select('trigger_type', __t('Trigger Type'))->options([
            'form_submission'   => __t('Form Submission'),
            'user_registration' => __t('User Registration'),
            'user_login'        => __t('User Login'),
            'order_created'     => __t('Order Created'),
            'order_completed'   => __t('Order Completed'),
            'payment_received'  => __t('Payment Received'),
            'custom_event'      => __t('Custom Event'),
        ])->required()
            ->help(__t('When should this notification be triggered?'));

        $this->text('trigger_reference', __t('Trigger Reference'))
            ->help(__t('Optional: Specific reference like form ID, order status, etc.'));

        $this->divider();

        $this->html('<h5>' . __t('Recipient Settings') . '</h5>');

        $this->select('recipient_type', __t('Recipient Type'))->options([
            'admin'       => __t('Admin Users'),
            'user'        => __t('Triggering User'),
            'custom'      => __t('Custom Email'),
            'conditional' => __t('Conditional (Based on Data)'),
        ])->required()
            ->help(__t('Who should receive this notification?'));

        // Custom emails field - only show when recipient_type is 'custom'
        $this->tags('recipient_emails', __t('Custom Emails'))
            ->help(__t('Required if recipient type is Custom. Enter email addresses'));

        // Conditions field - only show when recipient_type is 'conditional'
        $this->textarea('conditions', __t('Conditions'))->rows(5)
            ->help(__t('JSON conditions. Example: {"field": "email_domain", "operator": "equals", "value": "company.com"}'));

        $this->divider();

        $this->html('<h5>' . __t('Timing') . '</h5>');

        $this->number('delay_minutes', __t('Delay (Minutes)'))->default(0)
            ->help(__t('Delay before sending notification (0 = immediate)'));

        $this->divider();

        $this->html('<h5>' . __t('Settings') . '</h5>');

        $this->switch('status', __t('Status'))
            ->help(__t('Enable or disable this rule'))
            ->default(true);
    }
}
