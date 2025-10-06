<!-- PTA Results (match form layout) -->
@php($rows = ($svcRecords['pta'] ?? []))
@if(!empty($rows))
<div id="svc-pta" class="mt-4">
  @php($freqs = [250,500,1000,1500,2000,3000,4000,6000,8000])
    @php($freqs = [250,500,1000,1500,2000,3000,4000,6000,8000])
    @foreach($rows as $r)
      @php(
        $ra = is_array($r['pta_right_ac'] ?? null) ? $r['pta_right_ac'] : []
      )
      @php($ram = is_array($r['pta_right_ac_masked'] ?? null) ? $r['pta_right_ac_masked'] : [])
      @php($rb = is_array($r['pta_right_bc'] ?? null) ? $r['pta_right_bc'] : [])
      @php($rbm = is_array($r['pta_right_bc_masked'] ?? null) ? $r['pta_right_bc_masked'] : [])
      @php($la = is_array($r['pta_left_ac'] ?? null) ? $r['pta_left_ac'] : [])
      @php($lam = is_array($r['pta_left_ac_masked'] ?? null) ? $r['pta_left_ac_masked'] : [])
      @php($lb = is_array($r['pta_left_bc'] ?? null) ? $r['pta_left_bc'] : [])
      @php($lbm = is_array($r['pta_left_bc_masked'] ?? null) ? $r['pta_left_bc_masked'] : [])
      <div class="card shadow-sm mb-3" data-id="{{ $r['id'] ?? '' }}" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="text-center mb-2">
            <h5 class="mb-0" style="font-weight:700;">Pure Tone Audiometry</h5>
            <div class="text-muted" style="font-size:.9rem;">Date: {{ $r['date_taken'] ? date('F j, Y', strtotime($r['date_taken'])) : '' }}</div>
          </div>
            <form method="POST" action="#" onsubmit="event.preventDefault(); deleteServiceResult('pta', {{ (int)($r['id'] ?? 0) }});">
              @csrf
              <!--<button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button>-->
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
              <thead class="table-light">
                <tr>
                  <th style="width:200px;">&nbsp;</th>
                  @foreach($freqs as $hz)
                    <th>{{ $hz }}<br><span class="text-muted">Hz</span></th>
                  @endforeach
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th>RIGHT AC</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $ra[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>MASKED</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $ram[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>RIGHT BC</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $rb[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>MASKED</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $rbm[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>LEFT AC</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $la[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>MASKED</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $lam[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>LEFT BC</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $lb[$i] ?? '' }}</td>
                  @endfor
                </tr>
                <tr>
                  <th>MASKED</th>
                  @for($i=0;$i<count($freqs);$i++)
                    <td>{{ $lbm[$i] ?? '' }}</td>
                  @endfor
                </tr>
              </tbody>
            </table>
          </div>
          @if(!empty($r['pta_notes']))
            <div class="mt-2"><strong>Notes:</strong> {{ $r['pta_notes'] ?? '' }}</div>
          @endif
        </div>
      </div>
    @endforeach
  
</div>
@endif
