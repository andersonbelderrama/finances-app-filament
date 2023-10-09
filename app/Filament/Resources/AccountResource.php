<?php

namespace App\Filament\Resources;

use App\Enums\AccountType;
use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $label = 'Conta';

    protected static ?string $pluralLabel = 'Contas';

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('bank_name')
                    ->label('Banco')
                    ->required()
                    ->maxLength(255),
                TextInput::make('bank_branch')
                    ->label('Agência')
                    ->maxLength(255),
                TextInput::make('account_number')
                    ->label('Conta')
                    ->maxLength(255),
                TextInput::make('account_name')
                    ->label('Nome Titular')
                    ->maxLength(255),
                TextInput::make('balance')
                    ->label('Saldo')
                    ->required()
                    ->numeric()
                    ->prefix('R$'),
                Select::make('account_type')
                    ->label('Tipo')
                    ->options(AccountType::class)
                    ->required(),
                Toggle::make('account_status')
                    ->label('Ativo'),
                Hidden::make('user_id')->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bank_name')
                    ->label('Banco')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('bank_branch')
                    ->label('Agência')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('account_number')
                    ->label('Conta')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('account_name')
                    ->label('Nome')
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('account_status')
                    ->label('Status'),
                TextColumn::make('account_type')
                    ->badge()
                    ->label('Tipo'),
                TextColumn::make('balance')
                    ->weight(FontWeight::Bold)
                    ->label('Saldo')
                    ->money('BRL')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Data de Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('account_type')
                    ->label('Tipo')
                    ->options(AccountType::class),
                SelectFilter::make('account_status')
                    ->label('Status')
                    ->options([
                        true => 'Ativo',
                        false => 'Inativo',
                    ]),
                Filter::make('balance')
                    ->form([
                        TextInput::make('from')
                            ->label('Saldo De')
                            ->numeric(),
                        TextInput::make('to')
                            ->label('Saldo Até')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                            $data['from'],
                            fn (Builder $query) => $query->where('balance', '>=', $data['from'])
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query) => $query->where('balance', '<=', $data['to'])
                            );
                    }),


            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccounts::route('/'),
        ];
    }
}
