<?php

namespace App\Filament\Resources;

use App\Enums\BudgetPeriod;
use App\Filament\Resources\BudgetResource\Pages;
use App\Filament\Resources\BudgetResource\RelationManagers;
use App\Models\Budget;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $label = 'Orçamento';

    protected static ?string $pluralLabel = 'Orçamentos';

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome')
                    ->required()
                    ->maxLength(255),
                TextInput::make('description')
                    ->label('Descrição')
                    ->maxLength(255),
                TextInput::make('budget_limit')
                    ->label('Limite')
                    ->prefix('R$')
                    ->required()
                    ->numeric(),
                TextInput::make('budget_used')
                    ->label('Utilizado')
                    ->prefix('R$')
                    ->label('Utilizado')
                    ->disabled(true),
                Select::make('category_id')
                    ->label('Categoria')
                    ->searchable()
                    ->preload()
                    ->relationship('category', 'name')
                    ->required()
                    ->unique(modifyRuleUsing: function (Unique $rule, Get $get) {
                        return $rule->where('user_id', Auth::id())
                                    ->where('period', $get('period'));

                    }, ignoreRecord: true),
                Select::make('period')
                    ->label('Período')
                    ->options(BudgetPeriod::class)
                    ->required(),
                Hidden::make('user_id')->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->limit(50)
                    ->description(fn (Budget $record): string =>  $record->description ?? '')
                    ->searchable(),
                TextColumn::make('budget_limit')
                    ->label('Limite')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('budget_used')
                    ->label('Utilizado')
                    ->money('BRL')
                    ->sortable(),
                TextColumn::make('period')
                    ->label('Período')
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Categoria')
                    ->badge('primary')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Data de Atualização')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('Limite Ultrapassado')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereColumn('budget_used', '>', 'budget_limit')),
                SelectFilter::make('Categoria')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->relationship('category', 'name'),
                SelectFilter::make('period')
                    ->label('Período')
                    ->options(BudgetPeriod::class)
                    ->searchable()
                    ->preload()

            ])
            ->actions([
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
            'index' => Pages\ManageBudgets::route('/'),
        ];
    }
}
