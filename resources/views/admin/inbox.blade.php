<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        /* BASE LAYOUT */
        .inbox-card {
            height: calc(100vh - 165px);
            border-radius: 22px;
            border: 1px solid var(--border-color);
            border-top: 3.5px solid #2563eb !important;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            background: var(--bg-surface);
        }
        .contact-list {
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
            height: 100%;
            background: var(--bg-surface);
        }
        .chat-area {
            display: flex;
            flex-direction: column;
            height: 100%;
            background: var(--bg-surface);
            position: relative;
        }

        @media (max-width: 768px) {
            .inbox-card { height: calc(100vh - 120px); margin: -10px; border-radius: 0; }
            .contact-list { width: 100% !important; display: {{ request('user') ? 'none' : 'block' }}; }
            .chat-area { width: 100% !important; display: {{ request('user') ? 'flex' : 'none' }}; }
        }

        /* MESSENGER STYLE SIDEBAR */
        .contact-item {
            padding: 12px 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            color: var(--text-main);
            border-radius: 14px;
            margin: 4px 10px;
        }
        .contact-item:hover {
            background: var(--bg-subtle);
        }
        .contact-item.active {
            background: rgba(37, 99, 235, 0.1);
            border-left: 3px solid #2563eb;
        }
        .contact-item.active h6 {
            color: #2563eb !important;
        }

        .avatar-container { position: relative; display: inline-block; flex-shrink: 0; }
        .avatar-chat {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--bg-surface);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .status-dot {
            width: 13px;
            height: 13px;
            border-radius: 50%;
            border: 2px solid var(--bg-surface);
            position: absolute;
            bottom: 1px;
            right: 1px;
        }
        .status-online { background: #10b981; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }
        .status-offline { background: #94a3b8; }

        /* BUBBLES & ACTIONS LOGIC */
        .chat-history {
            flex-grow: 1;
            overflow-y: auto;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-subtle);
            background-image: radial-gradient(var(--border-color) 0.75px, transparent 0.75px);
            background-size: 20px 20px;
        }

        .msg-row { display: flex; align-items: center; margin-bottom: 4px; gap: 8px; width: 100%; }
        .msg-row.sent { flex-direction: row-reverse; }

        .msg-bubble {
            padding: 10px 16px;
            border-radius: 18px;
            max-width: 80%;
            font-size: 0.92rem;
            position: relative;
            line-height: 1.45;
            word-wrap: break-word;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
        }
        .sent .msg-bubble {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }
        .received .msg-bubble {
            background: var(--bg-surface);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
        }

        /* ACTIONS (React & Delete Icons) */
        .msg-actions { display: none; gap: 8px; color: var(--text-muted); font-size: 0.9rem; align-items: center; }
        .msg-row:hover .msg-actions { display: flex; }
        .action-btn { cursor: pointer; transition: 0.2s; padding: 3px; }
        .action-btn:hover { color: #2563eb; }
        .delete-btn:hover { color: #ef4444 !important; }

        /* REACTION BADGE */
        .reaction-badge {
            position: absolute;
            bottom: -10px;
            right: 10px;
            background: var(--bg-surface);
            border-radius: 12px;
            padding: 1px 6px;
            font-size: 0.8rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            border: 1px solid var(--border-color);
            z-index: 10;
            color: var(--text-main);
        }
        .received .reaction-badge { left: 10px; right: auto; }

        /* TIME & STATUS */
        .msg-info-row { display: flex; flex-direction: column; margin-bottom: 12px; }
        .msg-time { font-size: 0.65rem; color: var(--text-muted); }
        .msg-status { font-size: 0.62rem; font-weight: 700; }
        .sent .msg-info-row { align-items: flex-end; margin-right: 10px; }
        .received .msg-info-row { align-items: flex-start; margin-left: 10px; }

        /* MODALS & EMOJIS */
        #imageViewerModal, #reactionModal, #deleteConfirmModal, #deleteConvoModal { z-index: 9999 !important; }
        .emoji-option { font-size: 2rem; cursor: pointer; transition: 0.2s; padding: 5px; border-radius: 10px; text-decoration: none; }
        .emoji-option:hover { background: var(--bg-subtle); transform: scale(1.2); }

        /* EMOJI PICKER MENU */
        .emoji-insert-btn { font-size: 1.4rem; padding: 4px; cursor: pointer; transition: transform 0.1s; user-select: none; border-radius: 8px; }
        .emoji-insert-btn:hover { transform: scale(1.2); background: var(--bg-subtle); }
        .emoji-picker-container { max-height: 200px; overflow-y: auto; }

        /* INPUT AREA */
        #filePreviewSection {
            display: none;
            padding: 10px 16px;
            background: var(--bg-subtle);
            border-top: 1px solid var(--border-color);
            align-items: center;
            justify-content: space-between;
        }
        .preview-content { display: flex; align-items: center; gap: 10px; }
        .remove-file { cursor: pointer; color: #ef4444; font-size: 1.2rem; }

        #imagePreviewContainer { display: flex; gap: 5px; flex-wrap: wrap; }
        .preview-thumb { width: 42px; height: 42px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color); }

        .chat-input-container {
            padding: 14px 18px;
            background: var(--bg-surface);
            border-top: 1px solid var(--border-color);
        }
        .search-bar {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 9px 16px;
            font-size: 0.88rem;
            width: 100%;
            color: var(--text-main);
        }
        .search-bar:focus {
            background: var(--bg-surface);
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            color: var(--text-main);
        }
        
        .chat-img-clickable { 
            cursor: zoom-in; 
            transition: 0.3s; 
            border-radius: 12px; 
            max-width: 100%;
            width: 250px; 
            height: auto;
            margin: 2px 0; 
            object-fit: cover;
        }

        .chat-header-info { cursor: pointer; padding: 6px 12px; border-radius: 12px; transition: 0.2s; }
        .chat-header-info:hover { background: var(--bg-subtle); }

        @media (max-width: 768px) {
            .msg-row .msg-actions { display: flex !important; opacity: 0.8; font-size: 1rem; }
            .msg-actions { gap: 12px !important; }
            .msg-row.sent .msg-actions { margin-right: auto; }
            .msg-row.received .msg-actions { margin-left: auto; }
            .msg-bubble { max-width: 90%; }
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-3" style="border-radius: 15px;">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="card inbox-card">
                <div class="row g-0 h-100">

                    <div class="col-lg-4 col-md-5 contact-list">
                        <div class="p-3" style="border-bottom: 1px solid var(--border-color);">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <h5 class="fw-bold mb-0 d-none d-md-block" style="color: var(--text-main); letter-spacing: -0.3px;">Conversations</h5>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-0.5 rounded-pill fw-bold d-none d-md-inline-block mt-1" style="font-size: 9.5px; letter-spacing: 0.5px;">
                                        <i class="fas fa-shield-alt me-1"></i> SECURE COMMS HUB
                                    </span>
                                </div>
                                <span class="badge rounded-pill fw-semibold d-none d-md-inline-block" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); font-size: 0.72rem;">
                                    <i class="fas fa-satellite-dish me-1"></i> LIVE CHAT
                                </span>
                            </div>
                            <input type="text" id="contactSearchInput" class="form-control search-bar" placeholder="Search SecureLab...">
                        </div>

                        <div class="contacts-scroll py-2" id="contactsContainer">
                            @foreach($allMessages as $msg)
                                @php
                                    $isOnline = false; 
                                    $contactName = $msg->contact_name;
                                    $contactId = $msg->contact_id;
                                    $unreadCount = $msg->unread_count;

                                    $user = DB::table('users')->where('user_id', $contactId)->first();
                                    if($user) {
                                        $isOnline = !is_null($user->last_seen) && \Carbon\Carbon::parse($user->last_seen)->gt(now()->subMinutes(5));
                                    }
                                @endphp
                                <a href="?user={{ $contactId }}" class="contact-item {{ request('user') == $contactId ? 'active' : '' }}" data-name="{{ strtolower($contactName) }}" id="contact-sidebar-{{ $contactId }}">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="avatar-container">
                                            @if($user && $user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}" class="avatar-chat" alt="User Avatar">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($contactName) }}&background=random&color=fff" class="avatar-chat" alt="User Avatar">
                                            @endif
                                            <div class="status-dot {{ $isOnline ? 'status-online' : 'status-offline' }}"></div>
                                        </div>
                                        <div class="flex-grow-1 ms-3 overflow-hidden">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-bold small text-truncate" style="color: var(--text-main);">{{ $contactName }}</h6>
                                                @if($unreadCount > 0) <span class="badge bg-danger rounded-pill" id="badge-{{ $contactId }}">{{ $unreadCount }}</span> @endif
                                            </div>
                                            <p class="small mb-0 text-truncate sidebar-last-msg-text" style="color: var(--text-muted); font-size: 0.78rem;">
                                                @if($msg->last_message) 
                                                    {{ (Str::startsWith($msg->last_message, '[IMAGE]') ? 'Sent a photo' : (Str::startsWith($msg->last_message, '[FILE]') ? 'Sent a file' : $msg->last_message)) }} 
                                                @else 
                                                    Start a chat 
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach

                            {{-- Para sa mga users na wala pang chat history --}}
                            @php 
                                $activeChatIds = $allMessages->pluck('contact_id')->toArray();
                                $otherUsers = DB::table('users')->where('user_id', '!=', Auth::id())->whereNotIn('user_id', $activeChatIds)->get(); 
                            @endphp

                            @foreach($otherUsers as $user)
                                @php
                                    $isOnline = !is_null($user->last_seen) && \Carbon\Carbon::parse($user->last_seen)->gt(now()->subMinutes(5));
                                @endphp
                                <a href="?user={{ $user->user_id }}" class="contact-item {{ request('user') == $user->user_id ? 'active' : '' }}" data-name="{{ strtolower($user->full_name) }}" id="contact-sidebar-{{ $user->user_id }}">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="avatar-container">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}" class="avatar-chat" alt="User Avatar">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=random&color=fff" class="avatar-chat" alt="User Avatar">
                                            @endif
                                            <div class="status-dot {{ $isOnline ? 'status-online' : 'status-offline' }}"></div>
                                        </div>
                                        <div class="flex-grow-1 ms-3 overflow-hidden">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 fw-bold small text-truncate" style="color: var(--text-main);">{{ $user->full_name }}</h6>
                                            </div>
                                            <p class="small mb-0 text-truncate sidebar-last-msg-text" style="color: var(--text-muted); font-size: 0.78rem;">Start a chat</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-7 chat-area">
                        @php $selectedUser = request('user') ? DB::table('users')->where('user_id', request('user'))->first() : null; @endphp
                        @if($selectedUser)
                            @php
                                $selectedOnline = !is_null($selectedUser->last_seen) && \Carbon\Carbon::parse($selectedUser->last_seen)->gt(now()->subMinutes(5));
                            @endphp

                            <div class="p-2 p-md-3 d-flex align-items-center justify-content-between shadow-sm" style="background: var(--bg-surface); border-bottom: 1px solid var(--border-color);">
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('messages.index') }}" class="btn btn-sm d-md-none text-primary me-2"><i class="fas fa-chevron-left fs-4"></i></a>

                                    <a href="{{ route('profile.view', ['id' => $selectedUser->user_id]) }}" class="d-flex align-items-center chat-header-info text-decoration-none" title="View Full Profile">
                                        <div class="avatar-container me-2" style="width: 40px; height: 40px;">
                                            @if($selectedUser->profile_photo)
                                                <img src="{{ asset('storage/' . $selectedUser->profile_photo) }}" class="avatar-chat" style="width: 40px; height: 40px;" alt="Selected Avatar">
                                            @else
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedUser->full_name) }}&background=random&color=fff" class="avatar-chat" style="width: 40px; height: 40px;" alt="Selected Avatar">
                                            @endif
                                            <div class="status-dot {{ $selectedOnline ? 'status-online' : 'status-offline' }}" style="width: 12px; height: 12px;"></div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold small" style="color: var(--text-main);">{{ $selectedUser->full_name }} <i class="fas fa-external-link-alt text-muted ms-1" style="font-size: 10px;"></i></h6>
                                            <small class="{{ $selectedOnline ? 'text-success' : 'text-muted' }}" style="font-size: 0.68rem; font-weight: 600;">
                                                <i class="fas fa-circle me-1" style="font-size: 6px;"></i>{{ $selectedOnline ? 'Active Now' : 'Offline' }}
                                            </small>
                                        </div>
                                    </a>
                                </div>

                                <div>
                                    <button type="button" class="btn btn-sm rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteConvoModal" title="Hide Conversation" style="background: var(--bg-subtle); color: #ef4444; width: 36px; height: 36px;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="chat-history" id="chatHistoryBox">
                                @forelse($msgs ?? [] as $m)
                                    <div class="msg-row {{ $m->sender_id == Auth::id() ? 'sent' : 'received' }}" id="msg-{{ $m->message_id }}">
                                        <div class="msg-bubble">
                                            @if(Str::startsWith($m->body, '[IMAGE]:'))
                                                @php 
                                                    $imgUrl = asset('storage/' . str_replace('[IMAGE]:', '', $m->body)); 
                                                @endphp
                                                <img src="{{ $imgUrl }}" class="chat-img-clickable shadow-sm" data-url="{{ $imgUrl }}" alt="Chat Attachment">
                                            @elseif(Str::startsWith($m->body, '[FILE]:'))
                                                @php $fData = explode('|', str_replace('[FILE]:', '', $m->body)); @endphp
                                                <div class="p-1 text-center">
                                                    <i class="fas fa-file-alt fa-2x mb-2 text-primary"></i><br>
                                                    <small class="d-block mb-2 text-truncate fw-semibold" style="max-width: 180px; margin: 0 auto; color: var(--text-main);">{{ $fData[1] ?? 'Attachment' }}</small>
                                                    <a href="{{ asset('storage/' . $fData[0]) }}" download class="btn btn-sm w-100 rounded-pill shadow-sm fw-semibold" style="background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);">
                                                        <i class="fas fa-download me-1 text-primary"></i> Download
                                                    </a>
                                                </div>
                                            @else
                                                {{ $m->body }}
                                            @endif

                                            @if($m->reaction)
                                                <div class="reaction-badge">{{ $m->reaction }}</div>
                                            @endif
                                        </div>

                                        <div class="msg-actions">
                                            <i class="far fa-smile action-btn reaction-trigger" data-id="{{ $m->message_id }}"></i>
                                            @if($m->sender_id == Auth::id())
                                                <i class="far fa-trash-alt action-btn delete-btn delete-trigger" data-id="{{ $m->message_id }}"></i>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="msg-info-row {{ $m->sender_id == Auth::id() ? 'sent' : 'received' }}" id="info-{{ $m->message_id }}">
                                        <span class="msg-time">{{ \Carbon\Carbon::parse($m->sent_at)->format('h:i A') }}</span>
                                        @if($m->sender_id == Auth::id())
                                            <span class="msg-status {{ $m->is_read ? 'text-primary' : 'text-muted' }}">
                                                {{ $m->is_read ? 'Read' : 'Sent' }}
                                            </span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-4" id="emptyChatPlaceholder">
                                        <div class="stat-icon-wrapper mb-3" style="width: 70px; height: 70px; border-radius: 20px; background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 28px;">
                                            <i class="far fa-comments"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="color: var(--text-main);">No Messages Yet</h6>
                                        <p class="small mb-0" style="color: var(--text-muted); max-width: 280px;">Send a message to initialize an end-to-end conversation with {{ $selectedUser->full_name }}.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div id="filePreviewSection">
                                <div class="preview-content">
                                    <div id="imagePreviewContainer"></div>
                                    <i class="fas fa-file-alt fa-2x text-primary" id="docPreviewIcon" style="display:none;"></i>
                                    <span class="preview-text small fw-bold ms-2" id="previewFileName" style="color: var(--text-main);"></span>
                                </div>
                                <span class="remove-file" id="cancelUploadBtn">&times;</span>
                            </div>

                            <div class="chat-input-container">
                                <form id="secureLabInboxChatForm" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="receiver_id" id="receiverIdField" value="{{ $selectedUser->user_id }}">

                                    <input type="file" name="documents[]" id="docInput" multiple style="display:none">
                                    <input type="file" name="images[]" id="imgInput" accept="image/*" multiple style="display:none">

                                    <button type="button" class="btn text-primary p-0" id="triggerDocBtn" title="Attach Document"><i class="fas fa-plus-circle fs-5"></i></button>
                                    <button type="button" class="btn text-primary p-0" id="triggerImgBtn" title="Attach Image"><i class="fas fa-image fs-5"></i></button>

                                    <div class="dropup">
                                        <button type="button" class="btn text-primary p-0" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" title="Add Emoji">
                                            <i class="fas fa-smile fs-5"></i>
                                        </button>
                                        <div class="dropdown-menu p-2 shadow-lg border-0" style="width: 260px; border-radius: 18px; margin-bottom: 10px; background: var(--bg-surface); border: 1px solid var(--border-color);">
                                            <div class="d-flex flex-wrap gap-1 justify-content-center emoji-picker-container">
                                                <span class="emoji-insert-btn">😀</span><span class="emoji-insert-btn">😂</span>
                                                <span class="emoji-insert-btn">🤣</span><span class="emoji-insert-btn">😊</span>
                                                <span class="emoji-insert-btn">😍</span><span class="emoji-insert-btn">🥰</span>
                                                <span class="emoji-insert-btn">😘</span><span class="emoji-insert-btn">😭</span>
                                                <span class="emoji-insert-btn">😅</span><span class="emoji-insert-btn">🥺</span>
                                                <span class="emoji-insert-btn">😡</span><span class="emoji-insert-btn">😠</span>
                                                <span class="emoji-insert-btn">😱</span><span class="emoji-insert-btn">🥱</span>
                                                <span class="emoji-insert-btn">😴</span><span class="emoji-insert-btn">🙄</span>
                                                <span class="emoji-insert-btn">🤫</span><span class="emoji-insert-btn">🤥</span>
                                                <span class="emoji-insert-btn">👍</span><span class="emoji-insert-btn">👎</span>
                                                <span class="emoji-insert-btn">👏</span><span class="emoji-insert-btn">🙌</span>
                                                <span class="emoji-insert-btn">🤝</span><span class="emoji-insert-btn">🙏</span>
                                                <span class="emoji-insert-btn">✌️</span><span class="emoji-insert-btn">🤞</span>
                                                <span class="emoji-insert-btn">👋</span><span class="emoji-insert-btn">💪</span>
                                                <span class="emoji-insert-btn">❤️</span><span class="emoji-insert-btn">💔</span>
                                                <span class="emoji-insert-btn">💯</span><span class="emoji-insert-btn">✨</span>
                                                <span class="emoji-insert-btn">🔥</span><span class="emoji-insert-btn">🎉</span>
                                                <span class="emoji-insert-btn">🌟</span><span class="emoji-insert-btn">💡</span>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="text" name="message" id="messageInputField" class="form-control rounded-pill px-4 py-2" placeholder="Write a message..." autocomplete="off" data-gramm="false" data-gramm_editor="false" spellcheck="false" style="background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);" required>
                                    <button type="submit" class="btn text-primary p-0" title="Send Message"><i class="fas fa-paper-plane fs-4"></i></button>
                                </form>
                            </div>
                        @else
                            <div class="h-100 d-flex align-items-center justify-content-center text-center p-5">
                                <div>
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 36px; border: 2px dashed rgba(37, 99, 235, 0.3);">
                                        <i class="fas fa-satellite-dish"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1" style="color: var(--text-main);">Official Comms Terminal</h5>
                                    <div class="mb-3">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">
                                            <i class="fas fa-lock me-1"></i> END-TO-END SECURE CHANNEL
                                        </span>
                                    </div>
                                    <p class="small text-muted" style="max-width: 340px; margin: 0 auto;">Select any verified personnel or administrator from the sidebar to inspect telemetry, view audit communications, or dispatch direct notices.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageViewerModal" tabindex="-1" aria-hidden="true" style="background: rgba(0,0,0,0.85);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0 text-center">
                <div class="modal-header border-0 p-2 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <img id="fullViewImage" src="" class="img-fluid rounded shadow-lg mx-auto" style="max-height: 80vh;" alt="Fullscreen Content View">
                <div class="mt-3">
                    <a id="downloadImgBtn" href="" download class="btn btn-primary rounded-pill px-4 shadow">
                        <i class="fas fa-download me-2"></i> Download Image
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-body text-center p-3">
                    <input type="hidden" id="reactMsgId">
                    <div class="d-flex justify-content-around mb-2">
                        <span class="emoji-option" data-emoji="👍">👍</span>
                        <span class="emoji-option" data-emoji="❤️">❤️</span>
                        <span class="emoji-option" data-emoji="😂">😂</span>
                        <span class="emoji-option" data-emoji="😮">😮</span>
                        <span class="emoji-option" data-emoji="😢">😢</span>
                    </div>
                    <div class="border-top pt-2 mt-2" style="border-color: var(--border-color) !important;">
                        <button type="button" class="btn btn-light btn-sm w-100 rounded-pill emoji-option small" data-emoji="REMOVE_REACTION" style="background: var(--bg-subtle); color: var(--text-muted); border: 1px solid var(--border-color);">
                            <i class="fas fa-eraser me-1"></i> Remove Reaction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-body text-center p-4">
                    <input type="hidden" id="deleteMsgId">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-trash-alt fa-2x"></i>
                    </div>
                    <h6 class="fw-bold" style="color: var(--text-main);">Delete Message?</h6>
                    <p class="small" style="color: var(--text-muted);">This action cannot be undone and will delete this message for everyone.</p>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-light rounded-pill flex-grow-1 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger rounded-pill flex-grow-1 fw-semibold" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($selectedUser))
    <div class="modal fade" id="deleteConvoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-eye-slash fa-2x"></i>
                    </div>
                    <h6 class="fw-bold" style="color: var(--text-main);">Hide Conversation?</h6>
                    <p class="small" style="color: var(--text-muted);">This chat will be removed from your active list. The other user will still see it unless they hide it too.</p>
                    <form action="{{ route('messages.deleteConversation', ['user' => $selectedUser->user_id]) }}" method="POST">
                        @csrf
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-light rounded-pill flex-grow-1 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger rounded-pill flex-grow-1 fw-semibold">Remove</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('admin.components.footer')

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const activeUserId = '{{ request("user") ? (int)request("user") : "" }}';

            function escapeHtml(text) {
                if (!text) return '';
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.toString().replace(/[&<>"']/g, m => map[m]);
            }

            // 1. SCROLL TO BOTTOM LATEST CHAT & EVENT DELEGATION
            const chatHistoryBox = document.getElementById('chatHistoryBox'); 
            if(chatHistoryBox) {
                chatHistoryBox.scrollTop = chatHistoryBox.scrollHeight;

                chatHistoryBox.addEventListener('click', function(e) {
                    const reactBtn = e.target.closest('.reaction-trigger');
                    if (reactBtn) {
                        document.getElementById('reactMsgId').value = reactBtn.getAttribute('data-id');
                        new bootstrap.Modal(document.getElementById('reactionModal')).show();
                        return;
                    }

                    const deleteBtn = e.target.closest('.delete-trigger');
                    if (deleteBtn) {
                        document.getElementById('deleteMsgId').value = deleteBtn.getAttribute('data-id');
                        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
                        return;
                    }

                    const img = e.target.closest('.chat-img-clickable');
                    if (img) {
                        const url = img.getAttribute('data-url');
                        document.getElementById('fullViewImage').src = url;
                        document.getElementById('downloadImgBtn').href = url;
                        new bootstrap.Modal(document.getElementById('imageViewerModal')).show();
                        return;
                    }
                });
            }

            // 2. SEARCH CONTACTS
            document.getElementById('contactSearchInput')?.addEventListener('keyup', function() {
                let filter = this.value.toLowerCase();
                document.querySelectorAll('.contact-item').forEach(contact => {
                    if (contact.getAttribute('data-name').includes(filter)) {
                        contact.style.display = 'flex';
                    } else {
                        contact.style.display = 'none';
                    }
                });
            });

            // 3. REALTIME SIDEBAR CONTACTS & ACTIVE CHAT POLLING
            function updateSidebarContacts(recentMessages) {
                if (!recentMessages || !recentMessages.length) return;
                const contactsContainer = document.getElementById('contactsContainer');

                recentMessages.forEach(msg => {
                    const contactEl = document.getElementById('contact-sidebar-' + msg.contact_id);
                    if (contactEl) {
                        const lastMsgEl = contactEl.querySelector('.sidebar-last-msg-text');
                        if (lastMsgEl) {
                            let preview = msg.last_message || 'Start a chat';
                            if (msg.is_image) preview = 'Sent a photo';
                            else if (msg.is_file) preview = 'Sent a file';
                            else if (msg.preview_text) preview = msg.preview_text;
                            lastMsgEl.innerText = preview;
                        }

                        const titleRow = contactEl.querySelector('.d-flex.justify-content-between.align-items-center');
                        let badgeEl = document.getElementById('badge-' + msg.contact_id);

                        if (activeUserId && activeUserId == msg.contact_id) {
                            if (badgeEl) badgeEl.remove();
                        } else if (msg.unread_count > 0) {
                            if (!badgeEl && titleRow) {
                                badgeEl = document.createElement('span');
                                badgeEl.className = 'badge bg-danger rounded-pill';
                                badgeEl.id = 'badge-' + msg.contact_id;
                                titleRow.appendChild(badgeEl);
                            }
                            if (badgeEl) {
                                badgeEl.innerText = msg.unread_count;
                            }
                        } else {
                            if (badgeEl) badgeEl.remove();
                        }

                        if (contactsContainer && contactEl.previousElementSibling) {
                            contactsContainer.prepend(contactEl);
                        }
                    }
                });
            }

            function getLastMsgId() {
                const msgRows = document.querySelectorAll('#chatHistoryBox .msg-row');
                let maxId = 0;
                msgRows.forEach(row => {
                    const id = parseInt(row.id.replace('msg-', '')) || 0;
                    if (id > maxId) maxId = id;
                });
                return maxId;
            }

            let isThreadPolling = false;
            function pollActiveThread() {
                if (!activeUserId || isThreadPolling) return;
                isThreadPolling = true;
                const afterId = getLastMsgId();

                fetch(`/messages/thread/${activeUserId}?after=${afterId}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    const chatBox = document.getElementById('chatHistoryBox');
                    if (!chatBox) return;

                    if (data.is_online !== undefined) {
                        const statusDot = document.querySelector('.chat-header-info .status-dot');
                        const statusText = document.querySelector('.chat-header-info small');
                        if (statusDot) {
                            statusDot.className = 'status-dot ' + (data.is_online ? 'status-online' : 'status-offline');
                        }
                        if (statusText) {
                            statusText.className = (data.is_online ? 'text-success' : 'text-muted');
                            statusText.innerHTML = `<i class="fas fa-circle me-1" style="font-size: 6px;"></i>${data.is_online ? 'Active Now' : 'Offline'}`;
                        }
                    }

                    if (data.messages && data.messages.length > 0) {
                        const emptyPlaceholder = document.getElementById('emptyChatPlaceholder');
                        if (emptyPlaceholder) emptyPlaceholder.remove();

                        const hasIncoming = data.messages.some(m => !m.is_me);
                        if (hasIncoming && typeof window.playNotificationSound === 'function') {
                            window.playNotificationSound();
                        }
                        if (hasIncoming && document.hidden && typeof window.showSystemPopNotification === 'function') {
                            const lastIncoming = data.messages.filter(m => !m.is_me).slice(-1)[0];
                            if (lastIncoming) {
                                const senderName = document.querySelector('.chat-header-info h6')?.innerText?.trim() || 'New Message';
                                const preview = lastIncoming.is_image ? 'Sent a photo' : (lastIncoming.is_file ? 'Sent an attachment' : (lastIncoming.body || 'Sent you a message'));
                                window.showSystemPopNotification('💬 ' + senderName, preview, window.location.href);
                            }
                        }

                        let shouldScroll = false;
                        const isNearBottom = (chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < 180;

                        data.messages.forEach(m => {
                            if (document.getElementById('msg-' + m.message_id)) return;

                            let bodyHtml = '';
                            if (m.is_image && m.img_url) {
                                bodyHtml = `<img src="${m.img_url}" class="chat-img-clickable shadow-sm" data-url="${m.img_url}" alt="Chat Attachment">`;
                            } else if (m.is_file && m.file_url) {
                                bodyHtml = `
                                    <div class="p-1 text-center">
                                        <i class="fas fa-file-alt fa-2x mb-2 text-primary"></i><br>
                                        <small class="d-block mb-2 text-truncate fw-semibold" style="max-width: 180px; margin: 0 auto; color: var(--text-main);">${escapeHtml(m.file_name || 'Attachment')}</small>
                                        <a href="${m.file_url}" download class="btn btn-sm w-100 rounded-pill shadow-sm fw-semibold" style="background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);">
                                            <i class="fas fa-download me-1 text-primary"></i> Download
                                        </a>
                                    </div>`;
                            } else {
                                bodyHtml = escapeHtml(m.body);
                            }

                            const reactionBadge = m.reaction ? `<div class="reaction-badge">${escapeHtml(m.reaction)}</div>` : '';
                            const rowClass = m.is_me ? 'sent' : 'received';
                            const deleteBtn = m.is_me ? `<i class="far fa-trash-alt action-btn delete-btn delete-trigger" data-id="${m.message_id}"></i>` : '';

                            const rowDiv = document.createElement('div');
                            rowDiv.className = `msg-row ${rowClass}`;
                            rowDiv.id = `msg-${m.message_id}`;
                            rowDiv.innerHTML = `
                                <div class="msg-bubble">
                                    ${bodyHtml}
                                    ${reactionBadge}
                                </div>
                                <div class="msg-actions">
                                    <i class="far fa-smile action-btn reaction-trigger" data-id="${m.message_id}"></i>
                                    ${deleteBtn}
                                </div>
                            `;

                            const infoDiv = document.createElement('div');
                            infoDiv.className = `msg-info-row ${rowClass}`;
                            infoDiv.id = `info-${m.message_id}`;
                            infoDiv.innerHTML = `
                                <span class="msg-time">${m.time_formatted}</span>
                                ${m.is_me ? `<span class="msg-status ${m.is_read ? 'text-primary' : 'text-muted'}">${m.is_read ? 'Read' : 'Sent'}</span>` : ''}
                            `;

                            chatBox.appendChild(rowDiv);
                            chatBox.appendChild(infoDiv);
                            shouldScroll = true;
                        });

                        if (shouldScroll && isNearBottom) {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }

                        const activeBadge = document.getElementById('badge-' + activeUserId);
                        if (activeBadge) activeBadge.remove();
                    }

                    if (data.read_status) {
                        Object.keys(data.read_status).forEach(msgId => {
                            if (data.read_status[msgId] == 1) {
                                const statusEl = document.querySelector(`#info-${msgId} .msg-status`);
                                if (statusEl && statusEl.innerText.trim() === 'Sent') {
                                    statusEl.innerText = 'Read';
                                    statusEl.classList.remove('text-muted');
                                    statusEl.classList.add('text-primary');
                                }
                            }
                        });
                    }
                })
                .catch(err => console.error('Thread polling error:', err))
                .finally(() => {
                    isThreadPolling = false;
                });
            }

            let isUnreadPolling = false;
            function pollInboxSidebar() {
                if (isUnreadPolling) return;
                isUnreadPolling = true;

                fetch('/messages/unread-count', { 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success && data.recent_messages) {
                        updateSidebarContacts(data.recent_messages);
                    }
                })
                .catch(err => console.error('Sidebar polling error:', err))
                .finally(() => {
                    isUnreadPolling = false;
                });
            }

            if (activeUserId) {
                setInterval(pollActiveThread, 3000);
            }
            setInterval(pollInboxSidebar, 4000); 

            // 4. ATTACHMENT & PREVIEW LOGIC
            const chatForm = document.getElementById('secureLabInboxChatForm');
            if(chatForm) {
                const imgInput = document.getElementById('imgInput');
                const docInput = document.getElementById('docInput');
                const messageInputField = document.getElementById('messageInputField');
                const filePreviewSection = document.getElementById('filePreviewSection');
                const previewFileName = document.getElementById('previewFileName');

                const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                const docPreviewIcon = document.getElementById('docPreviewIcon');

                // EMOJI PICKER JS LOGIC
                document.querySelectorAll('.emoji-insert-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation(); 
                        const emoji = this.innerText;
                        messageInputField.value += emoji;
                        messageInputField.focus();
                    });
                });

                // UPLOAD & SEND MESSAGE LOGIC
                chatForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;

                    const formData = new FormData(this);

                    fetch('{{ route("messages.send") }}', { 
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData 
                    })
                    .then(async response => {
                        if(response.ok) {
                            location.reload();
                        } else {
                            let errData = await response.json();
                            alert(errData.message || 'Error sending message. The file might be too large or invalid.');
                            if (submitBtn) submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Upload Error:', error);
                        alert('A critical error occurred while sending your message. Please check file sizes and your network connection.');
                        if (submitBtn) submitBtn.disabled = false;
                    });
                });

                // FILE TRIGGERS
                document.getElementById('triggerImgBtn').addEventListener('click', () => imgInput.click());
                document.getElementById('triggerDocBtn').addEventListener('click', () => docInput.click());

                // IMAGE SELECTION PREVIEW 
                imgInput.addEventListener('change', function() {
                    if(this.files && this.files.length > 0) {
                        docInput.value = ''; 

                        filePreviewSection.style.display = 'flex';
                        docPreviewIcon.style.display = 'none';
                        imagePreviewContainer.innerHTML = ''; 

                        previewFileName.innerText = this.files.length + " image(s) selected";
                        messageInputField.removeAttribute('required');

                        Array.from(this.files).forEach(file => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'preview-thumb';
                                imagePreviewContainer.appendChild(img);
                            }
                            reader.readAsDataURL(file);
                        });
                    }
                });

                // MULTIPLE DOCUMENT PREVIEW
                docInput.addEventListener('change', function() {
                    if(this.files && this.files.length > 0) {
                        imgInput.value = ''; 

                        filePreviewSection.style.display = 'flex';
                        imagePreviewContainer.innerHTML = ''; 
                        docPreviewIcon.style.display = 'block';

                        previewFileName.innerText = this.files.length + " file(s) selected";
                        messageInputField.removeAttribute('required');
                    }
                });

                // CANCEL FILE UPLOAD
                document.getElementById('cancelUploadBtn').addEventListener('click', function() {
                    imgInput.value = '';
                    docInput.value = '';
                    filePreviewSection.style.display = 'none';
                    imagePreviewContainer.innerHTML = '';
                    docPreviewIcon.style.display = 'none';
                    previewFileName.innerText = '';
                    messageInputField.setAttribute('required', 'true');
                });
            }

            // HANDLE REACTION CLICK
            document.querySelectorAll('.emoji-option').forEach(btn => {
                btn.addEventListener('click', function() {
                    const emoji = this.getAttribute('data-emoji');
                    const msgId = document.getElementById('reactMsgId').value;
                    const finalEmoji = emoji === 'REMOVE_REACTION' ? null : emoji;

                    fetch(`/messages/react/${msgId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ reaction: finalEmoji })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success || data.status === 'success') {
                            location.reload(); 
                        } else {
                            alert('Failed to update reaction');
                        }
                    })
                    .catch(err => console.error(err));
                });
            });

            // HANDLE DELETE CONFIRM
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                const msgId = document.getElementById('deleteMsgId').value;
                const btn = this;
                btn.disabled = true;
                btn.innerText = 'Deleting...';

                fetch(`/messages/delete/${msgId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        const msgEl = document.getElementById(`msg-${msgId}`);
                        const infoEl = document.getElementById(`info-${msgId}`);
                        if(msgEl) msgEl.remove();
                        if(infoEl) infoEl.remove();
                        bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();
                    } else {
                        alert('Error deleting message.');
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = 'Delete';
                });
            });
        });
    </script>
</body>
</html>