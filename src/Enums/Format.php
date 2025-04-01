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
            Format::Letter  => ['width' => 8.5, 'height' =>   11, 'unit' => 'in'],
            Format::Legal   => ['width' => 8.5, 'height' =>   14, 'unit' => 'in'],
            Format::Tabloid => ['width' =>  11, 'height' =>   17, 'unit' => 'in'],
            Format::Ledger  => ['width' =>  17, 'height' =>   11, 'unit' => 'in'],
            Format::A0      => ['width' => 841, 'height' => 1189, 'unit' => 'mm'],
            Format::A1      => ['width' => 594, 'height' =>  841, 'unit' => 'mm'],
            Format::A2      => ['width' => 420, 'height' =>  594, 'unit' => 'mm'],
            Format::A3      => ['width' => 297, 'height' =>  420, 'unit' => 'mm'],
            Format::A4      => ['width' => 210, 'height' =>  297, 'unit' => 'mm'],
            Format::A5      => ['width' => 148, 'height' =>  210, 'unit' => 'mm'],
            Format::A6      => ['width' => 105, 'height' =>  148, 'unit' => 'mm'],
        };
    }
}
