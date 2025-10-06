@extends('layouts.patient')

@section('title','View Test Result')

@push('head')
<style>
  .result-card { border:1px solid #cfcfcf; padding:18px; background:#fff; }
  .panel-title { background:#9ea4b1; padding:12px 18px; font-weight:700; color:#fff; }
  .oae-table { width:100%; border-collapse:collapse; }
  .oae-table th, .oae-table td { border:1px solid #ddd; padding:8px; }
  .oae-table th { background:#f5f5f5; }
  .notes { min-height:120px; border:1px solid #eee; padding:12px; background:#fafafa; }
  .container-wide { max-width:920px; margin:20px auto; }
</style>
@endpush

@section('content')
  <div class="container-wide">
    <div class="panel-title">View Test Result</div>
    <div style="padding:18px; border:1px solid #e6e6e6; background:#fff;">
      <div class="result-card">
        <h5>Otoacoustic Emission Test Results</h5>
        <table class="oae-table mt-3">
          <thead>
            <tr>
              <th>Ear</th>
              <th>Pass (✓)</th>
              <th>Refer (✓)</th>
              <th>Not tested (✓)</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Left Ear</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Right Ear</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>

        <div class="mt-4">
          <label class="form-label">Notes</label>
          <div class="notes"></div>
        </div>
      </div>
    </div>
  </div>
@endsection
