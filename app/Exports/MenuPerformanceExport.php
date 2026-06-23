<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MenuPerformanceExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($row) {
            $item = [];
            if (isset($row['branch_name'])) {
                $item['branch_name'] = $row['branch_name'];
            }
            $item['menu_name'] = $row['menu_name'];
            $item['category_name'] = $row['category_name'] ?? '-';
            $item['total_quantity'] = $row['total_quantity'];
            $item['total_revenue'] = number_format($row['total_revenue'], 2, '.', '');

            return $item;
        });
    }

    public function headings(): array
    {
        $headings = [];
        if (collect($this->data)->first() && isset(collect($this->data)->first()['branch_name'])) {
            $headings[] = 'Cabang';
        }
        $headings[] = 'Nama Menu';
        $headings[] = 'Kategori';
        $headings[] = 'Jumlah Terjual';
        $headings[] = 'Total Penjualan (IDR)';

        return $headings;
    }
}
