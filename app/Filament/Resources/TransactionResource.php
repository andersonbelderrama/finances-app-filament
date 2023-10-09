<?php

namespace App\Filament\Resources;

use App\Enums\TransactionType;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $label = 'Transação';

    protected static ?string $pluralLabel = 'Transações';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

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
                TextInput::make('amount')
                    ->label('Valor')
                    ->prefix('R$')
                    ->required()
                    ->numeric(),
                Select::make('type')
                    ->label('Tipo')
                    ->options(TransactionType::class)
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('is_investment')
                    ->label('Investimento'),
                Toggle::make('has_been_paid')
                    ->label('Pago'),
                DatePicker::make('payment_date')
                    ->label('Data de Pagamento'),
                DatePicker::make('due_date')
                    ->label('Data de Vencimento'),
                Select::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('account_id')
                    ->label('Conta')
                    ->relationship('account', 'bank_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('created_at')
                    ->label('Data de Criação')
                    ->date()
                    ->disabled(true),
                DatePicker::make('updated_at')
                    ->label('Data de Atualização')
                    ->date()
                    ->disabled(true),
                Hidden::make('user_id')->default(Auth::id()),

            ]);
    }

    public static function table(Table $table): Table
    {
        Table::$defaultDateDisplayFormat = 'd/m/Y';

        return $table
            ->paginated([10, 25, 50, 100, 'all'])
            ->groups([
                Group::make('created_at')
                ->date()
                ->label('Data de Criação')
                ->titlePrefixedWithLabel(false),
                Group::make('payment_date')
                ->date()
                ->label('Data de Pagamento')
                ->titlePrefixedWithLabel(false),
                Group::make('due_date')
                ->date()
                ->label('Data de Vencimento')
                ->titlePrefixedWithLabel(false),
                Group::make('updated_at')
                ->date()
                ->label('Data de Atualização')
                ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('payment_date')
            ->columns([
                Grid::make([
                    'md' => 4,
                    '2xl' => 4,
                ])
                    ->schema([
                    Stack::make([
                        TextColumn::make('category.name')
                        ->label('Categoria')
                        ->sortable()
                        ->badge(),
                        TextColumn::make('name')
                        ->label('Nome')
                        ->extraAttributes(['class' => 'whitespace-nowrap'])
                        ->weight(FontWeight::Bold)
                        ->size(TextColumnSize::Medium)
                        ->sortable()
                        ->searchable(),
                        TextColumn::make('description')
                        ->label('Descrição')
                        ->weight(FontWeight::Light)
                        ->size(TextColumnSize::Small)
                        ->color('gray')
                        ->searchable(),
                    ])->space(2)->columnSpan([
                        'md' => 3,
                        '2xl' => 3,
                    ]),
                    Stack::make([
                        TextColumn::make('type')
                        ->label('Tipo')
                        ->badge()
                        ->size(TextColumnSize::ExtraSmall),
                        TextColumn::make('amount')
                        ->label('Valor')
                        ->weight(FontWeight::Bold)
                        ->size(TextColumnSize::Medium)
                        ->money('BRL')
                        ->sortable()
                        ->searchable(),
                        IconColumn::make('has_been_paid')
                        ->label('Pago')
                        ->boolean(),
                    ])->space(2),
                ]),
                TextColumn::make('created_at')
                ->label('Data de Criação')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->hidden(true),
            TextColumn::make('updated_at')
                ->label('Data de Atualização')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->hidden(true),
            ])
            ->filters([
                Filter::make('Pago')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('has_been_paid', true)),
                Filter::make('Nao Pago')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('has_been_paid', false)),
                SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(TransactionType::class)
                    ->multiple()
                    ->preload(),
                Filter::make('amount')
                    ->form([
                        TextInput::make('from')
                            ->label('Valor De')
                            ->numeric(),
                        TextInput::make('to')
                            ->label('Valor Até')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                            $data['from'],
                            fn (Builder $query) => $query->where('amount', '>=', $data['from'])
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query) => $query->where('amount', '<=', $data['to'])
                            );
                    }),
                Filter::make('payment_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Data de Pagamento De'),
                        DatePicker::make('until')
                            ->label('Data de Pagamento Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
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
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }

}
