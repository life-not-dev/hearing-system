{{-- Usage: @include('components.test-results', ['testResults' => $testResults]) --}}

@foreach($testResults as $result)
    @if($result['service_type'] === 'Puretone Audiometry')
        <div style="margin-bottom:3in;">
            <div style="font-weight:bold; margin-bottom:10px;">Puretone Audiometry Test Results</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;">Ear</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Threshold (dB)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Left Ear</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_ear'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_remarks'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Right Ear</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_ear'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_remarks'] ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-bottom:12px;">Notes</div>
            <textarea class="form-control" style="width:100%; height:60px; margin-bottom:12px;">{{ $result['notes'] ?? '' }}</textarea>
        </div>
    @elseif($result['service_type'] === 'OAE')
        <div style="margin-bottom:3in;">
            <div style="font-weight:bold; margin-bottom:10px;">Otoacoustic Emission Test Results</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;">Ear</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Pass (✓)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Refer (✓)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Not tested (✓)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Left Ear</td>
                        <td style="border:1px solid #d1d5db; padding:6px; text-align:center;">{{ $result['left_pass'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px; text-align:center;">{{ $result['left_refer'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px; text-align:center;">{{ $result['left_not_tested'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_remarks'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Right Ear</td>
                        <td style="border:1px solid #d1d5db; padding:6px; text-align:center;">{{ $result['right_pass'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px; text-align:center;">{{ $result['right_refer'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px; text-align:center;">{{ $result['right_not_tested'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_remarks'] ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-bottom:12px;">Notes</div>
            <textarea class="form-control" style="width:100%; height:60px; margin-bottom:12px;">{{ $result['notes'] ?? '' }}</textarea>
        </div>
    @elseif($result['service_type'] === 'Speech Audiometry')
        <div style="margin-bottom:3in;">
            <div style="font-weight:bold; margin-bottom:10px;">Speech Audiometry</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;"></th>
                        <th style="border:1px solid #d1d5db; padding:6px;">SRT</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">SDS</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Right</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_srt'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_sds'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Left</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_srt'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_sds'] ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-bottom:12px;">Notes</div>
            <textarea class="form-control" style="width:100%; height:60px; margin-bottom:12px;">{{ $result['notes'] ?? '' }}</textarea>
            <button style="background:#e74c3c; color:#fff; font-weight:bold; border:none; border-radius:6px; padding:8px 18px; font-size:15px; float:right;">Download Print</button>
        </div>
    @elseif($result['service_type'] === 'Tympanometry')
        <div style="margin-bottom:3in;">
            <div style="font-weight:bold; margin-bottom:10px;">Tympanometry</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;"> </th>
                        <th style="border:1px solid #d1d5db; padding:6px;">TYPE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Right</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_type'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Left</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_type'] ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            <div style="margin-bottom:12px;">Notes</div>
            <textarea class="form-control" style="width:100%; height:60px; margin-bottom:12px;">{{ $result['notes'] ?? '' }}</textarea>
            <button style="background:#e74c3c; color:#fff; font-weight:bold; border:none; border-radius:6px; padding:8px 18px; font-size:15px; float:right;">Download Print</button>
        </div>
    @elseif($result['service_type'] === 'ASSR')
        <div style="margin-bottom:3in;">
            <div style="font-weight:bold; margin-bottom:10px;">Auditory Steady State Response</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;">EAR</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">500 Hz</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">1000 Hz</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">2000 Hz</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">4000 Hz</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">RIGHT</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_500'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_1000'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_2000'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['right_4000'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid #d1d5db; padding:6px;">Left</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_500'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_1000'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_2000'] ?? '' }}</td>
                        <td style="border:1px solid #d1d5db; padding:6px;">{{ $result['left_4000'] ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;">N</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Electr.</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">LFF, Hz</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">HFF, Hz</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">50 Hz</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Rejection μV</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Aver.</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Reject.</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Transducer</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Stimulus</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Noise</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">Emm</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">RN, nV</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Add rows as needed --}}
                </tbody>
            </table>
            <button style="background:#e74c3c; color:#fff; font-weight:bold; border:none; border-radius:6px; padding:8px 18px; font-size:15px; float:right;">Download Print</button>
        </div>
    @elseif($result['service_type'] === 'ABR')
        <div style="margin-bottom:3in;">
            <div style="font-weight:bold; margin-bottom:10px;">Auditory Brain Response</div>
            <div style="margin-bottom:8px;">Latencies & amplitudes (right ear)</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;">N</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">I (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">III (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">V (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">I-III (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">III-V (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">I-V (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">V-V(a) (μV)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Add rows as needed --}}
                </tbody>
            </table>
            <div style="margin-bottom:8px;">Latencies & amplitudes (left ear)</div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:12px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th style="border:1px solid #d1d5db; padding:6px;">N</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">I (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">III (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">V (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">I-III (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">III-V (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">I-V (ms)</th>
                        <th style="border:1px solid #d1d5db; padding:6px;">V-V(a) (μV)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Add rows as needed --}}
                </tbody>
            </table>
            <button style="background:#e74c3c; color:#fff; font-weight:bold; border:none; border-radius:6px; padding:8px 18px; font-size:15px; float:right;">Download Print</button>
        </div>
    @endif
@endforeach
