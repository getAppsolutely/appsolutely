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
            $grid->column('created_at', __t('Created'))->datetime();

            $grid->model()->orderByDesc('id');
            $grid->quickSearch('id', 'name', 'slug');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->like('name')->width(3);
                $filter->like('slug')->width(3);
                $filter->equal('status')->width(3);
                $filter->between('created_at')->datetime()->width(6);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Form #' . $actions->getKey())
                    ->body(FormForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Create Form'))
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
                $filter->equal('id')->width(3);
                $filter->equal('form_id')->select($this->formRepository->getFormOptions())->width(3);
                $filter->equal('type')->select(FormFieldType::toArray())->width(3);
                $filter->equal('required')->select([1 => 'Required', 0 => 'Optional'])->width(3);
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('Edit Field #' . $actions->getKey())
                    ->body(FormFieldForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button(admin_edit_action()));
                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append(
                    Modal::make()->xl()->scrollable()
                        ->title(__t('Create Form Field'))
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
                return $isSpam ? '<span class="badge badge-danger">Spam</span>' : '<span class="badge badge-success">Valid</span>';
            });
            $grid->column('submitted_at', __t('Submitted'))->datetime();
            $grid->column('ip_address', __t('IP Address'));

            $grid->model()->orderByDesc('submitted_at');
            $grid->quickSearch('id', 'email', 'first_name', 'last_name');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width(3);
                $filter->equal('form_id')->select($this->formRepository->getFormOptions())->width(3);
                $filter->equal('is_spam')->select([1 => 'Spam', 0 => 'Valid'])->width(3);
                $filter->like('email')->width(3);
                $filter->between('submitted_at')->datetime()->width(6);
            });

            $grid->disableCreateButton();
            $grid->disableEditButton();
            $grid->disableDeleteButton();

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->append(Modal::make()->xl()->scrollable()
                    ->title('View Entry #' . $actions->getKey())
                    ->body(FormEntryForm::make($actions->getKey())->payload([
                        'id' => $actions->getKey(),
                    ]))
                    ->button('<i class="feather icon-eye"></i>'));

                // Add spam toggle action
                $entry = $this->entryRepository->find($actions->getKey());
                if ($entry) {
                    if ($entry->is_spam) {
                        $actions->append('<a href="javascript:void(0)" onclick="toggleSpamStatus(' . $actions->getKey() . ', false)" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Mark Valid</a>');
                    } else {
                        $actions->append('<a href="javascript:void(0)" onclick="toggleSpamStatus(' . $actions->getKey() . ', true)" class="btn btn-sm btn-warning"><i class="fa fa-ban"></i> Mark Spam</a>');
                    }
                }

                $actions->append(new DeleteAction());
            });

            $grid->tools(function (Tools $tools) {
                $tools->append('<a href="' . admin_route('api.forms.entries.export') . '" class="btn btn-outline-primary"><i class="fa fa-download"></i> Export CSV</a>');
            });

            $grid->header(function () {
                return $this->renderEntryStats();
            });
        });
    }

    protected function renderFormStats(): string
    {
        $totalForms   = $this->formRepository->count();
        $activeForms  = $this->formRepository->countByStatus(1);
        $totalFields  = $this->fieldRepository->count();
        $totalEntries = $this->entryRepository->count();
        $validEntries = $this->entryRepository->countValid();
        $spamEntries  = $this->entryRepository->countSpam();

        return "
        <div class='row mb-3'>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-primary'>{$totalForms}</h5>
                        <small>Total Forms</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-success'>{$activeForms}</h5>
                        <small>Active Forms</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-info'>{$totalFields}</h5>
                        <small>Total Fields</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-secondary'>{$totalEntries}</h5>
                        <small>Total Entries</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-success'>{$validEntries}</h5>
                        <small>Valid Entries</small>
                    </div>
                </div>
            </div>
            <div class='col-md-2'>
                <div class='card'>
                    <div class='card-body text-center'>
                        <h5 class='text-danger'>{$spamEntries}</h5>
                        <small>Spam Entries</small>
                    </div>
                </div>
            </div>
        </div>";
    }

    protected function renderEntryStats(): string
    {
        $todayEntries = $this->entryRepository->countValidByDateRange(today(), today());
        $weekEntries  = $this->entryRepository->countValidByDateRange(now()->startOfWeek(), now()->endOfWeek());
        $monthEntries = $this->entryRepository->countValidByDateRange(now()->startOfMonth(), now()->endOfMonth());

        return "
        <div class='alert alert-info border-0 mb-3'>
            <div class='d-flex align-items-center'>
                <i class='fa fa-chart-line text-info mr-2'></i>
                <strong>Entry Statistics: </strong>
                <span class='ml-2'>Today: {$todayEntries}</span>
                <span class='ml-3'>This Week: {$weekEntries}</span>
                <span class='ml-3'>This Month: {$monthEntries}</span>
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
