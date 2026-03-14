<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Directory') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12" x-data="userDirectory({{ auth()->id() }})">

        <div class="fixed bottom-5 right-5 z-[100] flex flex-col gap-3">
            <template x-for="toast in toasts" :key="toast.id">
                <div x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 translate-x-8"
                    class="bg-white border-l-4 border-indigo-600 shadow-2xl rounded-lg p-4 min-w-[300px] flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center mb-1">
                            <span class="flex h-2 w-2 rounded-full bg-indigo-600 mr-2 border border-white"></span>
                            <p class="text-xs font-bold text-indigo-900 uppercase tracking-wider">
                                From: <span x-text="toast.sender"></span>
                            </p>
                        </div>
                        <p class="text-sm text-gray-700 font-medium leading-relaxed" x-text="toast.body"></p>
                    </div>
                    <button @click="toasts = toasts.filter(t => t.id !== toast.id)"
                        class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-0 sm:p-6">
                    <div
                        class="hidden md:grid grid-cols-12 bg-gray-50 border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="col-span-4">User</div>
                        <div class="col-span-5">Email</div>
                        <div class="col-span-3 text-right">Actions</div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                        <div
                            class="grid grid-cols-1 md:grid-cols-12 p-4 md:px-6 md:py-4 items-center hover:bg-gray-50 transition duration-150 {{ $user->unread_count > 0 ? 'bg-indigo-50/30' : '' }}">

                            <div class="col-span-1 md:col-span-4 flex items-center mb-2 md:mb-0">
                                <div class="relative h-10 w-10 shrink-0">
                                    <div
                                        class="h-full w-full rounded-full {{ $user->unread_count > 0 ? 'bg-indigo-600' : 'bg-indigo-500' }} flex items-center justify-center text-white font-bold shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @if($user->unread_count > 0)
                                    <span
                                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white border-2 border-white animate-pulse">
                                        {{ $user->unread_count }}
                                    </span>
                                    @endif
                                </div>

                                <div class="ml-4">
                                    <div
                                        class="text-sm {{ $user->unread_count > 0 ? 'font-bold text-indigo-900' : 'font-medium text-gray-900' }}">
                                        {{ $user->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 md:hidden truncate max-w-[200px]">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </div>

                            <div class="hidden md:block col-span-5 text-sm text-gray-600 truncate px-2">
                                {{ $user->email }}
                            </div>

                            <div class="col-span-1 md:col-span-3 text-left md:text-right mt-2 md:mt-0">
                                <button @click="openModal({{ $user->id }}, @js($user->name))"
                                    class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest transition ease-in-out duration-150 
                                                                                                    {{ $user->unread_count > 0
                            ? 'bg-indigo-600 text-white border-transparent hover:bg-indigo-700 active:bg-indigo-800'
                            : 'bg-indigo-50 text-indigo-700 border-transparent hover:bg-indigo-100 active:bg-indigo-200' 
                                                                                                    }} focus:outline-none focus:ring ring-indigo-300">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    {{ $user->unread_count > 0 ? __('New Messages') : __('Send Message') }}
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="p-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>

        <x-message-modal trigger="isOpen" />
    </div>
</x-app-layout>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userDirectory', (currentUserId) => ({
            isOpen: false,
            recipientName: '',
            recipientId: null,
            currentUserId: currentUserId,
            toasts: [],

            openModal(id, name) {
                this.recipientId = id;
                this.recipientName = name;
                this.isOpen = true;
                this.$dispatch('load-messages', {
                    recipientId: id
                });
            },

            notify(e) {
                const id = Date.now();
                this.toasts.push({
                    id: id,
                    body: e.body,
                    sender: e.sender_name
                });
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 5000);
            },

            init() {
                if (window.Echo) {
                    window.Echo.private(`App.Models.User.${this.currentUserId}`)
                        .listen('MessageSent', (e) => {
                            if (e.sender_id != this.currentUserId && (!this.isOpen || this.recipientId != e.sender_id)) {
                                this.notify(e);
                            }
                        });
                }
            }
        }));
    });
</script>