<div class="p-5">
    <livewire:input-search wire:search="filterRegisters"/>
    <table class="w-full">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            @foreach($tableColumns as $tableColumn)
                <th id="{{ $tableColumn->getColumnName() }}" scope="col" class="px-6 py-3"> {{ $tableColumn->getColumnDescription() }}</th>
            @endforeach
            @if($modelClass::$useRecordActions)
                <th class="px-6 py-3 text-left">
                </th>
            @endif
        </tr>
        </thead>
        <tbody>
        @foreach($registers as $register)
            <tr class="even:bg-slate-300 odd:bg-white-100 text-center">
                {!!  $register->getTD() !!}
            </tr>
        @endforeach
        </tbody>
    </table>
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
</script>
