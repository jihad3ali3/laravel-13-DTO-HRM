<?php

namespace App\Domain\Announcement;

class AnnouncementFilterDto
{
    public function __construct(
        public ?int $departmentId = null,
        public bool $publishedOnly = true
    ) {}
}
