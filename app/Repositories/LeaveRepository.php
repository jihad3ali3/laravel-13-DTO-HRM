<?php

namespace App\Repositories;

use App\Domain\Leave\LeaveFilterDto;
use App\Domain\Leave\LeaveRequestProcessDto;
use App\Domain\Leave\LeaveRequestStoreDto;
use App\Exceptions\RepositoryException;
use App\Models\Leave;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LeaveRepository
{
    public function getTypes()
    {
        return Leave::where('is_active', true)->get();
    }

    public function getRequests(LeaveFilterDto $dto): LengthAwarePaginator
    {
        return LeaveRequest::query()
            ->with(['employee:id,first_name,last_name,employee_code', 'leave:id,name,code'])
            ->when($dto->employeeId, fn ($q, $id) => $q->where('employee_id', $id))
            ->when($dto->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate($dto->perPage);
    }

    /**
     * @throws RepositoryException
     */
    public function store(LeaveRequestStoreDto $dto): LeaveRequest
    {
        // 1. Calculate requested leave days
        $start = Carbon::parse($dto->startDate);
        $end = Carbon::parse($dto->endDate);
        $requestedDays = $start->diffInDays($end) + 1;

        // 2. Check balance
        $leave = Leave::find($dto->leaveId);
        if (! $leave) {
            throw new RepositoryException('Leave type not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        $balance = $this->getBalancesForType($dto->employeeId, $leave);
        if ($balance['remaining'] < $requestedDays) {
            throw new RepositoryException('Insufficient leave balance. Remaining: '.$balance['remaining'].' days. Requested: '.$requestedDays.' days.', ResponseAlias::HTTP_BAD_REQUEST);
        }

        // 3. Create leave request
        return LeaveRequest::create([
            'employee_id' => $dto->employeeId,
            'leave_id' => $dto->leaveId,
            'start_date' => $dto->startDate,
            'end_date' => $dto->endDate,
            'reason' => $dto->reason,
            'status' => LeaveRequest::STATUS_PENDING,
        ]);
    }

    /**
     * @throws RepositoryException
     */
    public function process(int $id, LeaveRequestProcessDto $dto): LeaveRequest
    {
        $request = LeaveRequest::find($id);
        if (! $request) {
            throw new RepositoryException('Leave request not found', ResponseAlias::HTTP_NOT_FOUND);
        }

        if ($request->status !== LeaveRequest::STATUS_PENDING) {
            throw new RepositoryException('Leave request is already processed.', ResponseAlias::HTTP_BAD_REQUEST);
        }

        $request->update([
            'status' => $dto->status,
            'reject_reason' => $dto->rejectReason,
            'actioned_by' => $dto->actionedBy,
            'actioned_at' => Carbon::now(),
        ]);

        return $request;
    }

    public function getBalances(int $employeeId): array
    {
        $leaveTypes = Leave::where('is_active', true)->get();
        $balances = [];

        foreach ($leaveTypes as $leave) {
            $balances[] = $this->getBalancesForType($employeeId, $leave);
        }

        return $balances;
    }

    private function getBalancesForType(int $employeeId, Leave $leave): array
    {
        $currentYear = Carbon::now()->year;

        // Find all approved leave requests for this employee and leave type in the current year
        $approvedRequests = LeaveRequest::where('employee_id', $employeeId)
            ->where('leave_id', $leave->id)
            ->where('status', LeaveRequest::STATUS_APPROVED)
            ->whereYear('start_date', $currentYear)
            ->get();

        $takenDays = 0;
        foreach ($approvedRequests as $req) {
            $start = Carbon::parse($req->start_date);
            $end = Carbon::parse($req->end_date);
            $takenDays += $start->diffInDays($end) + 1;
        }

        $maxDays = $leave->max_day_per_year;
        $remaining = max(0, $maxDays - $takenDays);

        return [
            'leave_type_id' => $leave->id,
            'leave_type_name' => $leave->name,
            'leave_type_code' => $leave->code,
            'max_days' => $maxDays,
            'taken' => $takenDays,
            'remaining' => $remaining,
        ];
    }
}
