<?php

namespace App\Filament\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use App\Models\Task;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Models\TaskAssignments;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Concerns\HasRecord;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class TaskAssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'taskAssignments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('comments')
                    ->required()
                    ->maxLength(255),
                Hidden::make('user_id')
                    ->default(Auth::user()->id),
                Forms\Components\DatePicker::make('assignment_date')
                    ->default(Carbon::now())
                    ->required()
                    ->readonly(),
                Hidden::make('status')
                    ->default('New'),
                FileUpload::make('file')->downloadable()
            ]);
    }

    public function table(Table $table): Table
    {
        $recipient = auth()->user();
        return $table
            ->recordTitleAttribute('comments')
            ->columns([
                Tables\Columns\TextColumn::make('comments'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\AttachAction::make(),

                // Tables\Actions\CreateAction::make(),
                Tables\Actions\CreateAction::make()->after(function (TaskAssignments $product) {
                    $task = Task::find($product->task_id);
                    $createdBy = User::find($task->created_by);

                    $recipient = auth()->user();
                    $taskDescription =  $task->title;
                    Notification::make()
                            ->success()
                            ->title('Task Selesai.')
                            ->body("$recipient->name telah menyelesaikan tugas: $taskDescription.")
                            ->icon('heroicon-o-bell')
                            ->actions([
                                Action::make('view')
                                    ->button()
                                    ->markAsRead()
                                    ->url(route('filament.admin.resources.tasks.view', $product->task_id))
                              
                            ])
                            ->sendToDatabase($createdBy);
                    // dd($createdBy);
                })
                // Tables\Actions\CreateAction::make()
                //     ->successNotification(
                //         Notification::make()
                //             ->success()
                //             ->title('User registered')
                //             ->body('The user has been created successfully.'),
                //     ),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // protected function beforeCreate(): void
    // {
    //     // ...
    // }
    // protected function getCreatedNotification(): ?Notification
    // {
    //     $recipient = auth()->user();
    //     return Notification::make()
    //         ->success()
    //         ->title('User registered')
    //         ->body('The user has been created successfully.')
    //         ->sendToDatabase($recipient);
    // }
    protected function afterCreate(): void
    {
        dd('eko');
        $taskId = $this->record->task_id;
        $task = Task::find($taskId);
        $createdBy = User::find($task->created_by);

        $notification = Notification::make()
            ->success()
            ->title('Task assignment created')
            ->body('A new task assignment has been created for the task "{task_name}".')
            ->icon('heroicon-o-bell')
            ->route('filament.resources.tasks.show', ['task' => $this->record])
            ->sendToDatabase($createdBy)
            ->action([
                'text' => 'View task',
                'route' => 'filament.resources.tasks.show',
                'parameters' => ['task' => $this->record],
            ]);

        $createdBy->notify($notification); // Send the notification
    }

    protected function getCreatedNotification(): ?Notification
    {
        $taskId = $this->record->task_id;
        $task = Task::find($taskId);
        $createdBy = User::find($task->created_by);
        dd($taskId);

        return   Notification::make()
            ->success()
            ->title('Task assignment created')
            ->body('A new task assignment has been created for the task "{task_name}".')
            ->icon('heroicon-o-bell')
            ->route('filament.resources.tasks.show', ['task' => $this->record])
            ->sendToDatabase($createdBy);
    }
    // protected function beforeCreate(): void
    // {
    //     $taskId = $this->attributes['task_id'];
    //     $task = Task::find($taskId);
    //     $createdBy = User::find($task->created_by);
    //     dd($taskId);
    //     Notification::make()
    //         ->success()
    //         ->title('Task assignment created')
    //         ->body('A new task assignment has been created for the task "{task_name}".')
    //         ->icon('heroicon-o-bell')
    //         ->route('filament.resources.tasks.show', ['task' => $this->attributes])
    //         ->sendToDatabase($createdBy);
    // }

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     $data['user_id'] = auth()->id();
    //     dd($data);
    //     return $data;
    // }

    public function isReadOnly(): bool
    {
        return false;
    }
}
