@extends('layouts.patient')

@section('title','Message')

@push('head')
<style>
  .pm-shell { display:flex; gap:28px; }
  .pm-contacts { width:360px; }
  .pm-contacts .card { border-radius:10px; }
  .pm-contacts .contacts-list { background:#f5f5f7; height:520px; border-radius:8px; padding:10px; overflow:auto; }
  .pm-contacts .contact-item { padding:10px; border-radius:8px; cursor:pointer; }
  .pm-contacts .contact-item:hover { background:#eef6ff; }
  .pm-contacts .avatar { width:48px; height:48px; border-radius:50%; background:#cfe8ff; display:inline-block; }

  .pm-conversation { width:420px; display:flex; flex-direction:column; }
  .pm-conversation .header { background:#9aa3b3; color:#fff; padding:12px 14px; border-top-right-radius:8px; border-top-left-radius:8px; font-weight:700; }
  .pm-conversation .messages { background:#fff; border:1px solid #dfe6ee; height:480px; overflow:auto; padding:14px; }
  .pm-conversation .composer { padding:12px; display:flex; gap:8px; align-items:center; }
  .pm-composer-input { flex:1; border-radius:24px; padding:10px 14px; border:1px solid #ddd; }
  .pm-send-btn { width:44px; height:44px; border-radius:22px; display:inline-flex; align-items:center; justify-content:center; background:#111; color:#fff; border:none; }

  .msg-in { text-align:left; margin-bottom:12px; }
  .msg-out { text-align:right; margin-bottom:12px; }
  .msg-bubble { display:inline-block; padding:10px 14px; border-radius:14px; max-width:78%; }
  .msg-in .msg-bubble { background:#f1f1f1; }
  .msg-out .msg-bubble { background:#d1e7dd; }

  /* small screens fallback */
  @media (max-width: 900px){ .pm-shell{ flex-direction:column; } .pm-contacts{ width:100%; } .pm-conversation{ width:100%; } }
</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <h4>Message</h4>
  </div>
</div>

<div class="pm-shell mt-3">
  <!-- Contacts column -->
  <div class="pm-contacts">
    <div class="card p-3">
      <div class="d-flex align-items-center mb-3">
        <div class="avatar"></div>
        <div style="margin-left:12px; font-weight:700;">User</div>
      </div>

      <div class="contacts-list rounded">
        <div class="contact-item d-flex align-items-center bg-white mb-2" data-id="{{ $staffUserId }}">
          <div class="avatar" style="width:40px; height:40px;"></div>
          <div style="margin-left:12px;">Staff</div>
        </div>
        <!-- more contacts could go here -->
      </div>
    </div>
  </div>

  <!-- Conversation column -->
  <form id="messageForm" action="{{ route('message.store') }}" method="post" autocomplete="off">
    @csrf
    <input type="hidden" name="patient_id" value="{{ auth()->user()->id }}">
    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
    <input type="hidden" name="sender_type" value="patient">
    <input type="hidden" name="receiver_id" id="receiver_id" value="">
    <input type="hidden" name="receiver_type" value="staff">
    <input type="hidden" name="appointment_id" value="">
    <input type="hidden" name="branch_id" value="">
    <div class="pm-conversation">
      <div class="header">Message Staff</div>
      <div class="messages" id="messagesArea">
        <div class="text-muted small">Select a contact on the left to start the conversation.</div>
      </div>
      <div class="composer border-top" style="background:#f8fafc; border-bottom-right-radius:8px; border-bottom-left-radius:8px;">
        <input id="messageInput" name="message_content" class="pm-composer-input" placeholder="Input message" autocomplete="off" />
        <button id="sendBtn" class="pm-send-btn" aria-label="Send" type="submit"><i class="fa fa-paper-plane"></i></button>
      </div>
    </div>
  </form>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
  const contacts = document.querySelectorAll('.contact-item');
  const messagesArea = document.getElementById('messagesArea');
  let current = null;

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

  function selectContact(el){
    const id = el.dataset.id;
    current = id;
    document.querySelectorAll('.contact-item').forEach(ci=>ci.classList.remove('bg-light'));
    el.classList.add('bg-light');
    document.getElementById('receiver_id').value = id;
    messagesArea.innerHTML = '';
    // Load previous messages via AJAX
    const patientId = {{ auth()->user()->id }};
    $.get('/messages/' + patientId + '/' + id, function(messages){
      if(messages.length === 0){
        messagesArea.innerHTML = '<div class="text-muted small">No messages yet.</div>';
      } else {
        messages.forEach(function(msg){
          if(msg.sender_type === 'patient'){
            messagesArea.appendChild(formatOutgoing(msg.message_content));
          } else {
            messagesArea.appendChild(formatIncoming(msg.message_content));
          }
        });
        messagesArea.scrollTop = messagesArea.scrollHeight;
      }
    });
  }

  contacts.forEach(c=> c.addEventListener('click', ()=> selectContact(c)));

  // AJAX form submit
  $('#messageForm').on('submit', function(e){
    e.preventDefault();
    if(!current){ alert('Select a contact first'); return; }
    const text = $('#messageInput').val().trim();
    if(!text) return;
    // Append outgoing message
    messagesArea.appendChild(formatOutgoing(text));
    messagesArea.scrollTop = messagesArea.scrollHeight;
    // Send to server
    $.ajax({
      url: $(this).attr('action'),
      method: 'POST',
      data: $(this).serialize(),
      headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
      success: function(response){
        // Optionally, append incoming message or update UI
        $('#messageInput').val('');
        // Simulate reply (replace with real data if available)
        setTimeout(()=>{
          messagesArea.appendChild(formatIncoming("Thanks — we'll get back to you shortly."));
          messagesArea.scrollTop = messagesArea.scrollHeight;
        }, 600);
      },
      error: function(xhr){
        alert('Failed to send message.');
      }
    });
  });

  // allow enter to send
  $('#messageInput').on('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); $('#sendBtn').click(); } });

})();
</script>
