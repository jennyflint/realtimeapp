<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Directory') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12" x-data="{ 
        isOpen: false, 
        recipientName: '', 
        recipientId: null,
        openModal(id, name) {
            this.recipientId = id;
            this.recipientName = name;
            this.isOpen = true;
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-0 sm:p-6">
                    
                    <div class="hidden md:grid grid-cols-12 bg-gray-50 border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="col-span-4">User</div>
                        <div class="col-span-5">Email</div>
                        <div class="col-span-3 text-right">Actions</div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <div class="grid grid-cols-1 md:grid-cols-12 p-4 md:px-6 md:py-4 items-center hover:bg-gray-50 transition duration-150">
                                
                                <div class="col-span-1 md:col-span-4 flex items-center mb-2 md:mb-0">
                                    <div class="h-10 w-10 shrink-0 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold md:font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 md:hidden truncate max-w-[200px]">{{ $user->email }}</div>
                                    </div>
                                </div>

                                <div class="hidden md:block col-span-5 text-sm text-gray-600 truncate px-2">
                                    {{ $user->email }}
                                </div>

                                <div class="col-span-1 md:col-span-3 text-left md:text-right mt-2 md:mt-0">
                                    <button @click="openModal({{ $user->id }}, @js($user->name))"
                                            class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 bg-indigo-50 border border-transparent rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 active:bg-indigo-200 focus:outline-none focus:border-indigo-300 focus:ring ring-indigo-300 transition ease-in-out duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 md:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                        Send Message
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