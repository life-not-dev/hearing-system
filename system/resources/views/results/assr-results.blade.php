<!-- ASSR Results (match form layout) -->
@php($rows = ($svcRecords['assr'] ?? []))
@if(!empty($rows))
<div id="svc-assr" class="mt-4">
    @foreach($rows as $r)
      <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="text-center mb-2">
            <h5 class="mb-0" style="font-weight:700;">Auditory Steady State Response</h5>
            <div class="text-muted" style="font-size:.9rem;">Date: {{ $r['date_taken'] ? date('F j, Y', strtotime($r['date_taken'])) : '' }}</div>
          </div>
            <form method="POST" action="#" onsubmit="event.preventDefault(); deleteServiceResult('assr', {{ (int)($r['id'] ?? 0) }});">
              @csrf
              <!--<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>-->
            </form>
          </div>
          <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle text-center assr-table">
              <thead class="table-light">
                <tr>
                  <th style="width:120px;">EAR</th>
                  <th>500 Hz</th>
                  <th>1000 Hz</th>
                  <th>2000 Hz</th>
                  <th>4000 Hz</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="fw-semibold">RIGHT</td>
                  <td>{{ $r['assr_r_500'] ?? '' }}</td>
                  <td>{{ $r['assr_r_1000'] ?? '' }}</td>
                  <td>{{ $r['assr_r_2000'] ?? '' }}</td>
                  <td>{{ $r['assr_r_4000'] ?? '' }}</td>
                </tr>
                <tr>
                  <td class="fw-semibold">Left</td>
                  <td>{{ $r['assr_l_500'] ?? '' }}</td>
                  <td>{{ $r['assr_l_1000'] ?? '' }}</td>
                  <td>{{ $r['assr_l_2000'] ?? '' }}</td>
                  <td>{{ $r['assr_l_4000'] ?? '' }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="table-responsive mb-2">
            <table class="table table-bordered align-middle text-center assr-table assr-grid">
              <thead class="table-light">
                <tr>
                  <th class="assr-col-n">N</th>
                  <th>Electr.</th>
                  <th>LFF, Hz</th>
                  <th>HFF, Hz</th>
                  <th>50 Hz</th>
                  <th>Rejection µV</th>
                  <th>Aver.</th>
                  <th>Reject.</th>
                  <th>Transducer</th>
                  <th>Stimulus</th>
                  <th>Noise</th>
                  <th>Emn</th>
                  <th>RN, nV</th>
                </tr>
              </thead>
              <tbody>
                @php($n = is_array($r['assr_n'] ?? null) ? $r['assr_n'] : [])
                @php($electr = is_array($r['assr_electr'] ?? null) ? $r['assr_electr'] : [])
                @php($lff = is_array($r['assr_lff'] ?? null) ? $r['assr_lff'] : [])
                @php($hff = is_array($r['assr_hff'] ?? null) ? $r['assr_hff'] : [])
                @php($hz50 = is_array($r['assr_50hz'] ?? null) ? $r['assr_50hz'] : [])
                @php($rej = is_array($r['assr_reject_uv'] ?? null) ? $r['assr_reject_uv'] : [])
                @php($aver = is_array($r['assr_aver'] ?? null) ? $r['assr_aver'] : [])
                @php($reject = is_array($r['assr_reject'] ?? null) ? $r['assr_reject'] : [])
                @php($trans = is_array($r['assr_transducer'] ?? null) ? $r['assr_transducer'] : [])
                @php($stim = is_array($r['assr_stimulus'] ?? null) ? $r['assr_stimulus'] : [])
                @php($noise = is_array($r['assr_noise'] ?? null) ? $r['assr_noise'] : [])
                @php($emn = is_array($r['assr_emn'] ?? null) ? $r['assr_emn'] : [])
                @php($rn = is_array($r['assr_rn_nv'] ?? null) ? $r['assr_rn_nv'] : [])
                @for($i=0;$i<max(count($n),6);$i++)
                  <tr>
                    <td class="assr-col-n">{{ $n[$i] ?? '' }}</td>
                    <td>{{ $electr[$i] ?? '' }}</td>
                    <td>{{ $lff[$i] ?? '' }}</td>
                    <td>{{ $hff[$i] ?? '' }}</td>
                    <td>{{ $hz50[$i] ?? '' }}</td>
                    <td>{{ $rej[$i] ?? '' }}</td>
                    <td>{{ $aver[$i] ?? '' }}</td>
                    <td>{{ $reject[$i] ?? '' }}</td>
                    <td>{{ $trans[$i] ?? '' }}</td>
                    <td>{{ $stim[$i] ?? '' }}</td>
                    <td>{{ $noise[$i] ?? '' }}</td>
                    <td>{{ $emn[$i] ?? '' }}</td>
                    <td>{{ $rn[$i] ?? '' }}</td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>

          @if(!empty($r['assr_notes']))
            <div class="mt-2"><strong>Notes:</strong> {{ $r['assr_notes'] ?? '' }}</div>
          @endif
        </div>
      </div>
    @endforeach
  
</div>
@endif
