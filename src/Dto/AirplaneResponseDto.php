<?php

namespace App\Dto;

class AirplaneResponseDto
{
    public ?int $id = null;
    public ?string $reference = null;
    public ?string $airplaneModel = null;
    public ?CompanyResponseDto $company = null;
    public ?AirplaneModelResponseDto $model = null;
}
