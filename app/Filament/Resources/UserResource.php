<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return trans('filament-users::user.resource.label');
    }

    public static function getPluralLabel(): string
    {
        return trans('filament-users::user.resource.label');
    }

    public static function getLabel(): string
    {
        return trans('filament-users::user.resource.single');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-users.group');
    }

    public function getTitle(): string
    {
        return trans('filament-users::user.resource.title.resource');
    }

    public static function form(Form $form): Form
    {
        $rows = [
            Forms\Components\Section::make('Información del Usuario')
                ->description('Información básica del usuario')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\FileUpload::make('profile_photo_path')
                                ->label('Foto de Perfil')
                                ->image()
                                ->avatar()
                                ->columnSpanFull()
                                ->alignCenter()
                                ->directory('profile-photos')
                                ->visibility('public')
                                ->imageEditor()
                                ->circleCropper(),

                            TextInput::make('name')
                                ->required()
                                ->label(trans('filament-users::user.resource.name'))
                                ->placeholder('Nombre completo')
                                ->autocomplete()
                                ->suffixIcon('heroicon-m-user'),

                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->label(trans('filament-users::user.resource.email'))
                                ->placeholder('correo@ejemplo.com')
                                ->suffixIcon('heroicon-m-envelope'),
                        ]),
                ]),

            Forms\Components\Section::make('Seguridad')
                ->description('Configuración de seguridad de la cuenta')
                ->schema([
                    TextInput::make('password')
                        ->label(trans('filament-users::user.resource.password'))
                        ->password()
                        ->revealable()
                        ->autocomplete('new-password')
                        ->minLength(8)
                        ->rule('min:8')
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrateStateUsing(static function ($state, $record) use ($form) {
                            return !empty($state)
                                ? Hash::make($state)
                                : $record->password;
                        })
                        ->suffixIcon('heroicon-m-lock-closed')
                        ->helperText('Mínimo 8 caracteres'),

                    Forms\Components\Toggle::make('email_verified')
                        ->label('¿Email verificado?')
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(true)
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Forms\Components\Toggle $component, $record) {
                            if ($record) {
                                $component->state($record->email_verified_at !== null);
                            }
                        })
                        ->dehydrateStateUsing(function ($state, $record) {
                            if ($state) {
                                return now();
                            }
                            return null;
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $set('email_verified_at', now());
                            } else {
                                $set('email_verified_at', null);
                            }
                        }),

                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->label('Fecha de verificación')
                        ->visible(fn (callable $get) => $get('email_verified')),
                ]),
        ];


        if (config('filament-users.shield') && class_exists(\BezhanSalleh\FilamentShield\FilamentShield::class)) {
            $rows[] = Forms\Components\Section::make('Roles y Permisos')
                ->description('Asignar roles al usuario')
                ->schema([
                    Forms\Components\Select::make('roles')
                        ->multiple()
                        ->preload()
                        ->relationship('roles', 'name')
                        ->label(trans('filament-users::user.resource.roles'))
                        ->searchable()
                        ->columnSpanFull(),
                ]);
        }

        $form->schema($rows);

        return $form;
    }

    public static function table(Table $table): Table
    {
        $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo_path')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->size(50),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Nombre')
                    ->weight('bold')
                    ->description(fn (User $record): string => $record->email)
                    ->color('primary'),

                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->roles->isEmpty()) {
                            return 'Sin rol';
                        }
                        return $record->roles->pluck('name')->join(', ');
                    }),

                TextColumn::make('created_at')
                    ->label('Fecha de registro')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->created_at->diffInDays(now()) < 1) {
                            return 'Hoy';
                        }
                        return $record->created_at->diffForHumans();
                    })
                    ->description(fn ($record) => 'Creado: ' . $record->created_at->format('d/m/Y'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('impersonate')
                    ->label('Acciones rápidas')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        if (class_exists(STS\FilamentImpersonate\Tables\Actions\Impersonate::class) && config('filament-users.impersonate')) {
                            $url = route('impersonate', $record->id);
                            return '<a href="' . $url . '" class="inline-flex items-center justify-center space-x-1 text-sm font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 h-9 px-4 text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <span>Suplantar</span>
                            </a>';
                        }
                        return '';
                    })
                    ->alignCenter(),

                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Tipo de usuario')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver perfil')
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                    EditAction::make()
                        ->label('Editar usuario')
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    DeleteAction::make()
                        ->label('Eliminar usuario')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
                ->tooltip('Más acciones')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->size('sm')
                ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->icon('heroicon-o-trash')
                        ->color('danger'),

                    Tables\Actions\BulkAction::make('assign_role')
                        ->label('Asignar rol')
                        ->icon('heroicon-o-user-group')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('role_id')
                                ->label('Rol')
                                ->options(function () {
                                    if (class_exists(\Spatie\Permission\Models\Role::class)) {
                                        return \Spatie\Permission\Models\Role::pluck('name', 'id');
                                    }
                                    return [];
                                })
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                                $role = \Spatie\Permission\Models\Role::find($data['role_id']);
                                $records->each(function ($record) use ($role) {
                                    $record->assignRole($role);
                                });
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->striped()
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 'all'])
            ->defaultPaginationPageOption(10)
            ->searchable();
        return $table;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
