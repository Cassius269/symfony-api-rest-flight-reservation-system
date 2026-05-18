<?php

namespace App\Dto;

use DateTime;


class CompanyCaptainResponseDto
{
    public ?string $company = null;
    public ?CaptainResponseDto $captain = null;
    public ?DateTime $startDate = null;
    public ?DateTime $endDate = null;
}
