<?php

namespace App\Domain\Setting;

class CompanySettingUpdateDto
{
    public function __construct(
        public array $settings
    ) {}
}
