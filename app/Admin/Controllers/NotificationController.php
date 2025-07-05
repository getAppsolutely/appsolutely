<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeleteAction;
use App\Admin\Forms\Models\NotificationQueueForm;
use App\Admin\Forms\Models\NotificationRuleForm;
use App\Admin\Forms\Models\NotificationTemplateForm;
use App\Models\NotificationQueue;
use App\Models\NotificationRule;
use App\Models\NotificationTemplate;
use App\Repositories\NotificationQueueRepository;
use App\Repositories\NotificationRuleRepository;
use App\Repositories\NotificationTemplateRepository;
use App\Services\NotificationRuleService;
use App\Services\NotificationService;
use App\Services\NotificationTemplateService;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;

final class NotificationController extends AdminBaseController
{
    public function __construct(
        protected NotificationTemplateRepository $templateRepository,
        protected NotificationRuleRepository $ruleRepository,
        protected NotificationQueueRepository $queueRepository,
        protected NotificationService $notificationService,
        protected NotificationTemplateService $templateService,
        protected NotificationRuleService $ruleService
    ) {}

    public function index(Content $content): Content
    {
        return $content
            ->header(__t('Notifications'))
            ->description(__t('Manage email templates, rules, and queue'))
            ->body($this->buildTabs());
    }

    protected function buildTabs(): Tab
    {
        $tab = new Tab();

        $tab->add(__t('Templates'), $this->templatesGrid(), true, 'templates');
        $tab->add(__t('Rules'), $this->rulesGrid(), false, 'rules');
        $tab->add(__t('Queue'), $this->queueGrid(), false, 'queue');
        $tab->add(__t('Statistics'), $this->statisticsGrid(), false, 'statistics');

        $tab->withCard();

        return $tab;
    }

    protected function templatesGrid(): Grid
    {
        return Grid::make(new NotificationTemplate(), function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'desc');

            $grid->column('id', __t('ID'))->sortable();
            $grid->column('name', __t('Name'))->limit(50)->editable();
            $grid->column('category', __t('Category'))->label();
            $grid->column('subject', __t('Subject'))->limit(60)->editable();
            $grid->column('usage_count', __t('Rules'))->display(function ($count) {
                return "<span class='badge bg-info'>{$count}</span>";
            });
            $grid->column('status', __t('Status'))->switch();
            $grid->column('created_at', __t('Created'))->display(column_time_format())->sortable();

            $grid->quickSearch('id', 'name', 'subject');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(3);
                $filter->like('name', __t('Name'))->width(3);
                $filter->equal('category', __t('Category'))->width(3);
                $filter->equal('status', __t('Status'))->select(['1' => __t('Active'), '0' => __t('Inactive')])->width(3);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableExport();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title(__t('Edit Template') . ' #' . $actions->getKey())
                    ->body(NotificationTemplateForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));

                if (! $actions->row->is_system) {
                    $actions->append('<a class="btn btn-xs btn-outline-success" href="#" onclick="duplicateTemplate(' . $actions->getKey() . ')">' . __t('Duplicate') . '</a>');
                }

                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('New Template'))
                        ->body(NotificationTemplateForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function rulesGrid(): Grid
    {
        return Grid::make(NotificationRule::with('template'), function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'desc');

            $grid->column('id', __t('ID'))->sortable();
            $grid->column('name', __t('Name'))->limit(50)->editable();
            $grid->column('trigger_type', __t('Trigger'))->label();
            $grid->column('trigger_reference', __t('Reference'))->limit(30)->editable();
            $grid->column('template.name', __t('Template'))->limit(40);
            $grid->column('recipient_type', __t('Recipients'))->display(function ($type) {
                $labels = [
                    'admin'       => __t('Admin'),
                    'user'        => __t('User'),
                    'custom'      => __t('Custom'),
                    'conditional' => __t('Conditional'),
                ];

                return "<span class='badge bg-secondary'>{$labels[$type]}</span>";
            });
            $grid->column('delay_minutes', __t('Delay'))->display(function ($minutes) {
                return $minutes > 0 ? "{$minutes}m" : __t('Immediate');
            });
            $grid->column('status', __t('Status'))->switch();
            $grid->column('created_at', __t('Created'))->display(column_time_format())->sortable();

            $grid->quickSearch('id', 'name', 'trigger_reference');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(3);
                $filter->like('name', __t('Name'))->width(3);
                $filter->equal('trigger_type', __t('Trigger'))->width(3);
                $filter->equal('status', __t('Status'))->select(['1' => __t('Active'), '0' => __t('Inactive')])->width(3);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableExport();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title(__t('Edit Rule') . ' #' . $actions->getKey())
                    ->body(NotificationRuleForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));

                $actions->append('<a class="btn btn-xs btn-outline-info" href="#" onclick="testRule(' . $actions->getKey() . ')">' . __t('Test') . '</a>');
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('New Rule'))
                        ->body(NotificationRuleForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function queueGrid(): Grid
    {
        return Grid::make(NotificationQueue::with(['template', 'rule']), function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'desc');

            $grid->column('id', __t('ID'))->sortable();
            $grid->column('template.name', __t('Template'))->limit(40);
            $grid->column('recipient_email', __t('Recipient'))->limit(50);
            $grid->column('subject', __t('Subject'))->limit(60);
            $grid->column('status', __t('Status'))->display(function ($status) {
                $colors = [
                    'pending'   => 'warning',
                    'sent'      => 'success',
                    'failed'    => 'danger',
                    'cancelled' => 'secondary',
                ];
                $labels = [
                    'pending'   => __t('Pending'),
                    'sent'      => __t('Sent'),
                    'failed'    => __t('Failed'),
                    'cancelled' => __t('Cancelled'),
                ];

                return "<span class='badge bg-{$colors[$status]}'>{$labels[$status]}</span>";
            });
            $grid->column('scheduled_at', __t('Scheduled'))->display(column_time_format())->sortable();
            $grid->column('sent_at', __t('Sent'))->display(column_time_format())->sortable();
            $grid->column('retry_count', __t('Attempts'))->display(function ($attempts) {
                return $attempts > 0 ? "<span class='badge bg-warning'>{$attempts}</span>" : '-';
            });

            $grid->quickSearch('id', 'recipient_email', 'subject');
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(3);
                $filter->like('recipient_email', __t('Recipient'))->width(3);
                $filter->equal('status', __t('Status'))->select([
                    'pending'   => __t('Pending'),
                    'sent'      => __t('Sent'),
                    'failed'    => __t('Failed'),
                    'cancelled' => __t('Cancelled'),
                ])->width(3);
                $filter->between('created_at', __t('Created'))->datetime()->width(3);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();
            $grid->disableExport();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->lg()->scrollable()
                    ->title(__t('View Queue Item') . ' #' . $actions->getKey())
                    ->body(NotificationQueueForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));

                if ($actions->row->status === 'failed') {
                    $actions->append('<a class="btn btn-xs btn-outline-success" href="#" onclick="retryNotification(' . $actions->getKey() . ')">' . __t('Retry') . '</a>');
                }
                if ($actions->row->status === 'pending') {
                    $actions->append('<a class="btn btn-xs btn-outline-danger" href="#" onclick="cancelNotification(' . $actions->getKey() . ')">' . __t('Cancel') . '</a>');
                }
            });

            $grid->tools(function (Tools $tools) {
                $tools->append('<a class="btn btn-success" href="#" onclick="processQueue()">' . __t('Process Queue') . '</a>');
            });
        });
    }

    protected function statisticsGrid(): string
    {
        $stats      = $this->notificationService->getStatistics();
        $queueStats = $this->queueRepository->getStatistics();

        $html = '<div class="row">';

        // Templates Statistics
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-primary">';
        $html .= '<div class="card-header">' . __t('Templates') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $stats['templates_count'] . '</h2>';
        $html .= '<p class="card-text">' . __t('Active Templates') . '</p>';
        $html .= '</div></div></div>';

        // Rules Statistics
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-info">';
        $html .= '<div class="card-header">' . __t('Rules') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $stats['rules_count'] . '</h2>';
        $html .= '<p class="card-text">' . __t('Active Rules') . '</p>';
        $html .= '</div></div></div>';

        // Pending Queue
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-warning">';
        $html .= '<div class="card-header">' . __t('Pending') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $queueStats['pending'] . '</h2>';
        $html .= '<p class="card-text">' . __t('Pending Notifications') . '</p>';
        $html .= '</div></div></div>';

        // Sent Today
        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-success">';
        $html .= '<div class="card-header">' . __t('Sent Today') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $queueStats['today'] . '</h2>';
        $html .= '<p class="card-text">' . __t('Notifications Sent') . '</p>';
        $html .= '</div></div></div>';

        $html .= '</div>';

        // Additional Statistics
        $html .= '<div class="row mt-4">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="card">';
        $html .= '<div class="card-header">' . __t('Queue Statistics') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<table class="table table-borderless">';
        $html .= '<tr><td>' . __t('Total') . ':</td><td><span class="badge bg-primary">' . $queueStats['total'] . '</span></td></tr>';
        $html .= '<tr><td>' . __t('Sent') . ':</td><td><span class="badge bg-success">' . $queueStats['sent'] . '</span></td></tr>';
        $html .= '<tr><td>' . __t('Failed') . ':</td><td><span class="badge bg-danger">' . $queueStats['failed'] . '</span></td></tr>';
        $html .= '<tr><td>' . __t('This Week') . ':</td><td><span class="badge bg-info">' . $queueStats['this_week'] . '</span></td></tr>';
        $html .= '<tr><td>' . __t('This Month') . ':</td><td><span class="badge bg-secondary">' . $queueStats['this_month'] . '</span></td></tr>';
        $html .= '</table>';
        $html .= '</div></div></div>';

        $html .= '<div class="col-md-6">';
        $html .= '<div class="card">';
        $html .= '<div class="card-header">' . __t('Actions') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<button type="button" class="btn btn-primary btn-block mb-2" onclick="processQueue()">' . __t('Process Queue') . '</button>';
        $html .= '<button type="button" class="btn btn-warning btn-block mb-2" onclick="retryFailed()">' . __t('Retry Failed') . '</button>';
        $html .= '<button type="button" class="btn btn-info btn-block mb-2" onclick="cleanOldSent()">' . __t('Clean Old Sent') . '</button>';
        $html .= '</div></div></div>';

        $html .= '</div>';

        return $html;
    }
}
