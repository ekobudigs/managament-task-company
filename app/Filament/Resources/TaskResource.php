<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TaskResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Filament\Resources\TaskResource\RelationManagers\TaskComentsRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers\TaskAssignmentsRelationManager;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // bagamaimana jika saya membuat select dengan relasi user 
                Forms\Components\Select::make('task_id')
                    ->relationship('asigne', 'name')
                    ->required()
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Hidden::make('created_by')
                    ->default(Auth::user()->id),
                Select::make('status')
                    ->required()
                    ->options([
                        'To-Do' => 'To-Do',
                        'In Progress' => 'In Progress',
                        'Completed' => 'Completed',
                        'Pending' => 'Pending',
                        'Canceled' => 'Canceled',
                        'On Hold' => 'On Hold',
                        // 'Assigned' => 'Assigned',
                        // 'Review' => 'Review',
                    ])
                    ->default('To-Do')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('completed_at'),
                Select::make('priority')
                    ->required()
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'])
                    ->default('low')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('estimated_hours')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('actual_hours')
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        // dd(Auth::user()->id);
        $userIDs = Auth::user()->id;
     
        $baseQuery = $table
            ->deferLoading()
            ->query(Task::where('created_by', $userIDs))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Pembuat Task')
                    ->sortable(),
                Tables\Columns\TextColumn::make('taskUsers.user.name')->label('Mengerjakan Tugas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (String $state): string => match ($state) {
                        'To-Do' => 'gray',
                        'In Progress' => 'warning',
                        'Completed' => 'success',
                        'Canceled' => 'danger',
                        'Pending' => 'warning',
                        'On Hold' => 'warning',
                        'Pending' => 'warning',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estimated_hours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_hours')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);

            $users = Auth::user();
            $hasRoleAdmin = $users->hasRole('Admin');
            // dd($hasRole);

            if($hasRoleAdmin){
                return $baseQuery->query(Task::query());
            }

        // Cek jika ID pengguna adalah 1
        //untuk staff yang hanya update tugas dan comment 
        if (Auth::user()->id != 1) {
            $user = Auth::user()->id;

            return $baseQuery->query(Task::whereHas('taskUsers', function ($query) use ($user) {
                $query->where('user_id', $user);
            }));
        }

        // Jika tidak, kembalikan query dasar tanpa query tambahan
        return $baseQuery;
    }

    public static function getRelations(): array
    {
        return [
            TaskAssignmentsRelationManager::class,
            TaskComentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'view' => Pages\ViewTask::route('/{record}'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
