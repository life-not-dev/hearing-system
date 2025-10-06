<!-- ABR Results (match form layout) -->
@php($rows = ($svcRecords['abr'] ?? []))
@if(!empty($rows))
<div id="svc-abr" class="mt-4">
    @foreach($rows as $r)
      <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="text-center mb-2">
            <h5 class="mb-0" style="font-weight:700;">Auditory Brain Response</h5>
            <div class="text-muted" style="font-size:.9rem;">Date: {{ $r['date_taken'] ? date('F j, Y', strtotime($r['date_taken'])) : '' }}</div>
          </div>
            <form method="POST" action="#" onsubmit="event.preventDefault(); deleteServiceResult('abr', {{ (int)($r['id'] ?? 0) }});">
              @csrf
              <!--<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>-->
            </form>
          </div>
          <h6 style="font-weight:600;">Latencies & amplitudes (right ear)</h6>
          <div class="table-responsive mb-2">
            <table class="table table-bordered align-middle abr-table">
              <thead class="table-light">
                <tr>
                  <th class="abr-col-n">N</th>
                  <th style="width:110px;">I (ms)</th>
                  <th style="width:100px;">III (ms)</th>
                  <th style="width:100px;">V (ms)</th>
                  <th style="width:100px;">I–III (ms)</th>
                  <th style="width:100px;">III–V (ms)</th>
                  <th style="width:100px;">I–V (ms)</th>
                  <th style="width:120px;">V–V(a) (µV)</th>
                </tr>
              </thead>
              <tbody>
                @php($rn = is_array($r['abr_rn'] ?? null) ? $r['abr_rn'] : [])
                @php($ri = is_array($r['abr_ri'] ?? null) ? $r['abr_ri'] : [])
                @php($r3 = is_array($r['abr_r3'] ?? null) ? $r['abr_r3'] : [])
                @php($rv = is_array($r['abr_rv'] ?? null) ? $r['abr_rv'] : [])
                @php($r13 = is_array($r['abr_r13'] ?? null) ? $r['abr_r13'] : [])
                @php($r35 = is_array($r['abr_r35'] ?? null) ? $r['abr_r35'] : [])
                @php($r15 = is_array($r['abr_r15'] ?? null) ? $r['abr_r15'] : [])
                @php($rvv = is_array($r['abr_rvv'] ?? null) ? $r['abr_rvv'] : [])
                @for($i=0;$i<max(count($rn),3);$i++)
                  <tr>
                    <td class="abr-col-n">{{ $rn[$i] ?? '' }}</td>
                    <td>{{ $ri[$i] ?? '' }}</td>
                    <td>{{ $r3[$i] ?? '' }}</td>
                    <td>{{ $rv[$i] ?? '' }}</td>
                    <td>{{ $r13[$i] ?? '' }}</td>
                    <td>{{ $r35[$i] ?? '' }}</td>
                    <td>{{ $r15[$i] ?? '' }}</td>
                    <td>{{ $rvv[$i] ?? '' }}</td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>

          <h6 style="font-weight:600;">Latencies & amplitudes (left ear)</h6>
          <div class="table-responsive mb-2">
            <table class="table table-bordered align-middle abr-table">
              <thead class="table-light">
                <tr>
                  <th class="abr-col-n">N</th>
                  <th style="width:110px;">I (ms)</th>
                  <th style="width:100px;">III (ms)</th>
                  <th style="width:100px;">V (ms)</th>
                  <th style="width:100px;">I–III (ms)</th>
                  <th style="width:100px;">III–V (ms)</th>
                  <th style="width:100px;">I–V (ms)</th>
                  <th style="width:120px;">V–V(a) (µV)</th>
                </tr>
              </thead>
              <tbody>
                @php($ln = is_array($r['abr_ln'] ?? null) ? $r['abr_ln'] : [])
                @php($li = is_array($r['abr_li'] ?? null) ? $r['abr_li'] : [])
                @php($l3 = is_array($r['abr_l3'] ?? null) ? $r['abr_l3'] : [])
                @php($lv = is_array($r['abr_lv'] ?? null) ? $r['abr_lv'] : [])
                @php($l13 = is_array($r['abr_l13'] ?? null) ? $r['abr_l13'] : [])
                @php($l35 = is_array($r['abr_l35'] ?? null) ? $r['abr_l35'] : [])
                @php($l15 = is_array($r['abr_l15'] ?? null) ? $r['abr_l15'] : [])
                @php($lvv = is_array($r['abr_lvv'] ?? null) ? $r['abr_lvv'] : [])
                @for($i=0;$i<max(count($ln),3);$i++)
                  <tr>
                    <td class="abr-col-n">{{ $ln[$i] ?? '' }}</td>
                    <td>{{ $li[$i] ?? '' }}</td>
                    <td>{{ $l3[$i] ?? '' }}</td>
                    <td>{{ $lv[$i] ?? '' }}</td>
                    <td>{{ $l13[$i] ?? '' }}</td>
                    <td>{{ $l35[$i] ?? '' }}</td>
                    <td>{{ $l15[$i] ?? '' }}</td>
                    <td>{{ $lvv[$i] ?? '' }}</td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>

          @if(!empty($r['abr_notes']))
            <div class="mt-2"><strong>Notes:</strong> {{ $r['abr_notes'] ?? '' }}</div>
          @endif
        </div>
      </div>
    @endforeach
  
</div>
@endif
