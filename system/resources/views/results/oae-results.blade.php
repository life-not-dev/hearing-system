<!-- OAE Results (match form layout) -->
@php($rows = ($svcRecords['oae'] ?? []))
@if(!empty($rows))
<div id="svc-oae" class="mt-4">
    @foreach($rows as $r)
      <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="text-center mb-2">
            <h5 class="mb-0" style="font-weight:700;">Oto Acoustic with Emession</h5>
            <div class="text-muted" style="font-size:.9rem;">Date: {{ $r['date_taken'] ? date('F j, Y', strtotime($r['date_taken'])) : '' }}</div>
          </div>
            <form method="POST" action="#" onsubmit="event.preventDefault(); deleteServiceResult('oae', {{ (int)($r['id'] ?? 0) }});">
              @csrf
              <!--<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>-->
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle oae-table">
              <thead class="table-light">
                <tr>
                  <th style="width:140px;">Ear</th>
                  <th class="text-center" style="width:120px;">Pass (✓)</th>
                  <th class="text-center" style="width:120px;">Refer (✓)</th>
                  <th class="text-center" style="width:140px;">Not tested (✓)</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Left Ear</td>
                  <td class="text-center">{{ !empty($r['oae_left_pass']) ? '✓' : '' }}</td>
                  <td class="text-center">{{ !empty($r['oae_left_refer']) ? '✓' : '' }}</td>
                  <td class="text-center">{{ !empty($r['oae_left_not_tested']) ? '✓' : '' }}</td>
                  <td>{{ $r['oae_left_remarks'] ?? '' }}</td>
                </tr>
                <tr>
                  <td>Right Ear</td>
                  <td class="text-center">{{ !empty($r['oae_right_pass']) ? '✓' : '' }}</td>
                  <td class="text-center">{{ !empty($r['oae_right_refer']) ? '✓' : '' }}</td>
                  <td class="text-center">{{ !empty($r['oae_right_not_tested']) ? '✓' : '' }}</td>
                  <td>{{ $r['oae_right_remarks'] ?? '' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          @if(!empty($r['oae_notes']))
            <div class="mt-2"><strong>Notes:</strong> {{ $r['oae_notes'] ?? '' }}</div>
          @endif
        </div>
      </div>
    @endforeach
  
</div>
@endif
