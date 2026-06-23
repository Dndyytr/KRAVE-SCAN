<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentMethodExport implements FromCollection, ShouldAutoSize, WithHeadings
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
            $item['method'] = strtoupper($row['method']);
            $item['total_transactions'] = $row['total_transactions'];
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
        $headings[] = 'Metode Pembayaran';
        $headings[] = 'Jumlah Transaksi Sukses';
        $headings[] = 'Total Pendapatan (IDR)';

        return $headings;
    }
}
