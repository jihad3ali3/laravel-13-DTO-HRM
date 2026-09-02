<!doctype html>

<html
    lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact "
    dir="ltr"
    data-skin="default"
    data-bs-theme="light"
    data-template="vertical-menu-template">
<head>
    @include('admin.partials.meta')
    <title>{{config('app.name')}} : @yield('title') </title>
    @include('admin.partials.styles.style')
    @include('admin.partials.js.helper-scripts')
</head>

<body>
<div class="layout-wrapper layout-content-navbar  ">
    <div class="layout-container">
        @include('admin.partials.sidebar')
        <div class="layout-page">
            @include('admin.partials.navbar')
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                    @include('admin.partials.alerts')
                    @yield('container')
                </div>
                @include('admin.partials.footer')
                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->
@include('admin.partials.js.scripts')
<!-- Page JS -->
@yield('page-script')
</body>
</html>
