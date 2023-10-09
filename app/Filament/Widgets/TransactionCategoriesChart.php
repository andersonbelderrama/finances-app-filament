<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TransactionCategoriesChart extends ChartWidget
{
    protected static ?string $heading = 'Transações por Categoria';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '400px';

    protected static ?string $pollingInterval = null;

    protected static ?array $options = [
        'scales' => [
            'y' => [
                'grid' => [
                    'display' => false,
                ],
                'ticks' => [
                    'display' => false,
                ]
            ],
            'x' => [
                'grid' => [
                    'display' => false,
                ],
                'ticks' => [
                    'display' => false,
                ]
            ],
        ],
        'plugins' => [
            'legend' => [
                'display' => true, // Exibe a legenda
                'position' => 'bottom', // Posição da legenda (pode ser 'top', 'bottom', 'left' ou 'right')
            ],



        ],
    ];

    protected function getData(): array
    {

        $data = $this->getTransactionPerCategory();

        return [
            'labels' => $data['labels'],
            'datasets' => [
                [
                    'data' => $data['quantidades'],
                    'backgroundColor' => $data['backgroundColors'],
                    'borderColor' => $data['backgroundColors'],
                    'hoverOffset' => 4,
                    'borderDash' => 10,
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getTransactionPerCategory(): array
    {

        $labels = [];
        $quantidades = [];
        $backgroundColors = [];


        // Mapeie classes do Tailwind CSS para valores de cores
        $tailwindColors = [
            'rgba(239, 68, 68, 1)',    // Red
            'rgba(255, 193, 7, 1)',    // Yellow
            'rgba(16, 185, 129, 1)',  // Green
            'rgba(75, 192, 192, 1)',  // Emerald
            'rgba(59, 130, 246, 1)',  // Blue
            'rgba(63, 81, 181, 1)',   // Indigo
            'rgba(156, 39, 176, 1)',  // Purple
            'rgba(233, 30, 99, 1)',   // Pink
            'rgba(75, 85, 99, 1)',    // Gray
            'rgba(0, 128, 128, 1)',   // Teal
            'rgba(0, 188, 212, 1)',   // Cyan
            'rgba(255, 152, 0, 1)',   // Orange
            'rgba(75, 192, 192, 1)',  // Emerald
            'rgba(153, 102, 255, 1)', // Violet
            'rgba(255, 99, 132, 1)',  // Maroon
        ];


        $result = Transaction::select('categories.name as category', DB::raw('COUNT(*) as quantidade'))
                            ->join('categories', 'transactions.category_id', '=', 'categories.id')
                            ->where('transactions.user_id', auth()->user()->id)
                            ->groupBy('categories.name')
                            ->get()
                            ->toArray();

        // Índice para rastrear a próxima cor a ser usada
        $colorIndex = 0;

        foreach ($result as $item) {
            $labels[] = $item['category'];
            $quantidades[] = $item['quantidade'];
            // Use a próxima cor na sequência
            $backgroundColors[] = $tailwindColors[$colorIndex];
            // Avance para a próxima cor na sequência ou volte ao início
            $colorIndex = ($colorIndex + 1) % count($tailwindColors);
        }

        return [
            'labels' => $labels,
            'quantidades' => $quantidades,
            'backgroundColors' => $backgroundColors
        ];

    }

}
