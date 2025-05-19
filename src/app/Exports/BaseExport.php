<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

abstract class BaseExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Summary of data
     * @var Collection
     */
    protected Collection $data;

    /**
     * Summary of __construct
     * @param \Illuminate\Support\Collection|array $data
     */
    public function __construct(Collection|array $data)
    {
        $this->data = collect($data);
    }

    /**
     * Summary of collection
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Summary of headings
     * @return void
     */
    abstract public function headings(): array;

    /**
     * Summary of map
     * @param mixed $row
     * @return void
     */
    abstract public function map($row): array;
}
