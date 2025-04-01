<?php

namespace SaferMobility\LaravelGotenberg\Enums;

enum Format: string
{
    case Letter = 'letter';
    case Legal = 'legal';
    case Tabloid = 'tabloid';
    case Ledger = 'ledger';
    case A0 = 'a0';
    case A1 = 'a1';
    case A2 = 'a2';
    case A3 = 'a3';
    case A4 = 'a4';
    case A5 = 'a5';
    case A6 = 'a6';

    public function pageSize(): array
    {
        return match ($this) {
            Format::Letter  => [8.5,   11, 'in'],
            Format::Legal   => [8.5,   14, 'in'],
            Format::Tabloid => [ 11,   17, 'in'],
            Format::Ledger  => [ 17,   11, 'in'],
            Format::A0      => [841, 1189, 'mm'],
            Format::A1      => [594,  841, 'mm'],
            Format::A2      => [420,  594, 'mm'],
            Format::A3      => [297,  420, 'mm'],
            Format::A4      => [210,  297, 'mm'],
            Format::A5      => [148,  210, 'mm'],
            Format::A6      => [105,  148, 'mm'],
        };
    }
}
