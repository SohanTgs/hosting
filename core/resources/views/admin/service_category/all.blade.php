@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                        <tr>
                            <th>@lang('Service Category')</th>
                            <th>@lang('Slug')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td data-label="@lang('Service Category')">
                                        <span class="font-weight-bold">{{ __($category->name) }}</span>
                                    </td>

                                    <td data-label="@lang('Slug')">
                                        <span>{{ $category->slug }}</span>
                                    </td>

                                    <td data-label="@lang('Status')">
                                        @if($category->status == 1)
                                            <span class="badge badge--success">@lang('Enable')</span>
                                        @else 
                                            <span class="badge badge--danger">@lang('Disable')</span>
                                        @endif
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <button class="icon-btn editBtn" data-toggle="tooltip" data-original-title="@lang('Edit')" data-data="{{ $category }}">
                                            <i class="las la-edit text--shadow"></i>
                                        </button>
                                    </td> 
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            <div class="card-footer py-4">
                {{ paginateLinks($categories) }}
            </div>
        </div>
    </div>
</div>

{{-- NEW MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Add New Service Category')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> 
            <form class="form-horizontal" method="post" action="{{ route('admin.service.category.add') }}">
                @csrf 
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control add_name" name="name" placeholder="@lang('Name')" required value="{{old('name')}}" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <div class="justify-content-between d-flex">
                            <label>@lang('Slug')</label>
                            <div class="addSlugValidatation d-none">
                               <i>
                                    <span class="addSlugIcon"></span>
                                    <small class="addAjaxResponse">@lang('Validating')...</small>
                               </i>
                            </div>
                        </div>
                        <input type="text" class="form-control add_slug" name="slug" placeholder="@lang('Slug')" required value="{{old('slug')}}" oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')">
                        <small class="addUrl">{{ route('home') }}/<span></span></small>
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="short_description" class="form-control" required>{{old('short_description')}}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-save" value="add">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}} 
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="createModalLabel">@lang('Update Service Category')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="form-horizontal" method="post" action="{{ route('admin.service.category.update') }}">
                @csrf
                <input type="hidden" name="id" required>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input type="text" class="form-control edit_name" name="name" required required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <div class="justify-content-between d-flex">
                            <label>@lang('Slug')</label>
                            <div class="editSlugValidatation d-none">
                               <i>
                                    <span class="editSlugIcon"></span>
                                    <small class="editAjaxResponse">@lang('Validating')...</small>
                               </i>
                            </div>
                        </div>
                        <input type="text" class="form-control edit_slug" name="slug" required oninput="this.value = this.value.replace(/[^a-z0-9\s -]/gi, '')">
                        <small class="editUrl">{{ route('home') }}/<span></span></small>
                    </div>
                    <div class="form-group">
                        <label>@lang('Short Description')</label>
                        <textarea name="short_description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>@lang('Status')</label>
                        <input type="checkbox" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="status" checked>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary btn-block" id="btn-save" value="add">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn--primary box--shadow1 text-white text--small addBtn">
        <i class="fa fa-fw fa-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";  

            $('.addBtn').on('click', function () {
                var modal = $('#createModal');
                modal.modal('show');
            });

            $('.editBtn').on('click', function () {
                var modal = $('#editModal');
                $('.editSlugValidatation').addClass('d-none').removeClass('d-inline');   
                var record = $(this).data('data');

                modal.find('input[name=id]').val(record.id);
                modal.find('input[name=name]').val(record.name);
                modal.find('input[name=slug]').val(record.slug);
                modal.find('.editUrl span').text(record.slug);
                modal.find('textarea[name=short_description]').val(record.short_description);

                if(record.status == 1){
                    modal.find('input[name=status]').bootstrapToggle('on');
                }else{
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                modal.modal('show');
            });
 
            var slugRule = /^[0-9a-zA-Z -]+$/;

            function makeSlug(input){
                return input.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
            }

            $('.add_name').on('input', function(){
                var input = $(this).val();
                var slug = makeSlug(input);
        
                if(input.match(slugRule)){
                    $('.add_slug').val(slug);
                    return checkSlug(input, slug, 'add');
                }

                $('.addSlugValidatation').addClass('d-none').removeClass('d-inline');
            });

            $('.add_slug').on('input', function(){ 
                var input = $(this).val();
                var slug = makeSlug(input);

                if(input.match(slugRule)){
                    $(this).val(slug);
                    return checkSlug(input, slug, 'add');
                }

                $('.addSlugValidatation').addClass('d-none').removeClass('d-inline');
            });

            $('.edit_name').on('input', function(){
            
                var input = $(this).val();
                var slug = makeSlug(input);
                var id = $('#editModal').find('input[name=id]').val();

                if(input.match(slugRule)){
                    $('.edit_slug').val(slug);
                    return checkSlug(input, slug, 'edit', id);
                }

                $('.editSlugValidatation').addClass('d-none').removeClass('d-inline');
            }); 
 
            $('.edit_slug').on('input', function(){ 
                var input = $(this).val();
                var slug = makeSlug(input);
                var id = $('#editModal').find('input[name=id]').val();

                if(input.match(slugRule)){
                    $(this).val(slug);
                    return checkSlug(input, slug, 'edit', id);
                }
                
                $('.editSlugValidatation').addClass('d-none').removeClass('d-inline');
            });

            function checkSlug(input, slug, type, id = null){
                $.ajax({
                    type:'POST',
                    url:'{{ route("admin.check.slug") }}',
                    data: {
                        'input': input,
                        'id': id,
                        'model_type': 'service_category',
                        '_token': '{{ csrf_token() }}',
                    },

                    beforeSend: function() {
                        $(`.${type}SlugValidatation`).removeClass('d-none');
                        $(`.${type}SlugValidatation`).addClass('d-inline');
                        $(`.${type}SlugIcon`).html('<i class="fas fa-spinner fa-spin"></i>');
                        $(`.${type}AjaxResponse`).text('Validating...');
                    },

                    success:function(response){
                        setTimeout(function() {
                            if(response.error){
                                $.each(response.error, function(key, value) {
                                    notify('error', value);
                                });
                                $(`.${type}SlugValidatation`).addClass('d-none');
                                $(`.${type}SlugValidatation`).removeClass('d-inline');
                            }
                            else if(!response.success){
                                $(`.${type}Url span`).text(slug);
                                $(`.${type}SlugIcon`).html('<i class="fas fa-times"></i>');
                                return $(`.${type}AjaxResponse`).text(response.message);
                            }
                            else if(response.success){
                                $(`.${type}AjaxResponse`).text(response.message);
                                $(`.${type}SlugIcon`).html('<i class="fas fa-check"></i>');
                                return $(`.${type}Url span`).text(slug);
                            }
                        }, 300);
                    }
                });
            }
            
        })(jQuery);
    </script>
@endpush

@push('style')
<style>
    .addUrl span, .editUrl span{ 
        border-bottom: 1px dashed;
    }
    .addUrl, .editUrl{ 
        font-size: 15px;
    }
</style>
@endpush

 