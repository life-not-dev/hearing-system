<!-- Speech Audiometry Results (match form layout) -->
@php($rows = ($svcRecords['speech'] ?? []))
@if(!empty($rows))
<div id="svc-speech" class="mt-4">
    @foreach($rows as $r)
      <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="text-center mb-2">
            <h5 class="mb-0" style="font-weight:700;">Speech Audiometry</h5>
            <div class="text-muted" style="font-size:.9rem;">Date: {{ $r['date_taken'] ? date('F j, Y', strtotime($r['date_taken'])) : '' }}</div>
          </div>
            <form method="POST" action="#" onsubmit="event.preventDefault(); deleteServiceResult('speech', {{ (int)($r['id'] ?? 0) }});">
              @csrf
              <!--<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>-->
            </form>
          </div>
          <div class="table-responsive mb-2">
            <table class="table table-bordered align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th style="width:140px;"></th>
                  <th>SRT</th>
                  <th>SDS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th class="text-center">Right</th>
                  <td>{{ $r['speech_right_srt'] ?? '' }}</td>
                  <td>{{ $r['speech_right_sds'] ?? '' }}</td>
                </tr>
                <tr>
                  <th class="text-center">Left</th>
                  <td>{{ $r['speech_left_srt'] ?? '' }}</td>
                  <td>{{ $r['speech_left_sds'] ?? '' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          @if(!empty($r['speech_notes']))
            <div class="mt-2"><strong>Notes:</strong> {{ $r['speech_notes'] ?? '' }}</div>
          @endif
        </div>
      </div>
    @endforeach
  
</div>
@endif
