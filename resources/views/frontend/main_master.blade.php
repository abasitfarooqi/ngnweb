<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-UK" lang="en-UK"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"><!--<![endif]-->

<head>



    @include('frontend.ngnstore.partials.head')
    <meta name="google-site-verification" content="GYDdkN0OXeZ5AyR5eYTbFas6nKto4yTZJBxOMHS6ys4" />
    @livewireStyles
    <script>
        window.addEventListener('livewire:init', () => {
            console.log('Livewire initialized');
        });
        window.addEventListener('livewire:available', () => {
            console.log('Livewire available');
        });
    </script>
    
    <style>
       
    </style>
</head>
<!-- /#site-header-wrap -->

<body class="header_sticky header-style-2 has-menu-extra" id="top">

    <!-- Preloader -->
    <div id="loading-overlay">
        <div class="loader"></div>
    </div>

    <!-- Boxed -->
    <div class="boxed">
        <div id="site-header-wrap">

            @include('frontend.ngnstore.partials.white-header')

        </div>

        <!-- End /#site-header-wrap -->

        <!-- Page Content -->

        @yield('content')

        <!-- End Page Content -->

        <!-- Newsletter -->



        <!-- End Newsletter -->

        <!-- Footer -->

        @include('frontend.ngnstore.partials.footer')

        <!-- End Footer -->

        <div id="ngn-chat-widget">
            <div id="ngn-chat-bar" onclick="ngnChatToggle()" aria-label="Open chat assistant" role="button">
                <span>Need help? Chat with our assistant.</span>
                <span id="ngn-chat-toggle">Open</span>
            </div>
            <div id="ngn-chat-panel">
                <div id="ngn-chat-messages">
                    <div class="ngn-chat-message ngn-chat-message-agent">
                        <span>Hello, how can I help you today?</span>
                    </div>
                </div>
                <div id="ngn-chat-status"></div>
                <form id="ngn-chat-form" onsubmit="return ngnChatSend(event);">
                    <div id="ngn-chat-input-row">
                        <input id="ngn-chat-input" type="text" placeholder="Type your question..." autocomplete="off" />
                        <button id="ngn-chat-send-btn" type="submit" class="ngn-btn bg-ngn ngn-bg" style="margin-bottom:0;">
                            Send
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div style="z-index: 2000;">
            <!-- WhatsApp Chat Button -->
            <div class="whatsapp-chat-button" style="position: fixed; bottom: 28px; right: 100px;padding:10px 8px;">
                <button onclick="openWhatsAppModal()" style="background-color: transparent; border: none;  ">
                    <i class="fa-brands fa-whatsapp" style="font-size: 37px; color: white;"></i>
                </button>
            </div>
            <!-- <div class="whatsapp-chat-button" style="position: fixed; bottom: 20px; right: 20px;padding:10px 8px;">
                <button onclick="openWhatsAppModal()" style="background-color: transparent; border: none;  ">
                    <i class="fa-brands fa-whatsapp" style="font-size: 37px; color: white;"></i>
                </button>
            </div> -->
            <!-- WhatsApp Modal -->
            <div id="whatsappModal"
                style="display: none; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;z-index: 1000;">
                <div
                    style="background: white; padding: 20px; border-radius: 10px; display: flex; flex-direction: column; align-items: center;">
                    <h3>Select a Branch:</h3>
                    <button onclick="redirectToWhatsApp('sutton')" class="ngn-btn ngn-btn-primary w-100 mt-2"
                        style="margin-bottom: 0;">Sutton</button>
                    <button onclick="redirectToWhatsApp('tooting')" class="ngn-btn ngn-btn-primary w-100 mt-2"
                        style="margin-bottom: 0;">Tooting</button>
                    <button onclick="redirectToWhatsApp('catford')" class="ngn-btn ngn-btn-primary w-100 mt-2"
                        style="margin-bottom: 0;">Catford</button>
                    <button onclick="closeWhatsAppModal()" class="ngn-btn ngn-btn-primary bg-ngn ngn-bg  mt-2"
                        style="margin-bottom: 0;">Close</button>
                </div>
            </div>

            <script>
                function openWhatsAppModal() {
                    document.getElementById('whatsappModal').style.display = 'flex';
                }

                function closeWhatsAppModal() {
                    document.getElementById('whatsappModal').style.display = 'none';
                }

                function redirectToWhatsApp(branch) {
                    var url = "";
                    switch (branch) {
                        case "sutton":
                            url =
                                "https://api.whatsapp.com/send/?phone=447946295530&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0";
                            break;
                        case "tooting":
                            url =
                                "https://api.whatsapp.com/send/?phone=447951790565&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0";
                            break;
                        case "catford":
                            url =
                                "https://api.whatsapp.com/send/?phone=447951790568&text=Hello+NGN%2C+I+would+like+to+inquire+about+your+services.&type=phone_number&app_absent=0";
                            break;
                    }
                    window.open(url, '_blank');
                    closeWhatsAppModal();
                }
            </script>
        </div>
        <!-- End WhatsApp Chat Button -->

        <!-- Go Top -->
        <a class="go-top" href="#top">
            <i class="fa fa-chevron-up"></i>
        </a>

    </div>
    <!-- Javascript -->
    @include('frontend.body.footer-scripts')
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            if (typeof window.Livewire !== 'undefined') {
                console.log('Livewire found');
                Livewire.on('save', () => {
                    console.log('Save event triggered');
                });
            } else {
                console.error('Livewire not found');
            }
        });
    </script>

    <script>
        (function() {
            var state = {
                open: false,
                sending: false,
                conversationId: null
            };

            function scrollMessagesToBottom() {
                var box = document.getElementById('ngn-chat-messages');
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
            }

            window.ngnChatToggle = function() {
                var panel = document.getElementById('ngn-chat-panel');
                var toggle = document.getElementById('ngn-chat-toggle');
                if (!panel || !toggle) {
                    return;
                }

                state.open = !state.open;
                panel.style.display = state.open ? 'block' : 'none';
                toggle.textContent = state.open ? 'Close' : 'Open';

                if (state.open) {
                    scrollMessagesToBottom();
                    var input = document.getElementById('ngn-chat-input');
                    if (input) {
                        input.focus();
                    }
                }
            };

            function setStatus(message) {
                var el = document.getElementById('ngn-chat-status');
                if (el) {
                    el.textContent = message || '';
                }
            }

            function appendMessage(text, from) {
                var box = document.getElementById('ngn-chat-messages');
                if (!box) {
                    return;
                }

                var wrapper = document.createElement('div');
                wrapper.className = 'ngn-chat-message ' + (from === 'user' ? 'ngn-chat-message-user' : 'ngn-chat-message-agent');

                var bubble = document.createElement('span');
                bubble.textContent = text;

                wrapper.appendChild(bubble);
                box.appendChild(wrapper);
                scrollMessagesToBottom();
            }

            window.ngnChatSend = function(event) {
                if (event) {
                    event.preventDefault();
                }

                if (state.sending) {
                    return false;
                }

                var input = document.getElementById('ngn-chat-input');
                if (!input) {
                    return false;
                }

                var message = input.value.trim();
                if (!message) {
                    return false;
                }

                appendMessage(message, 'user');
                input.value = '';
                state.sending = true;
                setStatus('Assistant is typing…');

                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

                fetch('{{ route('chat.agent.message') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message,
                        conversation_id: state.conversationId
                    })
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(function(data) {
                    state.sending = false;

                    if (data.conversation_id) {
                        state.conversationId = data.conversation_id;
                    }

                    if (data.reply) {
                        appendMessage(data.reply, 'agent');
                        setStatus('');
                        return;
                    }

                    if (data.error) {
                        setStatus(data.error);
                        return;
                    }

                    appendMessage('I am not sure how to respond to that at the moment.', 'agent');
                    setStatus('');
                })
                .catch(function() {
                    state.sending = false;
                    setStatus('The assistant is unavailable just now. Please try again in a moment.');
                });

                return false;
            };
        })();
    </script>
    
    <script async
  src="https://q3fdfxbbqy7r2fw5ezowvysq.agents.do-ai.run/static/chatbot/widget.js"
  data-agent-id="17983f58-d04f-11f0-b074-4e013e2ddde4"
  data-chatbot-id="dyoArWe9MFR0kKsM_wR-4hmEbTfRWmya"
  data-name="ngn-agent Chatbot"
  data-primary-color="#982426"
  data-secondary-color="#c00c0f"
  data-button-background-color="#982426"
  data-starting-message="Hello!, How can I help you today?"
  data-logo="/static/chatbot/icons/default-agent.svg">
</script>

</body>

</html>
