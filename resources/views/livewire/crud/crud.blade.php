<div class="p-5">
    <div class="flex flex-row justify-between">
        <h1 class="text-xl"> {{ $routineTitle }}</h1>
        <x-button icon="plus" label="Add {{ $routineTitle }}" wire:click="toggleModalAddEdit()" primary lg/>
    </div>
    <div>
        @livewire('dynamic-table', ['modelClass' => $modelClass], key(Str::random()))
    </div>

    @if($modalAddEdit)
        <div id="defaultModal" tabindex="-1"  class="fixed top-0 left-0 right-0 z-50  w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex flex-row justify-center items-center bg-indigo-600 bg-opacity-50">
        <div class="relative w-full max-w-2xl max-h-full modal-edit">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <!-- Modal header -->
                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                       {{ $modalAddEditTitle  }}
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="defaultModal" onclick="closeModal()">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <form wire:submit.prevent="saveEdit">
                    <div class="p-6 space-y-6">
                            <x-errors />
                            <div class="flex flex-col gap-2 items-center">
                            @foreach($modelClass::getDtoColumnDefinitions() as $definition)
                                    <div class="mb-4">
                                        @if ($definition->getColumnName() === "password")
                                            <x-inputs.password
                                                :label="$definition->getColumnDescription()"
                                                wire:model="{{ $this->modelLower }}.{{ $definition->getColumnName() }}"
                                                initial-value=""
                                                right-icon="pencil"
                                                :disabled="!$definition->isAllowEdit()"
                                            />
                                        @else
                                            <x-input
                                                :label="$definition->getColumnDescription()"
                                                wire:model="{{ $this->modelLower }}.{{ $definition->getColumnName() }}"
                                                initial-value="{{ $this->{$this->modelLower} }}.{{ $definition->getColumnName() }} "
                                                right-icon="pencil"
                                                :disabled="!$definition->isAllowEdit()"
                                            />
                                        @endif

                                    </div>
                            @endforeach
                            </div>
                    </div>
                    <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button data-modal-hide="defaultModal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" > Confirm </button>
                        <button data-modal-hide="defaultModal" type="button" class="text-black bg-red-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-red-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10" wire:click="closeModalAddEdit()"> Decline </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function closeModal() {
        @this.closeModalAddEdit()
    }
    window.addEventListener('click', function (event) {
        if (event.target.id === "defaultModal") {
            @this.closeModalAddEdit()
        }
    })
    window.addEventListener('registerSaved', () => {
        Swal.fire({
            icon: "success",
            toast: true,
            text: "Register update successfully",
            position: "top-right"
        })
    })
</script>
