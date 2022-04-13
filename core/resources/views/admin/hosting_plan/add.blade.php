@extends('admin.layouts.app')

@section('panel')

<div class="container mt-4" style="max-width: 600px">
    <h2 class="mb-5">Laravel Image Text Watermarking Example</h2>
    <form action="{{route('demo')}}" enctype="multipart/form-data" method="post">
        @csrf
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <strong>{{ $message }}</strong>
        </div>

        <div class="col-md-12 mb-3 text-center">
            <strong>Manipulated image:</strong><br />
            <img src="{{ asset('assets/demo') }}/{{ Session::get('fileName') }}" width="600px"/>
        </div>
        @endif
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="mb-3">
            <input type="file" name="file" class="form-control"  id="formFile">
        </div>
        <div class="d-grid mt-4">
            <button type="submit" name="submit" class="btn btn-primary">
                Upload File
            </button>
        </div>
    </form>
</div>

<div class="box">
    <div class="wrapper">
        <header>File Uploader</header>
        <form class="form-data" enctype="multipart/form-data" id="form">
            @csrf
            <input class="file-input form-control" type="file" name="file[]" id="file-input" multiple='multiple' style="background: transparent; border: none;" hidden>
            <i class="fas fa-cloud-upload-alt"></i>
            <p>Click Here to File Upload</p>
        </form>
        <section class="progress-area"></section>
        <section class="uploaded-area"></section>
    </div>
</div>
@endsection


@push('breadcrumb-plugins')
 
@endpush

@push('script') 
    <script>

    var form = $(".form-data");    ;
    fileInput = document.querySelector(".file-input"),
    progressArea = document.querySelector(".progress-area"),
    uploadedArea = document.querySelector(".uploaded-area");

    form.on('click', function(){
        fileInput.click(); 
    });

    form.on('change', function({ target }){
        let file = target.files;
        let fileType = null;
        fileName = null;

        for (x = 0; x < file.length; x++) {

            fileName = file[x].name;
            fileType = fileName.split('.').pop().toLowerCase();

            if(fileName.length >= 12){  
                let splitName = fileName.split('.');
                fileName = splitName[0].substring(0, 13) + "...." + splitName[1];
            }

            var getSize = file[x].size;

            // if((getSize / (1024 * 1024)) > '{{ $maxLimit }}'){ 
            //     return notify('info', 'Sorry, Your file size exceeded the limit');
            // }
 
            uploadFile(fileName, x, fileType);

            if(file.length - 1 == x){
                $("#form")[0].reset();
            }
        }
    });

    function uploadFile(name, index, fileType = null){

        var image = ['gif','png','jpg','jpeg','bmp'];
        var video = ['mp3','mp4'];
        var icon = null;

        if( image.includes(fileType) ){
            icon = `<i class="fas fa-image"></i>`;
        }
        else if( video.includes(fileType) ){
            icon = `<i class="fas fa-video"></i>`;
        }
        else{
            icon = `<i class="fas fa-file-alt"></i>`;
        }

        var random = null;
        var data = new FormData();
        var file = $("input[type='file']")[0].files[index];
        data.append('file', file);
        data.append('_token', "{{ csrf_token() }}");
 
        $.ajax({
            xhr: function() {
            var xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener("progress", ({ loaded, total }) => { 
                let fileLoaded = Math.floor((loaded / total) * 100); 
                let fileTotal = Math.floor(total / 1000); 
                let fileSize;
                
                (fileTotal < 1024) ? fileSize = fileTotal + " KB": fileSize = (loaded / (1024 * 1024)).toFixed(2) + " MB";
                
                    let progressHTML = `
                        <li class="row ml-0 mr-0">
                            <div class='col-md-2 p-0'>
                                ${icon}
                            </div>
                            <div class='col-md-10 p-0'>
                                <div class="content">
                                    <div class="details">
                                        <span class="name">${name} • Uploading</span>
                                        <span class="percent">${fileLoaded}%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress" style="width: ${fileLoaded}%"></div>
                                    </div>
                                </div>
                            <div>
                        </li>`;
          
                uploadedArea.classList.add("onpdatarogress");
                progressArea.innerHTML = progressHTML;

                if(loaded == total){
                    random = Math.random().toString(36).substr(2, 5);
                    progressArea.innerHTML = "";  
                    let uploadedHTML = `
                                <li class="row ${random}">
                                    <div class="content upload">
                                        ${icon}
                                        <div class="details">
                                            <span class="name">${name} • <span class='status'>Uploaded<span></span>
                                            <span class="size">${fileSize}</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-check"></i>
                                </li>`;
                    uploadedArea.classList.remove("onprogress");

                    uploadedArea.insertAdjacentHTML("afterbegin", uploadedHTML);
                }
            });

            return xhr;
            },
        
            type:'POST',
            url:'{{ route("admin.demo") }}',
            data: data,
            contentType: false,
            processData: false,
            cache: false,

            success: function(response){
                // console.log(response);
            },
            statusCode:{ 
                413: function(response) {
                    var element = $(`.${random}`);
                    var str = element.find('.status').text();
                    element.find('.status').text(str.replace('Uploaded', response.statusText)).css('color', 'red');
                    element.find('.fa-check').removeClass('fa-check').addClass('fa-times').css('color', 'red');
                }
            },

        });

    } 
    
    </script>
@endpush 

@push('style')
<style>

Style Sheet

body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

::selection {
    color: #fff;
}

.wrapper {
    max-width: 430px;
    background: #fff;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0px 0px 7px #b2d4d1;
}

.wrapper header {
    color: #6990F2;
    font-size: 27px;
    font-weight: 600;
    text-align: center;
}

.wrapper form {
    height: 167px;
    display: flex;
    cursor: pointer;
    margin: 30px 0;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    border-radius: 5px;
    border: 2px dashed #6990F2;
}

form :where(i, p) {
    color: #6990F2;
}

form i {
    font-size: 50px;
}

form p {
    margin-top: 15px;
    font-size: 16px;
}

section .row {
    margin-bottom: 10px;
    background: #E9F0FF;
    list-style: none;
    padding: 15px 20px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

section .row i {
    color: #6990F2;
    font-size: 30px;
}

section .details span {
    font-size: 14px;
}

.progress-area .row .content {
    width: 100%;
    /* margin-left: 15px; 847 */  
    margin-left: -10px;  
}

.progress-area .details {
    display: flex;
    align-items: center;
    margin-bottom: 7px;
    justify-content: space-between;
}

.progress-area .content .progress-bar {
    height: 6px;
    width: 100%;
    margin-bottom: 4px;
    background: #fff;
    border-radius: 30px;
}

.content .progress-bar .progress {
    height: 100%;
    width: 0%;
    background: #6990F2;
    border-radius: inherit;
}

.uploaded-area {
    max-height: 232px;
    overflow-y: scroll;
}

.uploaded-area.onprogress {
    max-height: 150px;
}

.uploaded-area::-webkit-scrollbar {
    width: 0px;
}

.uploaded-area .row .content {
    display: flex;
    align-items: center;
    margin-left: 12px; /* 847 */
}

.uploaded-area .row .details {
    display: flex;
    margin-left: 15px;
    flex-direction: column;
}

.uploaded-area .row .details .size {
    color: #404040;
    font-size: 11px;
}

.uploaded-area i.fa-check {
    font-size: 16px;
}

.uploaded-area i.fa-times {
    font-size: 16px;
}

.fa-check, .fa-times{
    margin-right: 20px;
}

.form-control:focus, .form-control:active, .form-control:visited, .form-control:focus-within, input:focus, input:active, input:visited, input:focus-within{
    border-color: none;
    box-shadow: none;
}
</style>
@endpush
