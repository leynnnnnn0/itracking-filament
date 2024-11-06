<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplyResource\Pages;
use App\Filament\Resources\SupplyResource\RelationManagers;
use App\Models\Category;
use App\Models\Supply;
use App\Models\SupplyHistory;
use App\Models\Unit;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;
    protected static ?string $navigationGroup = 'Supply';

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Department Details')
                    ->schema([
                        TextInput::make('description')
                            ->rules([
                                'string',
                                'regex:/[a-zA-Z]/',
                            ])->required(),

                        Select::make('unit')
                            ->options(Unit::select('name')->pluck('name', 'name'))
                            ->native(false)
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                            ])
                            ->preload()
                            ->createOptionUsing(function (array $data): string {
                                return Unit::create($data)->name;
                            })
                            ->required(),

                        TextInput::make('quantity')
                            ->maxLength(30)
                            ->live()
                            ->extraInputAttributes([
                                'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                            ])
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $set('total', $state ?? 0);
                            })
                            ->required()
                            ->visible(fn(string $operation) => $operation === 'create'),

                        Hidden::make('total')->required(),

                        DatePicker::make('expiry_date')
                            ->native(false),

                        Select::make('categories')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                            ])
                            ->preload()
                            ->createOptionUsing(function (array $data): string {
                                return Category::create($data)->name;
                            })
                            ->searchable()
                            ->required(),

                        Radio::make('is_consumable')
                            ->label('Is consumable?')
                            ->boolean()
                            ->inline()
                            ->required()
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),

                TextColumn::make('quantity'),

                TextColumn::make('used'),

                TextColumn::make('total')
                    ->color(fn($record) => $record->total < 50 ? 'danger' : 'success'),

                TextColumn::make('categories.name')
                    ->badge(),

                TextColumn::make('expiry_date')
                    ->date('F d, Y'),
            ])
            ->filters([
                TrashedFilter::make()
                    ->visible(Auth::user()->role === 'Admin'),
                TernaryFilter::make('is_consumable'),
                SelectFilter::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->preload()
                    ->searchable()
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Archive')
                    ->modalHeading('Archive Supply')
                    ->successNotificationTitle('Archived'),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('Add Quantity')
                        ->requiresConfirmation()
                        ->modalDescription('Please confirm that the quantity provided is accurate before proceeding. This action will update the supply details record accordingly.')
                        ->color('success')
                        ->form([
                            TextInput::make('quantity')
                                ->integer()
                                ->maxLength(10)
                                ->minValue(1)
                                ->live()
                                ->extraInputAttributes([
                                    'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                                ])
                                ->required()
                        ])
                        ->action(function ($data, $record) {
                            try {
                                $record->quantity += $data['quantity'];
                                $record->total += $data['quantity'];
                                $record->recently_added = $data['quantity'];
                                $record->save();
                                Notification::make()
                                    ->title("$record->description (ID: $record->id)")
                                    ->body('Quantity Updated')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('Record Usage')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalDescription('Please confirm that the quantity provided is accurate before proceeding. This action will update the supply details record accordingly.')
                        ->modalSubmitActionLabel('Submit')
                        ->form([
                            TextInput::make('quantity')
                                ->integer()
                                ->maxLength(10)
                                ->hint(fn($record) => "Available: {$record->total}")
                                ->minValue(1)
                                ->maxValue(fn($record) => $record->total)
                                ->live()
                                ->extraInputAttributes([
                                    'onkeydown' => 'return (event.keyCode !== 69 && event.keyCode !== 187 && event.keyCode !== 189)',
                                ])
                                ->required()
                        ])
                        ->action(function ($data, $record) {
                            try {
                                $quantityUsed = $data['quantity'];
                                if ($quantityUsed > $record->total) {
                                    Notification::make()
                                        ->title('Quantity Used Exceeded.')
                                        ->body("Quantiy used should not be greater than $record->description (ID: $record->id) quantity")
                                        ->danger()
                                        ->send()
                                        ->duration(100000);
                                    return;
                                }
                                $record->used += $quantityUsed;
                                $record->total -= $quantityUsed;
                                $record->recently_added = 0;
                                $record->save();
                                Notification::make()
                                    ->title("$record->description (ID: $record->id)")
                                    ->body('Supply Usage Updated.')
                                    ->success()
                                    ->send();
                            } catch (Exception $e) {
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Archive')
                        ->modalHeading('Archive Supplies')
                        ->successNotificationTitle('Archived'),
                    RestoreBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('id'),
                TextEntry::make('description'),
                TextEntry::make('unit'),
                TextEntry::make('quantity'),
                TextEntry::make('used'),
                TextEntry::make('recently_added'),
                TextEntry::make('total'),
                TextEntry::make('categories.name')
                    ->badge(),
                TextEntry::make('expiry_date')
                    ->date('F d, Y'),
                TextEntry::make(name: 'is_consumable')
                    ->formatStateUsing(fn($record) => $record ? 'Yes' : 'No'),
            ])->columns(2);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupplies::route('/'),
            'create' => Pages\CreateSupply::route('/create'),
            'edit' => Pages\EditSupply::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['categories'])->orderBy('total');
    }
}
