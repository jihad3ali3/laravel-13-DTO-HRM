<?php

namespace App\Repositories;

use App\Domain\Announcement\AnnouncementFilterDto;
use App\Domain\Announcement\AnnouncementStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\Announcement;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AnnouncementRepository
{
    public function getAnnouncements(AnnouncementFilterDto $dto)
    {
        return Announcement::query()
            ->with(['department:id,name', 'creator:id,name'])
            ->when($dto->publishedOnly, fn ($q) => $q->where('is_published', true))
            ->when($dto->departmentId, function ($q) use ($dto) {
                $q->where(function ($sub) use ($dto) {
                    $sub->where('department_id', $dto->departmentId)
                        ->orWhereNull('department_id');
                });
            })
            ->latest('is_pinned')
            ->latest()
            ->get();
    }

    /**
     * @throws RepositoryException
     */
    public function show(int $id): Announcement
    {
        $announcement = Announcement::with(['department', 'creator'])->find($id);
        if (! $announcement) {
            throw new RepositoryException('Announcement not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        return $announcement;
    }

    public function store(AnnouncementStoreDto $dto): Announcement
    {
        return Announcement::create([
            'title' => $dto->title,
            'content' => $dto->content,
            'department_id' => $dto->departmentId,
            'priority' => $dto->priority,
            'is_pinned' => $dto->isPinned,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'is_published' => $dto->isPublished,
            'created_by' => $dto->createdBy,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function update(int $id, AnnouncementStoreDto $dto): Announcement
    {
        $announcement = Announcement::find($id);
        if (! $announcement) {
            throw new RepositoryException('Announcement not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $announcement->update([
            'title' => $dto->title,
            'content' => $dto->content,
            'department_id' => $dto->departmentId,
            'priority' => $dto->priority,
            'is_pinned' => $dto->isPinned,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'is_published' => $dto->isPublished,
        ]);

        return $announcement;
    }

    /**
     * @throws RepositoryException
     */
    public function destroy(int $id): void
    {
        $announcement = Announcement::find($id);
        if (! $announcement) {
            throw new RepositoryException('Announcement not found', ResponseAlias::HTTP_NOT_FOUND);
        }
        $announcement->delete();
    }
}
