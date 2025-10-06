<!-- Tympanometry Results (match form layout) -->
@php($rows = ($svcRecords['tym'] ?? []))
@if(!empty($rows))
<div id="svc-tym" class="mt-4">
    @foreach($rows as $r)
      <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="text-center mb-2">
            <h5 class="mb-0" style="font-weight:700;">Tympanometry</h5>
            <div class="text-muted" style="font-size:.9rem;">Date: {{ $r['date_taken'] ? date('F j, Y', strtotime($r['date_taken'])) : '' }}</div>
          </div>
            <form method="POST" action="#" onsubmit="event.preventDefault(); deleteServiceResult('tym', {{ (int)($r['id'] ?? 0) }});">
              @csrf
              <!--<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>-->
            </form>
          </div>
          <div class="table-responsive mb-2">
            <table class="table table-bordered align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th style="width:160px;">&nbsp;</th>
                  <th>Type</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th>Right</th>
                  <td>{{ $r['tym_right_type'] ?? '' }}</td>
                </tr>
                <tr>
                  <th>Left</th>
                  <td>{{ $r['tym_left_type'] ?? '' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          @if(!empty($r['tym_notes']))
            <div class="mt-2"><strong>Notes:</strong> {{ $r['tym_notes'] ?? '' }}</div>
          @endif
        </div>
      </div>
    @endforeach
  
</div>
@endif
