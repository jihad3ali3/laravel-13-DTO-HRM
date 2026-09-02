<?php

namespace App\Http\Services;

use App\Domain\Announcement\AnnouncementFilterDto;
use App\Domain\Announcement\AnnouncementStoreDto;
use App\Repositories\AnnouncementRepository;

readonly class AnnouncementService
{
    public function __construct(private AnnouncementRepository $announcementRepository) {}

    public function getAnnouncements(AnnouncementFilterDto $dto)
    {
        return $this->announcementRepository->getAnnouncements($dto);
    }

    public function show(int $id)
    {
        return $this->announcementRepository->show($id);
    }

    public function store(AnnouncementStoreDto $dto)
    {
        return $this->announcementRepository->store($dto);
    }

    public function update(int $id, AnnouncementStoreDto $dto)
    {
        return $this->announcementRepository->update($id, $dto);
    }

    public function destroy(int $id): void
    {
        $this->announcementRepository->destroy($id);
    }
}
