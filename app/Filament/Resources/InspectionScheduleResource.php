<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionScheduleResource\Pages;
use App\Filament\Resources\InspectionScheduleResource\RelationManagers;
use App\Models\InspectionSchedule;
use App\Models\Vessel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionScheduleResource extends Resource
{
    protected static ?string $model = InspectionSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Calendario de Inspecciones';

    protected static ?string $modelLabel = 'Inspección Programada';

    protected static ?string $pluralModelLabel = 'Inspecciones Programadas';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Inspección')
                    ->description('Detalles básicos de la inspección programada')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 3,
                        ])
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->label('Título')
                                ->columnSpan([
                                    'md' => 2,
                                ]),

                            Forms\Components\Select::make('vessel_id')
                                ->relationship('vessel', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->label('Embarcación'),

                            Forms\Components\DateTimePicker::make('start_datetime')
                                ->required()
                                ->label('Fecha y Hora de Inicio')
                                ->seconds(false),

                            Forms\Components\DateTimePicker::make('end_datetime')
                                ->required()
                                ->label('Fecha y Hora de Finalización')
                                ->seconds(false),

                            Forms\Components\TextInput::make('inspector_name')
                                ->required()
                                ->maxLength(255)
                                ->label('Nombre del Inspector'),

                            Forms\Components\TextInput::make('location')
                                ->maxLength(255)
                                ->label('Ubicación'),

                            Forms\Components\Select::make('status')
                                ->options(InspectionSchedule::getStatusOptions())
                                ->required()
                                ->default('scheduled')
                                ->label('Estado'),

                            Forms\Components\Textarea::make('description')
                                ->label('Descripción')
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Título'),

                Tables\Columns\TextColumn::make('vessel.name')
                    ->searchable()
                    ->sortable()
                    ->label('Embarcación'),

                Tables\Columns\TextColumn::make('start_datetime')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Inicio'),

                Tables\Columns\TextColumn::make('end_datetime')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Fin'),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->searchable()
                    ->label('Inspector'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => InspectionSchedule::getStatusOptions()[$state] ?? $state)
                    ->label('Estado'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vessel')
                    ->relationship('vessel', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Embarcación'),

                Tables\Filters\SelectFilter::make('status')
                    ->options(InspectionSchedule::getStatusOptions())
                    ->label('Estado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListInspectionSchedules::route('/'),
            'create' => Pages\CreateInspectionSchedule::route('/create'),
            'edit' => Pages\EditInspectionSchedule::route('/{record}/edit'),
        ];
    }
}
