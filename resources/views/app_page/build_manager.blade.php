@extends('layouts.app')

@section('title', trans('messages.previous_version'))
@section('content')
  <div class="container">
    @include('layouts.alert')
    <h3 class="text-center">{{ trans('messages.previous_version') }}</h3><br>
    <div class="text-right">
      <button class="btn btn-dark" id="deleteOldVerions">{{ trans('messages.delete_old_versions') }}</button>
      <button class="btn btn-primary" id="uploadNewVersionButton">{{ trans('messages.upload_new_version') }}</button>
      <button class="d-none" id="progressBarPopupButton" type="button" data-toggle="modal"
        data-target="#progressBarPopup"></button>
      <input type="file" hidden id="uploadNewVersionInput">
    </div>
    <br>
    <div class="row mb-4">
      <div class="col-lg-6 col-md-12">
        <div class="panel panel-default time-line">
          <h2 class="h4 text-gray-900 mb-4">{{ trans('messages.ios') }}</h2>
          @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('status') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif
        </div>
        <div class="member-list mb-4">
          @foreach ($iosBuilds as $iosBuild)
            <div class="member-content">
            <img src="" class="avatar-icon" alt="android_icon" onerror="this.onerror=null;this.src='@if (isset($iosBuild->app_icon)) {{ $iosBuild->app_icon }} @else
              {{ url(config('constants.DEFAULT_IMAGE_APP')) }} @endif';">
              <div class="mem-text">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h5>#{{ $iosBuild->build_number }}</h5>&nbsp;
                  <span>{{ $iosBuild->version_number }}</span>&nbsp;
                  <span>{{ $iosBuild->version_code_number ? '(' . $iosBuild->version_code_number . ')' : '' }}</span>
                </div>

                <span>{{ Carbon\Carbon::parse($iosBuild->build_date)->format('Y/m/d H:i:s') }}</span>
              </div>
              <span class="mem-text-right">
                <h4 class="delete-build-number"
                  data-route-delete="{{ route('apps.build_number.destroy', ['buildNumber' => $iosBuild->id]) }}"><i
                    class="fas fa-times"></i></h4>
              </span>
            </div>
          @endforeach
        </div>
      </div>
      <div class="col-lg-6 col-md-12 time-line">
        <div class="panel panel-default">
          <h2 class="h4 text-gray-900 mb-4">{{ trans('messages.android') }}</h2>
        </div>
        <div class="member-list mb-4">
          @foreach ($androidBuilds as $androidBuild)
            <div class="member-content">
            <img src="" class="avatar-icon" alt="android_icon" onerror="this.onerror=null;this.src='@if (isset($androidBuild->app_icon)) {{ $androidBuild->app_icon }} @else
              {{ url(config('constants.DEFAULT_IMAGE_APP')) }} @endif';">
              <div class="mem-text">
                <div class="d-flex justify-content-between align-items-baseline">
                  <h5>#{{ $androidBuild->build_number }}</h5>&nbsp;
                  <span>{{ $androidBuild->version_number }}</span>&nbsp;
                  <span>{{ $androidBuild->version_code_number ? '(' . $androidBuild->version_code_number . ')' : '' }}</span>
                </div>

                <span>{{ Carbon\Carbon::parse($androidBuild->build_date)->format('Y/m/d H:i:s') }}</span>
              </div>
              <span class="mem-text-right">
                <h4 class="delete-build-number"
                  data-route-delete="{{ route('apps.build_number.destroy', ['buildNumber' => $androidBuild->id]) }}"><i
                    class="fas fa-times"></i></h4>
              </span>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    <!-- Popup delete build number -->
    <div class="modal fade" id="deleteBuildNumber" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ trans('messages.delete_build_number_title') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            {{ trans('messages.delete_this_build_number') }}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.close') }}</button>
            <form action="" method="POST" id="frmDeleteBuildNumber">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-primary">{{ trans('messages.delete') }}</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup delete all old versions -->
    <div class="modal fade" id="modalDeleteOldVersions" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ trans('messages.delete_old_versions') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            {{ trans('messages.delete_old_versions') }} ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.close') }}</button>
            <form action="{{ route('apps.delete-old-versions', ['app' => request('app')]) }}" method="POST" id="frmDeleteOldVersion">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-primary">{{ trans('messages.delete') }}</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup progressing bar -->
    <div class="modal fade" id="progressBarPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ trans('messages.uploading_new_build') }}</h5>
          </div>
          <div class="modal-body">
            <div class="progress mt-3">
              <div class="progress-bar progress-bar-striped" role="progressbar"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button id="stop-progress-button" type="button" class="btn btn-danger"
              data-dismiss="modal">{{ trans('messages.cancel') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    $(document).on('click', '.delete-build-number', function() {
      const route = $(this).attr("data-route-delete");
      $("#frmDeleteBuildNumber").attr("action", route);
      $("#deleteBuildNumber").modal("toggle");
    });

    $(document).on('click', '#uploadNewVersionButton', function() {
      $('#uploadNewVersionInput').click();
    });

    var xhr = new window.XMLHttpRequest();
    $(document).on('change', '#uploadNewVersionInput', function() {
      $('#progressBarPopupButton').click()
      var file = $(this)[0].files[0];
      var extension = file.name.substr((file.name.lastIndexOf('.') + 1));
      var buildNumber = "";
      if (extension == "ipa") {
        buildNumber =
          '{{ $application->buildNumbers()->latestIosBuild()->first()
    ? $application->buildNumbers()->LatestIosBuild()->build_number + 1
    : 1 }}'
      } else if (extension == "apk") {
        buildNumber =
          '{{ $application->buildNumbers()->latestAndroidBuild()->first()
    ? $application->buildNumbers()->latestAndroidBuild()->build_number + 1
    : 1 }}'
      } else {
        swal("{{ trans('messages.error') }}", "{{ trans('messages.upload_new_build_failed') }}", "error");
      }

      var formData = new FormData();

      formData.append("file", file);
      formData.append("appName", '{{ $application->app_name }}');
      formData.append("buildNumber", buildNumber);

      $.ajax({
        type: "POST",
        url: '{{ route('api.builds.upload') }}',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
          xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable) {
              var percentComplete = Math.round(((evt.loaded / evt.total) * 100));
              $(".progress-bar").width(percentComplete + '%');
              $(".progress-bar").html(percentComplete + '%');
            }
          }, false);

          return xhr;
        },
        beforeSend: function() {
          $(".progress-bar").width('0%');
        }
      }).done(function(data) {
        swal("{{ trans('messages.success') }}", "{{ trans('messages.upload_new_build_successfully') }}",
            "success")
          .then(function() {
            location.reload();
          })
      }).catch(function(error) {
        swal("{{ trans('messages.error') }}", "{{ trans('messages.upload_new_build_failed') }}", "error")
          .then(function() {
            location.reload();
          })
      })
    });

    $(document).on('click', '#stop-progress-button', function(e) {
      xhr.abort();
    });

    $("#deleteOldVerions").click(function() {
        $("#modalDeleteOldVersions").modal("toggle");
    })

  </script>
@endsection
