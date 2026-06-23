<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesReportExport implements FromCollection, ShouldAutoSize, WithHeadings
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
            if (isset($row['branch'])) {
                $item['branch_name'] = $row['branch']['name'] ?? '-';
            }
            $item['date'] = $row['date'];
            $item['total_orders'] = $row['total_orders'];
            $item['total_revenue'] = number_format($row['total_revenue'], 2, '.', '');

            return $item;
        });
    }

    public function headings(): array
    {
        $headings = [];
        if (collect($this->data)->first() && isset(collect($this->data)->first()['branch'])) {
            $headings[] = 'Cabang';
        }
        $headings[] = 'Tanggal';
        $headings[] = 'Total Pesanan Selesai';
        $headings[] = 'Total Pendapatan (IDR)';

        return $headings;
    }
}
