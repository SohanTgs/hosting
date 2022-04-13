<div class="col-md-3">
    <div class="vertical-menu">
        <span class="d-flex justify-content-between align-items-center"><b>@lang('Categories')
        </b> <i class="fa fa-list"></i>
        </span>
        @foreach($active_service_categories as $category) 
            <a href="{{ route('home') }}?category={{ $category->slug }}">
                {{ __($category->name) }}
            </a>
        @endforeach
    </div>
</div>

@push('style')
    <style>
        .vertical-menu {
            width: 100%;
        }
        
        .vertical-menu a, .vertical-menu span {
            background-color: #00000008;
            color: black;
            display: block;
            padding: 12px;
            text-decoration: none;
        }
        
        .vertical-menu a:hover {
            background-color: #ccc;
        }
        
        .vertical-menu a.active {
            background-color: #666;
            color: white; 
        }
        
        .fz-12{
            font-size: 12px;
        }
    </style>
@endpush