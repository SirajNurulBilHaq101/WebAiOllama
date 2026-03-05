<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DeepSeek Coder Chat</title>

    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.14/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .msg-ai .markdown-body code:not(pre code) {
            background: oklch(var(--p) / 0.15);
            color: oklch(var(--pc));
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85em;
        }

        .msg-ai .markdown-body pre {
            margin: 0;
            padding: 14px;
            background: #0d1117;
            overflow-x: auto;
        }

        .msg-ai .markdown-body pre code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            line-height: 1.6;
        }

        .msg-ai .markdown-body p {
            margin-bottom: 0.5rem;
        }

        .msg-ai .markdown-body p:last-child {
            margin-bottom: 0;
        }

        .msg-ai .markdown-body ul,
        .msg-ai .markdown-body ol {
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .msg-ai .markdown-body blockquote {
            border-left: 3px solid oklch(var(--p));
            padding-left: 1rem;
            margin: 0.5rem 0;
            opacity: 0.8;
        }

        .msg-ai .markdown-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.5rem 0;
        }

        .msg-ai .markdown-body th,
        .msg-ai .markdown-body td {
            border: 1px solid oklch(var(--bc) / 0.15);
            padding: 6px 10px;
        }

        .msg-ai .markdown-body th {
            background: oklch(var(--b2));
        }

        .code-block-wrapper {
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid oklch(var(--bc) / 0.1);
            background: #0d1117;
            margin: 0.5rem 0;
        }

        .code-block-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px;
            background: oklch(var(--bc) / 0.05);
            border-bottom: 1px solid oklch(var(--bc) / 0.08);
            font-size: 0.75rem;
            opacity: 0.6;
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>

<body class="bg-base-300 h-screen">

    <div class="drawer lg:drawer-open h-full">
        <input id="sidebar-toggle" type="checkbox" class="drawer-toggle" />

        <!-- Main Content -->
        <div class="drawer-content flex flex-col h-screen">

            <!-- Navbar -->
            <div class="navbar bg-base-100 border-b border-base-content/5 px-4 min-h-14">
                <div class="flex-none lg:hidden">
                    <label for="sidebar-toggle" class="btn btn-ghost btn-sm btn-square">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </label>
                </div>
                <div class="flex-1 gap-2">
                    <div class="badge badge-primary badge-sm gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        deepseek-coder
                    </div>
                    <div class="badge badge-success badge-xs gap-1">
                        <div class="w-1.5 h-1.5 rounded-full bg-success animate-pulse"></div>
                        online
                    </div>
                </div>
                <div class="flex-none" id="navbar-title">
                    <span class="text-sm opacity-50">{{ $activeConversation?->title ?? 'New Chat' }}</span>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="messages" class="flex-1 overflow-y-auto p-4 space-y-4">

                @if ($activeConversation && count($messages) > 0)
                    @foreach ($messages as $msg)
                        @if ($msg->role === 'user')
                            <div class="chat chat-end">
                                <div class="chat-bubble chat-bubble-primary text-sm whitespace-pre-wrap">
                                    {{ $msg->content }}</div>
                            </div>
                        @else
                            <div class="chat chat-start">
                                <div class="chat-image avatar">
                                    <div class="w-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="chat-bubble bg-base-200 text-base-content msg-ai">
                                    <div class="markdown-body text-sm" data-raw="{{ e($msg->content) }}"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div id="welcome" class="flex items-center justify-center h-full">
                        <div class="text-center space-y-4 max-w-md">
                            <div class="avatar placeholder mx-auto">
                                <div class="bg-primary/10 text-primary rounded-xl w-16">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-xl font-semibold">DeepSeek Coder</h2>
                            <p class="text-sm opacity-50">Ask me anything about coding — writing, debugging, explaining,
                                and more.</p>
                            <div class="flex flex-wrap justify-center gap-2 pt-2">
                                <button onclick="sendQuick('Write a Python hello world program')"
                                    class="btn btn-outline btn-primary btn-sm">🐍 Python Hello World</button>
                                <button onclick="sendQuick('Explain async/await in JavaScript')"
                                    class="btn btn-outline btn-info btn-sm">⚡ Async/Await</button>
                                <button onclick="sendQuick('Write a SQL query to find duplicates')"
                                    class="btn btn-outline btn-success btn-sm">🗃️ SQL Query</button>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            <!-- Input Area -->
            <div class="p-4 border-t border-base-content/5 bg-base-100">
                <div class="join w-full">
                    <textarea id="msg" class="textarea textarea-bordered join-item flex-1 text-sm resize-none leading-relaxed"
                        placeholder="Ask me anything about code..." rows="1" style="max-height: 120px;" oninput="autoResize(this)"
                        onkeydown="handleKey(event)"></textarea>
                    <button id="sendBtn" onclick="send()" class="btn btn-primary join-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19V5m-7 7l7-7 7 7" />
                        </svg>
                    </button>
                </div>
                <p class="text-center text-xs opacity-30 mt-2">DeepSeek Coder may produce inaccurate info. Verify
                    important code.</p>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="drawer-side z-40">
            <label for="sidebar-toggle" class="drawer-overlay"></label>
            <div class="bg-base-200 w-72 h-full flex flex-col">

                <!-- Sidebar Header -->
                <div class="p-4 border-b border-base-content/5">
                    <a href="/" class="btn btn-primary btn-block btn-sm gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        New Chat
                    </a>
                </div>

                <!-- Conversation List -->
                <div class="flex-1 overflow-y-auto">
                    <ul class="menu menu-sm p-2 gap-1" id="conversation-list">
                        @forelse($conversations as $conv)
                            <li>
                                <a href="/chat/{{ $conv->id }}"
                                    class="flex justify-between items-center group {{ $activeConversation?->id === $conv->id ? 'active' : '' }}"
                                    data-id="{{ $conv->id }}">
                                    <span class="truncate flex-1 text-xs">{{ $conv->title }}</span>
                                    <button
                                        onclick="event.preventDefault(); event.stopPropagation(); deleteConversation({{ $conv->id }})"
                                        class="btn btn-ghost btn-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </a>
                            </li>
                        @empty
                            <li class="text-xs opacity-40 px-3 py-6 text-center">No conversations yet</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Sidebar Footer -->
                <div class="p-3 border-t border-base-content/5">
                    <div class="flex items-center gap-2 px-2">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-7">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-xs opacity-50">WebAI Ollama</span>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Marked + Highlight.js setup
        const renderer = new marked.Renderer();
        renderer.code = function(code, language) {
            if (typeof code === 'object') {
                language = code.lang || '';
                code = code.text || '';
            }
            const lang = language || 'code';
            let highlighted;
            try {
                highlighted = (language && hljs.getLanguage(language)) ?
                    hljs.highlight(code, {
                        language
                    }).value :
                    hljs.highlightAuto(code).value;
            } catch (e) {
                highlighted = code;
            }
            const escaped = code.replace(/'/g, "\\'").replace(/\n/g, "\\n");
            return `<div class="code-block-wrapper">
                <div class="code-block-header"><span>${lang}</span><button class="btn btn-ghost btn-xs text-xs" onclick="copyCode(this, '${escaped}')">📋 Copy</button></div>
                <pre><code class="hljs language-${lang}">${highlighted}</code></pre>
            </div>`;
        };
        marked.setOptions({
            renderer,
            breaks: true,
            gfm: true
        });

        // Render existing markdown messages from DB
        document.querySelectorAll('.markdown-body[data-raw]').forEach(el => {
            el.innerHTML = marked.parse(el.dataset.raw);
        });

        let isWaiting = false;
        let currentConversationId = {{ $activeConversation?->id ?? 'null' }};

        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        }

        function handleKey(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                send();
            }
        }

        function sendQuick(text) {
            document.getElementById('msg').value = text;
            send();
        }

        function scrollBottom() {
            const m = document.getElementById('messages');
            m.scrollTop = m.scrollHeight;
        }

        function escapeHtml(text) {
            const d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        async function send() {
            if (isWaiting) return;
            const input = document.getElementById('msg');
            const msg = input.value.trim();
            if (!msg) return;

            const messages = document.getElementById('messages');
            const sendBtn = document.getElementById('sendBtn');

            // Remove welcome
            document.getElementById('welcome')?.remove();

            // User bubble
            const userDiv = document.createElement('div');
            userDiv.className = 'chat chat-end';
            userDiv.innerHTML =
                `<div class="chat-bubble chat-bubble-primary text-sm whitespace-pre-wrap">${escapeHtml(msg)}</div>`;
            messages.appendChild(userDiv);

            input.value = '';
            input.style.height = 'auto';

            // Typing indicator
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat chat-start';
            typingDiv.id = 'typing';
            typingDiv.innerHTML = `
                <div class="chat-image avatar"><div class="w-8 rounded-lg bg-primary/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div></div>
                <div class="chat-bubble bg-base-200 text-base-content">
                    <span class="loading loading-dots loading-sm"></span>
                </div>`;
            messages.appendChild(typingDiv);
            scrollBottom();

            isWaiting = true;
            sendBtn.disabled = true;
            sendBtn.classList.add('btn-disabled');

            try {
                const res = await fetch("/chat/send", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: msg,
                        conversation_id: currentConversationId
                    })
                });

                document.getElementById('typing')?.remove();

                if (!res.ok) throw new Error(`Server error (${res.status})`);

                const data = await res.json();

                // Update conversation context
                if (!currentConversationId) {
                    currentConversationId = data.conversation_id;
                    // Update URL without reload
                    history.pushState(null, '', `/chat/${data.conversation_id}`);
                    // Add to sidebar
                    addToSidebar(data.conversation_id, data.conversation_title);
                    // Update navbar title
                    document.getElementById('navbar-title').innerHTML =
                        `<span class="text-sm opacity-50">${escapeHtml(data.conversation_title)}</span>`;
                }

                // Render AI response
                if (data.message?.content) {
                    appendAiMessage(data.message.content);
                } else {
                    appendAiMessage('*(Empty response)*');
                }
            } catch (error) {
                document.getElementById('typing')?.remove();
                appendAiMessage(`**Error:** ${error.message}`);
            }

            isWaiting = false;
            sendBtn.disabled = false;
            sendBtn.classList.remove('btn-disabled');
            scrollBottom();
        }

        function appendAiMessage(content) {
            const messages = document.getElementById('messages');
            const div = document.createElement('div');
            div.className = 'chat chat-start';
            div.innerHTML = `
                <div class="chat-image avatar"><div class="w-8 rounded-lg bg-primary/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                </div></div>
                <div class="chat-bubble bg-base-200 text-base-content msg-ai">
                    <div class="markdown-body text-sm">${marked.parse(content)}</div>
                </div>`;
            messages.appendChild(div);
            scrollBottom();
        }

        function addToSidebar(id, title) {
            const list = document.getElementById('conversation-list');
            // Remove "no conversations" placeholder
            const empty = list.querySelector('li.text-xs');
            if (empty) empty.remove();

            const li = document.createElement('li');
            li.innerHTML = `
                <a href="/chat/${id}" class="flex justify-between items-center group active" data-id="${id}">
                    <span class="truncate flex-1 text-xs">${escapeHtml(title)}</span>
                    <button onclick="event.preventDefault(); event.stopPropagation(); deleteConversation(${id})"
                            class="btn btn-ghost btn-xs opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </a>`;
            // Deactivate others
            list.querySelectorAll('a.active').forEach(a => a.classList.remove('active'));
            list.prepend(li);
        }

        async function deleteConversation(id) {
            if (!confirm('Delete this conversation?')) return;
            try {
                await fetch(`/chat/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                // If we're currently viewing this conversation, go home
                if (currentConversationId === id) {
                    window.location.href = '/';
                } else {
                    // Remove from sidebar
                    const link = document.querySelector(`a[data-id="${id}"]`);
                    if (link) link.closest('li').remove();
                }
            } catch (e) {
                alert('Failed to delete conversation');
            }
        }

        function copyCode(btn, code) {
            const decoded = code.replace(/\\n/g, "\n").replace(/\\'/g, "'");
            navigator.clipboard.writeText(decoded).then(() => {
                const original = btn.textContent;
                btn.textContent = '✅ Copied!';
                setTimeout(() => btn.textContent = original, 2000);
            });
        }

        // Scroll to bottom on page load if there are messages
        scrollBottom();
    </script>

</body>

</html>
