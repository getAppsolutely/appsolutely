<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Actions\Grid\DeleteAction;
use App\Admin\Forms\Models\FormEntryForm;
use App\Admin\Forms\Models\FormFieldForm;
use App\Admin\Forms\Models\FormForm;
use App\Enums\FormFieldType;
use App\Models\Form;
use App\Models\FormEntry;
use App\Models\FormField;
use App\Repositories\FormEntryRepository;
use App\Repositories\FormFieldRepository;
use App\Repositories\FormRepository;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Widgets\Modal;
use Dcat\Admin\Widgets\Tab;

final class DynamicFormController extends AdminBaseController
{
    public function __construct(
        protected FormRepository $formRepository,
        protected FormFieldRepository $fieldRepository,
        protected FormEntryRepository $entryRepository,
    ) {}

    public function index(Content $content): Content
    {
        return $content
            ->header(__t('Dynamic Forms'))
            ->description(__t('Manage Forms, Fields & Entries'))
            ->body($this->buildTabs());
    }

    protected function buildTabs(): Tab
    {
        $tab = new Tab();

        $tab->add(__t('Form Entries'), $this->formEntriesGrid(), true, 'form-entries');
        $tab->add(__t('Forms'), $this->formsGrid(), false, 'forms');
        $tab->add(__t('Form Fields'), $this->formFieldsGrid(), false, 'form-fields');
        $tab->add(__t('Statistics'), $this->statisticsGrid(), false, 'statistics');

        $tab->withCard();

        return $tab;
    }

    protected function formsGrid(): Grid
    {
        return Grid::make(Form::query()->withCount(['fields', 'entries', 'validEntries']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('name', __t('Name'))->editable();
            $grid->column('slug', __t('Slug'))->copyable();
            $grid->column('description', __t('Description'))->limit(50);
            $grid->column('fields_count', __t('Fields'))->badge('primary');
            $grid->column('entries_count', __t('Total Entries'))->badge('info');
            $grid->column('valid_entries_count', __t('Valid Entries'))->badge('success');
            $grid->column('status', __t('Status'))->switch();
            $grid->column('created_at', __t('Created'))->display(column_time_format())->sortable();

            $grid->model()->orderByDesc('id');
            $grid->quickSearch('id', 'name', 'slug');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(3);
                $filter->like('name', __t('Name'))->width(3);
                $filter->like('slug', __t('Slug'))->width(3);
                $filter->equal('status', __t('Status'))->width(3);
                $filter->between('created_at', __t('Created'))->datetime()->width(6);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title(__t('Edit Form') . ' #' . $actions->getKey())
                    ->body(FormForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('New Form'))
                        ->body(FormForm::make())
                        ->button(admin_create_button())
                );
            });

            $grid->header(function () {
                return $this->renderFormStats();
            });
        });
    }

    protected function formFieldsGrid(): Grid
    {
        return Grid::make(FormField::query()->with('form'), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('form.name', __t('Form'));
            $grid->column('label', __t('Label'))->editable();
            $grid->column('name', __t('Field Name'));
            $grid->column('type', __t('Type'))->display(function ($type) {
                $formFieldType = FormFieldType::tryFrom($type);

                return $formFieldType ? $formFieldType->label() : $type;
            })->label([
                'text'     => 'primary',
                'email'    => 'info',
                'select'   => 'warning',
                'textarea' => 'secondary',
                'checkbox' => 'success',
                'radio'    => 'danger',
                'file'     => 'dark',
            ]);
            $grid->column('required', __t('Required'))->bool();
            $grid->column('sort', __t('Sort'))->editable();

            $grid->model()->orderBy('form_id')->orderBy('sort');
            $grid->quickSearch('id', 'label', 'name');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(3);
                $filter->equal('form_id', __t('Form'))->select($this->formRepository->getFormOptions())->width(3);
                $filter->equal('type', __t('Type'))->select(FormFieldType::toArray())->width(3);
                $filter->equal('required', __t('Required'))->select([1 => __t('Required'), 0 => __t('Optional')])->width(3);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title(__t('Edit Field') . ' #' . $actions->getKey())
                    ->body(FormFieldForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('New Form Field'))
                        ->body(FormFieldForm::make())
                        ->button(admin_create_button())
                );
            });
        });
    }

    protected function formEntriesGrid(): Grid
    {
        return Grid::make(FormEntry::query()->with(['form', 'user']), function (Grid $grid) {
            $grid->column('id', __t('ID'))->sortable();
            $grid->column('form.name', __t('Form'));
            $grid->column('full_name', __t('Name'))->display(function () {
                return $this->full_name ?: '-';
            });
            $grid->column('email', __t('Email'))->copyable();
            $grid->column('mobile', __t('Mobile'));
            $grid->column('user.name', __t('User'))->display(function ($userName) {
                return $userName ?: __t('Guest');
            });
            $grid->column('is_spam', __t('Status'))->display(function ($isSpam) {
                return $isSpam
                    ? '<span class="badge badge-danger">' . __t('Spam') . '</span>'
                    : '<span class="badge badge-success">' . __t('Valid') . '</span>';
            });
            $grid->column('submitted_at', __t('Submitted'))->display(column_time_format())->sortable();
            $grid->column('ip_address', __t('IP Address'));

            $grid->model()->orderByDesc('submitted_at');
            $grid->quickSearch('id', 'email', 'first_name', 'last_name');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id', __t('ID'))->width(3);
                $filter->equal('form_id', __t('Form'))->select($this->formRepository->getFormOptions())->width(3);
                $filter->equal('is_spam', __t('Status'))->select([1 => __t('Spam'), 0 => __t('Valid')])->width(3);
                $filter->like('email', __t('Email'))->width(3);
                $filter->between('submitted_at', __t('Submitted'))->datetime()->width(6);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title(__t('View Entry') . ' #' . $actions->getKey())
                    ->body(FormEntryForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button('<i class="feather icon-eye"></i> ' . __t('View')));

                // Add spam toggle action (use row model; closure may run where $this is not the controller)
                $row = $actions->row;
                if ($row) {
                    if ($row->is_spam) {
                        $actions->append('<a href="javascript:void(0)" onclick="toggleSpamStatus(' . $actions->getKey() . ', false)"><i class="fa fa-check"></i> ' . __t('Mark Valid') . '</a>');
                    } else {
                        $actions->append('<a href="javascript:void(0)" onclick="toggleSpamStatus(' . $actions->getKey() . ', true)"><i class="fa fa-ban"></i> ' . __t('Mark Spam') . '</a>');
                    }
                }

                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append('<a href="' . admin_route('api.forms.entries.export') . '" class="btn btn-outline-primary"><i class="fa fa-download"></i> ' . __t('Export CSV') . '</a>');
            });

            $grid->header(function () {
                return $this->renderEntryStats();
            });
        });
    }

    protected function statisticsGrid(): string
    {
        $totalForms   = $this->formRepository->count();
        $activeForms  = $this->formRepository->countByStatus(1);
        $totalFields  = $this->fieldRepository->count();
        $totalEntries = $this->entryRepository->count();
        $validEntries = $this->entryRepository->countValid();
        $spamEntries  = $this->entryRepository->countSpam();
        $todayEntries = $this->entryRepository->countValidByDateRange(today(), today());
        $weekEntries  = $this->entryRepository->countValidByDateRange(now()->startOfWeek(), now()->endOfWeek());
        $monthEntries = $this->entryRepository->countValidByDateRange(now()->startOfMonth(), now()->endOfMonth());

        $html = '<div class="row">';

        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-primary">';
        $html .= '<div class="card-header">' . __t('Forms') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $totalForms . '</h2>';
        $html .= '<p class="card-text">' . __t('Total Forms') . '</p>';
        $html .= '</div></div></div>';

        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-info">';
        $html .= '<div class="card-header">' . __t('Active') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $activeForms . '</h2>';
        $html .= '<p class="card-text">' . __t('Active Forms') . '</p>';
        $html .= '</div></div></div>';

        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-warning">';
        $html .= '<div class="card-header">' . __t('Fields') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $totalFields . '</h2>';
        $html .= '<p class="card-text">' . __t('Total Fields') . '</p>';
        $html .= '</div></div></div>';

        $html .= '<div class="col-md-3">';
        $html .= '<div class="card text-white bg-success">';
        $html .= '<div class="card-header">' . __t('Entries') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<h2 class="card-title">' . $totalEntries . '</h2>';
        $html .= '<p class="card-text">' . __t('Total Entries') . '</p>';
        $html .= '</div></div></div>';

        $html .= '</div>';

        $html .= '<div class="row mt-4">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="card">';
        $html .= '<div class="card-header">' . __t('Entry Statistics') . '</div>';
        $html .= '<div class="card-body">';
        $html .= '<table class="table table-borderless">';
        $html .= '<tr><td>' . __t('Valid') . ':</td><td><span class="badge bg-success">' . $validEntries . '</span></td></tr>';
        $html .= '<tr><td>' . __t('Spam') . ':</td><td><span class="badge bg-danger">' . $spamEntries . '</span></td></tr>';
        $html .= '<tr><td>' . __t('Today') . ':</td><td><span class="badge bg-info">' . $todayEntries . '</span></td></tr>';
        $html .= '<tr><td>' . __t('This Week') . ':</td><td><span class="badge bg-primary">' . $weekEntries . '</span></td></tr>';
        $html .= '<tr><td>' . __t('This Month') . ':</td><td><span class="badge bg-secondary">' . $monthEntries . '</span></td></tr>';
        $html .= '</table>';
        $html .= '</div></div></div>';
        $html .= '</div>';

        return $html;
    }

    protected function renderFormStats(): string
    {
        $totalForms   = $this->formRepository->count();
        $activeForms  = $this->formRepository->countByStatus(1);
        $totalFields  = $this->fieldRepository->count();
        $totalEntries = $this->entryRepository->count();
        $validEntries = $this->entryRepository->countValid();
        $spamEntries  = $this->entryRepository->countSpam();

        $totalFormsLabel   = __t('Total Forms');
        $activeFormsLabel  = __t('Active Forms');
        $totalFieldsLabel  = __t('Total Fields');
        $totalEntriesLabel = __t('Total Entries');
        $validEntriesLabel = __t('Valid Entries');
        $spamEntriesLabel  = __t('Spam Entries');

        return "
        <div class='row mb-3'>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-primary'>{$totalForms}</h5>
                        <small>{$totalFormsLabel}</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-success'>{$activeForms}</h5>
                        <small>{$activeFormsLabel}</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-info'>{$totalFields}</h5>
                        <small>{$totalFieldsLabel}</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-secondary'>{$totalEntries}</h5>
                        <small>{$totalEntriesLabel}</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-success'>{$validEntries}</h5>
                        <small>{$validEntriesLabel}</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-danger'>{$spamEntries}</h5>
                        <small>{$spamEntriesLabel}</small>
                    </div>
                </div>
            </div>
        </div>";
    }

    protected function renderEntryStats(): string
    {
        $todayEntries   = $this->entryRepository->countValidByDateRange(today(), today());
        $weekEntries    = $this->entryRepository->countValidByDateRange(now()->startOfWeek(), now()->endOfWeek());
        $monthEntries   = $this->entryRepository->countValidByDateRange(now()->startOfMonth(), now()->endOfMonth());
        $statsLabel     = __t('Entry Statistics');
        $todayLabel     = __t('Today');
        $thisWeekLabel  = __t('This Week');
        $thisMonthLabel = __t('This Month');

        return "
        <div class='alert alert-info border-0 mt-1 mb-1'>
            <div class='d-flex align-items-center'>
                <i class='fa fa-chart-line text-info mr-2'></i>
                <strong>{$statsLabel}: </strong>
                <span class='ml-2'>{$todayLabel}: {$todayEntries}</span>
                <span class='ml-3'>{$thisWeekLabel}: {$weekEntries}</span>
                <span class='ml-3'>{$thisMonthLabel}: {$monthEntries}</span>
            </div>
        </div>
        <script>
        function toggleSpamStatus(entryId, isSpam) {
            const spamUrl = '" . admin_route('api.forms.entries.mark-spam', ['id' => '__ID__']) . "';
            const notSpamUrl = '" . admin_route('api.forms.entries.mark-not-spam', ['id' => '__ID__']) . "';
            const url = isSpam 
                ? spamUrl.replace('__ID__', entryId)
                : notSpamUrl.replace('__ID__', entryId);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    Dcat.success(data.message);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    Dcat.error(data.message);
                }
            })
            .catch(error => {
                Dcat.error('An error occurred while updating the entry status');
            });
        }
        </script>";
    }
}
