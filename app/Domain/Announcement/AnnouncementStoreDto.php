<?php

namespace App\Domain\Announcement;

class AnnouncementStoreDto
{
    public function __construct(
        public string $title,
        public ?string $content,
        public ?int $departmentId,
        public string $priority,
        public bool $isPinned,
        public string $startDate,
        public ?string $endDate,
        public bool $isPublished,
        public ?int $createdBy = null
    ) {}
}
