<?php

namespace App\Filament\Resources\Users;

use BackedEnum;
use App\Models\User;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use YacoubAlhaidari\FilamentTour\Concerns\HasTourSteps;

class UserResource extends Resource
{
    use HasTourSteps;
    
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getTourStepId(): ?string
{
    return 'users'; // معرّف فريد
}

public static function getTourStepDescription(): ?string
{
    return 'وصف مختصر للقسم ووظيفته.';
}

public static function getTourStepFeatures(): array
{
    return ['ميزة 1', 'ميزة 2', 'ميزة 3'];
}

public static function getTourStepPosition(): string
{
    return 'right'; // أو 'left', 'top', 'bottom'
}
}
