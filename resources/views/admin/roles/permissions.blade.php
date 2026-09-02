@extends('layouts.admin-layout')

@section('title', 'Permissions Matrix')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Permissions Matrix</h4>
            <p class="mb-0 text-muted">Comprehensive role privilege mapping across all 16 HRM application domains</p>
        </div>
        <a href="{{ route('admin::roles.index') }}" class="btn btn-label-secondary">
            <i class="icon-base ti tabler-arrow-left me-1"></i> Back to Roles
        </a>
    </div>

    <!-- Permissions Matrix Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 250px;">Domain / Permission Name</th>
                        @foreach($roles as $r)
                            <th class="text-center text-capitalize">{{ str_replace('_', ' ', $r->name) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $group => $perms)
                        <tr class="table-secondary">
                            <td colspan="{{ count($roles) + 1 }}" class="fw-bold text-uppercase small text-heading">
                                <i class="icon-base ti tabler-folder me-1"></i> {{ $group }} Module
                            </td>
                        </tr>
                        @foreach($perms as $p)
                            <tr>
                                <td>
                                    <span class="fw-medium text-heading small">{{ $p->name }}</span>
                                </td>
                                @foreach($roles as $r)
                                    <td class="text-center">
                                        @if($r->name === 'admin' || $r->hasPermissionTo($p->name))
                                            <i class="icon-base ti tabler-circle-check-filled text-success fs-5"></i>
                                        @else
                                            <i class="icon-base ti tabler-circle-x-filled text-muted opacity-25 fs-5"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
