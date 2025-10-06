@extends('layouts.staff')

@section('content')

<style>
  .chat-panel { background:#fff; border:1px solid #ddd; padding:18px; }
  .chat-top { display:flex; gap:18px; margin-bottom:12px; }
  .chat-container { display:flex; gap:18px; }
  .chat-list { width:340px; border:1px solid #ddd; padding:14px; border-radius:10px; background:#f5f5f5; }
  .chat-list .title { font-weight:800; margin-bottom:10px; }
  .chat-item { background:#fff; padding:12px; border-radius:6px; margin-bottom:10px; display:flex; gap:12px; align-items:center; border:1px solid #e6e6e6; }
  .chat-item img { width:36px; height:36px; border-radius:50%; }
  .chat-right { flex:1; border:1px solid #ddd; min-height:540px; border-radius:10px; padding:14px; position:relative; }
  .chat-message-input { position:absolute; bottom:12px; left:12px; right:12px; display:flex; gap:8px; align-items:center; }
  .chat-input { flex:1; padding:10px 14px; border-radius:20px; border:1px solid #ccc; }
  .send-btn { width:44px; height:44px; border-radius:22px; background:#0d6efd; color:#fff; display:flex; align-items:center; justify-content:center; border:none; }
  .avatar-row { display:flex; gap:12px; margin-bottom:12px; }
  .avatar-row .avatar { width:36px; height:36px; border-radius:50%; background:linear-gradient(90deg,#9ad4ff,#bfe9ff); display:flex; align-items:center; justify-content:center; cursor:pointer; background-image:url('/images/avatar.png'); background-size:cover; background-position:center; }
</style>

<div class="main-content">
  <div class="chat-panel">
    <div class="chat-top">
      <div style="flex:1"><h3>Message</h3></div>
    </div>

    <div class="chat-container">
      <div class="chat-list">
        <div class="avatar-row">
          <div class="avatar"></div>
          <div class="avatar"></div>
          <div class="avatar"></div>
          <div class="avatar"></div>
          <div class="avatar"></div>
        </div>
        <div class="title">My Chat List</div>
        <div id="chatListInner">
          <!-- Example chat items -->
          <div class="chat-item" data-id="1">
            <img src="/images/avatar.png" />
            <span>Patient 1</span>
          </div>
          <div class="chat-item" data-id="2">
            <img src="/images/avatar.png" />
            <span>Patient 2</span>
          </div>
          <!-- Add more patients as needed -->
        </div>
      </div>

      <div class="chat-right">
        <form id="staffMessageForm" action="{{ route('message.store') }}" method="post" autocomplete="off">
          @csrf
          <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
          <input type="hidden" name="sender_type" value="staff">
          <input type="hidden" name="receiver_id" id="receiver_id" value="">
          <input type="hidden" name="receiver_type" value="patient">
          <input type="hidden" name="patient_id" id="patient_id" value="">
          <input type="hidden" name="appointment_id" value="">
          <input type="hidden" name="branch_id" value="">
          <div id="chatWindow" style="padding-bottom:110px; min-height:400px;">
            <div class="text-muted small">Select a patient to start the conversation.</div>
          </div>
          <div class="chat-message-input">
            <input id="chatInput" name="message_content" class="chat-input" placeholder="Input message" autocomplete="off" />
            <button id="sendBtn" class="send-btn" type="submit">➤</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
  let currentPatient = null;
  const chatWindow = document.getElementById('chatWindow');

  function formatIncoming(text){
    const wrap = document.createElement('div');
    wrap.className = 'msg-in';
    wrap.innerHTML = `<div class="msg-bubble">${text}</div>`;
    return wrap;
  }

  function formatOutgoing(text){
    const wrap = document.createElement('div');
    wrap.className = 'msg-out';
    wrap.innerHTML = `<div class="msg-bubble">${text}</div>`;
    return wrap;
  }

  // Load messages for selected patient
  function loadMessages(patientId) {
    chatWindow.innerHTML = '';
    const staffId = {{ auth()->user()->id }};
    $.get('/messages/' + staffId + '/' + patientId, function(messages){
      if(messages.length === 0){
        chatWindow.innerHTML = '<div class="text-muted small">No messages yet.</div>';
      } else {
        messages.forEach(function(msg){
          if(msg.sender_type === 'staff'){
            chatWindow.appendChild(formatOutgoing(msg.message_content));
          } else {
            chatWindow.appendChild(formatIncoming(msg.message_content));
          }
        });
        chatWindow.scrollTop = chatWindow.scrollHeight;
      }
    });
  }

  // Handle patient selection
  $('#chatListInner').on('click', '.chat-item', function(){
    const patientId = $(this).data('id');
    currentPatient = patientId;
    $('#receiver_id').val(patientId);
    $('#patient_id').val(patientId);
    $('#chatListInner .chat-item').removeClass('bg-light');
    $(this).addClass('bg-light');
    loadMessages(patientId);
  });

  // Send message via AJAX
  $('#staffMessageForm').on('submit', function(e){
    e.preventDefault();
    if(!currentPatient){ alert('Select a patient first'); return; }
    const text = $('#chatInput').val().trim();
    if(!text) return;
    // Append outgoing message
    chatWindow.appendChild(formatOutgoing(text));
    chatWindow.scrollTop = chatWindow.scrollHeight;
    // Send to server
    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: $(this).serialize(),
      headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
      success: function(response){
        $('#chatInput').val('');
        // Optionally reload messages or append new incoming messages
      },
      error: function(xhr){
        alert('Failed to send message.');
      }
    });
  });

  // Allow enter to send
  $('#chatInput').on('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); $('#sendBtn').click(); } });

})();
</script>
@endpush
