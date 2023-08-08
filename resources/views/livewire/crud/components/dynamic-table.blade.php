<div class="p-5">
    <div class="p-2">
        <div class="grid grid-cols-4 gap-2 items-center">
            <div class="col-span-2">
                <livewire:input-search />
            </div>
            <x-select label="Order"
                      :options="$orderByOptions"
                      option-label="name"
                      option-value="value"
                      wire:model="orderBy"
                      multiselect
            />
            <x-select label="Order"
                      :options="$orderDirectionOptions"
                      option-label="name"
                      option-value="value"
                      wire:model="orderByDirection"
            />
        </div>
    </div>
    <table class="w-full">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            @foreach($tableColumns as $tableColumn)
                @if($this->shouldShowColumnOnList($tableColumn->getColumnName()))
                    <th id="{{ $tableColumn->getColumnName() }}" scope="col" class="px-6 py-3"> {{ $tableColumn->getColumnDescription() }}</th>
                @endif
            @endforeach
            @if($modelClass::$useRecordActions)
                <th class="px-6 py-3 text-left">
                </th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($this->registers as $register)
            <tr class="even:bg-slate-300 odd:bg-white-100 text-center">
                {!!  $register->getTD() !!}
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $this->registers->links() }}
</div>

<script>
    async function showToastDelete(id) {
        const result = await Swal.fire({
            icon: "warning",
            text: "Are you shure?",
            html: "By accepting this the register will be removed from the system",
            showCancelButton: true,
        })

        if (result.isConfirmed) {
            let icon = "success"
            let text = "Register deleted succesfully"
            try {
                await @this.delete(id)
            } catch (ex) {
                icon = "error";
                text = "An error ocurred while deleting the register. Contact System Admin for more details."
            } finally {
                Swal.fire({
                    icon,
                    toast: true,
                    text,
                    position: "top-right"
                })
            }
        }
    }
    async function onEditClick(register) {
        Livewire.emitTo("crud", "onEditClick", register)
    }
</script>
