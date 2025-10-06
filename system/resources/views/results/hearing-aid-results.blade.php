{{-- Hearing Aid Test Results Display (matching other test results UI) --}}
@php($rows = ($svcRecords['hearing'] ?? []))
@if(!empty($rows))
<div id="svc-hearing" class="mt-4">
  @foreach($rows as $r)
    <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
      <div class="card-body">
        <div class="text-center mb-2">
          <h5 class="mb-0" style="font-weight:700;">Hearing Aid Fitting</h5>
          <div class="text-muted" style="font-size:.9rem;">Date: {{ ($r['test_date'] ?? $r['date_issued'] ?? '') ? date('F j, Y', strtotime($r['test_date'] ?? $r['date_issued'] ?? '')) : '' }}</div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
              <tr>
                <th style="width:200px;">Brand</th>
                <th style="width:200px;">Model</th>
                <th style="width:150px;">Ear Side</th>
                <th style="width:150px;">Date Issued</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>{{ $r['brand'] ?? 'N/A' }}</td>
                <td>{{ $r['model'] ?? 'N/A' }}</td>
                <td>{{ $r['ear_side'] ?? 'N/A' }}</td>
                <td>{{ $r['test_date'] ?? $r['date_issued'] ?? 'N/A' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endforeach
</div>
@endif
