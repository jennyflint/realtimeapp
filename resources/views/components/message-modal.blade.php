@props(['trigger' => 'isOpen'])

<div x-show="{{ $trigger }}" x-data="chatComponent({ 
        storeUrl: '{{ route('messages.store') }}', 
        csrfToken: '{{ csrf_token() }}',
        currentUserId: {{ auth()->id() }}
     })" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>

    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-600 opacity-80" @click="{{ $trigger }} = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

            <div class="bg-white px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">
                    Chat with <span x-text="recipientName" class="text-indigo-700"></span>
                </h3>
                <button @click="{{ $trigger }} = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div x-ref="messageContainer" class="bg-slate-50 px-4 py-4 h-96 overflow-y-auto space-y-4 flex flex-col">

                <div x-show="loadingMessages" class="flex justify-center py-10">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>

                <div x-show="!loadingMessages && messages.length === 0"
                    class="text-center text-gray-500 py-10 text-sm italic">
                    No previous messages. Start the conversation!
                </div>

                <template x-for="msg in messages" :key="msg.id || Math.random()">
                    <div :class="msg.sender_id == currentUserId ? 'self-end items-end' : 'self-start items-start'"
                        class="max-w-[85%] flex flex-col">
                        <div  :class="msg.sender_id == currentUserId 
                             ? 'bg-indigo-600 p-4 shadow-md rounded-l-2xl rounded-tr-2xl text-black' 
                             : 'bg-white border p-4  border-gray-200  shadow-sm rounded-r-2xl rounded-tl-2xl text-black'"
                            class="inline-block p-3 px-4 text-sm leading-relaxed">
                            <p x-text="msg.body" class="whitespace-pre-wrap text-black"></p>
                        </div>
                        <div class="text-[10px] text-gray-500 mt-1 font-medium px-1"
                            x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})">
                        </div>
                    </div>
                </template>
            </div>

            <form @submit.prevent="send" class="p-4 bg-white border-t border-gray-200">
                <div class="flex items-end space-x-3">
                    <textarea x-model="newMessage" @keydown.enter.prevent="send" rows="1"
                        class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm text-sm p-3 resize-none"
                        placeholder="Write a message..." :disabled="sending"></textarea>

                    <button type="submit" :disabled="sending || !newMessage.trim()"
                        class="inline-flex items-center justify-center h-11 w-11 bg-indigo-600 rounded-full text-white shadow-lg hover:bg-indigo-700 active:bg-indigo-800 disabled:opacity-50 transition-all transform active:scale-95">
                        <svg class="h-5 w-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chatComponent', (config) => ({
            messages: [],
            newMessage: '',
            loadingMessages: false,
            sending: false,
            recipientId: null,
            init() {
                window.addEventListener('load-messages', (e) => {
                    this.recipientId = e.detail.recipientId;
                    this.fetchMessages(this.recipientId);
                });
            },

            fetchMessages(id) {
                this.loadingMessages = true;
                this.messages = [];

                fetch(`/message/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        this.messages = data.messages.reverse();
                        this.loadingMessages = false;
                        this.scrollToBottom();
                    });
            },

            send() {
                if (!this.newMessage.trim() || !this.recipientId) return;
                this.sending = true;

                fetch(config.storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient_id: this.recipientId,
                        message: this.newMessage
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        this.messages.push({
                            body: this.newMessage,
                            sender_id: config.currentUserId,
                            created_at: new Date().toISOString()
                        });
                        this.newMessage = '';
                        this.sending = false;
                        this.scrollToBottom();
                    })
                    .catch(() => {
                        this.sending = false;
                        alert('Error sending message');
                    });
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const container = this.$refs.messageContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                });
            }
        }));
    });
</script>